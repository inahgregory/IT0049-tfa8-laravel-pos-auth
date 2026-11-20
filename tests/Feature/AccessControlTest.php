<?php
namespace Tests\Feature;
use App\Models\Customer;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;
class AccessControlTest extends TestCase
{
    use RefreshDatabase;

    public function test_all_resource_endpoints_redirect_guests_without_mutating_records(): void
    {
        $this->seed();
        foreach (['customers', 'users'] as $resource) {
            foreach ([
                ['GET', '/'.$resource], ['GET', '/'.$resource.'/create'],
                ['POST', '/'.$resource], ['GET', '/'.$resource.'/1'],
                ['GET', '/'.$resource.'/1/edit'], ['PUT', '/'.$resource.'/1'],
                ['PATCH', '/'.$resource.'/1'], ['DELETE', '/'.$resource.'/1'],
            ] as [$method, $url]) {
                $this->call($method, $url)->assertRedirect(route('login'));
            }
        }
        $this->assertDatabaseCount('customers', 5);
        $this->assertDatabaseCount('users', 5);
        $this->getJson('/customers')->assertUnauthorized();
    }

    public function test_both_resources_register_every_conventional_method_with_auth(): void
    {
        foreach (['customers', 'users'] as $resource) {
            foreach (['index', 'create', 'store', 'show', 'edit', 'update', 'destroy'] as $method) {
                $route = Route::getRoutes()->getByName($resource.'.'.$method);
                $this->assertNotNull($route);
                $this->assertContains('auth', $route->gatherMiddleware());
                $this->assertStringEndsWith('@'.$method, $route->getActionName());
            }
        }
    }

    public function test_real_login_returns_to_the_protected_page_and_logout_revokes_access(): void
    {
        $this->seed();
        $user = User::factory()->create(['email' => 'login.test@example.com', 'password' => 'known-test-password']);
        $this->get('/customers/create')->assertRedirect('/login');
        $this->post('/login', ['email' => $user->email, 'password' => 'known-test-password'])
            ->assertRedirect('/customers/create');
        $this->assertAuthenticatedAs($user);
        foreach (['/customers', '/customers/create', '/customers/1', '/customers/1/edit',
            '/users', '/users/create', '/users/1', '/users/1/edit'] as $url) {
            $this->get($url)->assertOk();
        }
        $this->post('/logout')->assertRedirect('/');
        $this->assertGuest();
        $this->get('/customers/create')->assertRedirect('/login');
        $this->get('/users')->assertRedirect('/login');
    }

    public function test_registration_hashes_password_and_maps_existing_user_fields(): void
    {
        $this->post('/register', [
            'username' => 'new.registrant', 'full_name' => 'New Registrant',
            'email' => 'registrant@example.com', 'password' => 'private-test-password',
            'password_confirmation' => 'private-test-password',
            'role' => 'Administrator',
        ])->assertRedirect('/dashboard')->assertSessionHasNoErrors();
        $user = User::where('username', 'new.registrant')->firstOrFail();
        $this->assertTrue(Hash::check('private-test-password', $user->password));
        $this->assertNotSame('private-test-password', $user->password);
        $this->assertSame('Cashier', $user->role);
        $this->assertArrayNotHasKey('password', $user->toArray());
        $this->get('/dashboard')->assertOk()->assertSee('New Registrant');
        $this->get('/users')->assertOk()->assertSee('new.registrant');
    }

    public function test_registration_rejects_duplicate_username_and_mismatched_password(): void
    {
        $this->seed();
        $this->post('/register', [
            'username' => 'alex.rivera', 'full_name' => 'Duplicate User',
            'email' => 'new@example.com', 'password' => 'password-one',
            'password_confirmation' => 'password-two',
        ])->assertSessionHasErrors(['username', 'password']);
        $this->assertGuest();
        $this->assertDatabaseCount('users', 5);
    }

    public function test_breeze_login_throttles_repeated_failed_attempts(): void
    {
        $email = 'throttle-test@example.com';
        $key = $email.'|127.0.0.1';
        RateLimiter::clear($key);
        try {
            for ($attempt = 0; $attempt < 6; $attempt++) {
                $response = $this->from('/login')->post('/login', ['email' => $email, 'password' => 'wrong-password']);
            }
            $response->assertRedirect('/login')->assertSessionHas('errors', function ($errors) {
                return str_contains($errors->first('email'), 'Too many login attempts');
            });
            $this->assertGuest();
        } finally {
            RateLimiter::clear($key);
        }
    }

    public function test_authenticated_resource_deletion_requires_confirmation_and_cleans_avatar(): void
    {
        $this->seed();
        $this->actingAs(User::findOrFail(1));
        Storage::fake('public');
        $this->delete('/customers/1')->assertSessionHasErrors('confirm_delete');
        $this->assertDatabaseHas('customers', ['id' => 1]);
        $this->delete('/customers/1', ['confirm_delete' => '1'])->assertRedirect('/customers');
        $this->assertDatabaseMissing('customers', ['id' => 1]);
        Storage::disk('public')->put('avatars/deletable.jpg', 'thumbnail');
        User::findOrFail(2)->update(['avatar' => 'avatars/deletable.jpg']);
        $this->delete('/users/2', ['confirm_delete' => '1'])->assertRedirect('/users');
        $this->assertDatabaseMissing('users', ['id' => 2]);
        Storage::disk('public')->assertMissing('avatars/deletable.jpg');
        $this->delete('/users/1', ['confirm_delete' => '1'])->assertForbidden();
        $this->assertDatabaseHas('users', ['id' => 1]);
    }

    public function test_blank_password_on_edit_retains_existing_hash_and_new_password_is_hashed(): void
    {
        $user = User::factory()->create(['password' => 'original-test-password']);
        $this->actingAs($user);
        $originalHash = $user->password;
        $data = ['username' => $user->username, 'full_name' => $user->full_name, 'email' => $user->email];
        $this->put('/users/'.$user->id, $data + ['password' => ''])->assertRedirect('/users');
        $this->assertSame($originalHash, $user->fresh()->password);
        $this->put('/users/'.$user->id, $data + ['password' => 'replacement-test-password',
            'password_confirmation' => 'replacement-test-password'])->assertRedirect('/users');
        $this->assertTrue(Hash::check('replacement-test-password', $user->fresh()->password));
    }
}
