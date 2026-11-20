<?php
namespace Tests\Feature;
use App\Models\Customer;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;
class AccountFormsTest extends TestCase
{
    use RefreshDatabase;
    protected function setUp(): void
    {
        parent::setUp();
        $this->seed();
        $this->actingAs(User::findOrFail(1));
        Storage::fake('public');
    }

    public function test_create_forms_and_prefilled_edit_forms_render(): void
    {
        $this->get('/customers/create')->assertOk()->assertSee('name="_token"', false);
        $this->get('/users/create')->assertOk()->assertSee('Create User');
        $this->get('/customers/1/edit')->assertOk()->assertSee('value="Ana Reyes"', false);
        $this->get('/users/1/edit')->assertOk()->assertSee('value="alex.rivera"', false)
            ->assertSee('multipart/form-data')->assertSee('name="avatar"', false);
    }

    public function test_customer_creation_validates_required_name_and_email_and_preserves_input(): void
    {
        $this->from('/customers/create')->post('/customers', ['full_name' => 'Remember This', 'email' => 'invalid-email'])
            ->assertRedirect('/customers/create')->assertSessionHasErrors('email')
            ->assertSessionHasInput('full_name', 'Remember This')->assertSessionHasInput('email', 'invalid-email');
        $this->get('/customers/create')->assertSee('value="Remember This"', false)
            ->assertSee('Please check your details')->assertSee('valid email');
        $this->post('/customers', [])->assertSessionHasErrors(['full_name', 'email']);
        $this->assertDatabaseCount('customers', 5);
    }

    public function test_customer_can_be_created_and_updated_using_required_fields_only(): void
    {
        $this->post('/customers', ['full_name' => 'New Customer', 'email' => 'new.customer@example.com'])
            ->assertRedirect('/customers')->assertSessionHas('success');
        $customer = Customer::where('email', 'new.customer@example.com')->firstOrFail();
        $this->put('/customers/'.$customer->id, ['full_name' => 'Edited Customer', 'email' => $customer->email])
            ->assertRedirect('/customers');
        $this->assertDatabaseHas('customers', ['id' => $customer->id, 'full_name' => 'Edited Customer', 'balance' => 0]);
        $this->get('/customers')->assertSee('Edited Customer');
    }

    public function test_customer_update_rejects_invalid_data_without_changing_record(): void
    {
        $this->put('/customers/1', ['full_name' => '', 'email' => 'not-valid', 'balance' => -1])
            ->assertSessionHasErrors(['full_name', 'email', 'balance']);
        $this->assertDatabaseHas('customers', ['id' => 1, 'full_name' => 'Ana Reyes']);
        $this->post('/customers', ['full_name' => 'Duplicate', 'email' => 'ana.reyes@example.com'])
            ->assertSessionHasErrors('email');
    }

    public function test_user_creation_requires_unique_username_and_full_name_and_preserves_input(): void
    {
        $this->from('/users/create')->post('/users', ['username' => 'alex.rivera', 'full_name' => 'Remember User'])
            ->assertRedirect('/users/create')->assertSessionHasErrors('username')
            ->assertSessionHasInput('full_name', 'Remember User');
        $this->post('/users', [])->assertSessionHasErrors(['username', 'full_name']);
        $this->assertDatabaseCount('users', 5);
    }

    public function test_user_can_be_created_and_edit_can_keep_the_same_username(): void
    {
        $this->post('/users', ['username' => 'new.staff', 'full_name' => 'New Staff', 'email' => 'new.staff@example.com', 'password' => 'test-password-123', 'password_confirmation' => 'test-password-123'])
            ->assertRedirect('/users')->assertSessionHas('success');
        $user = User::where('username', 'new.staff')->firstOrFail();
        $this->put('/users/'.$user->id, ['username' => 'new.staff', 'full_name' => 'Updated Staff', 'email' => 'new.staff@example.com'])
            ->assertRedirect('/users');
        $this->assertDatabaseHas('users', ['id' => $user->id, 'full_name' => 'Updated Staff']);
        $this->put('/users/'.$user->id, ['username' => 'alex.rivera', 'full_name' => 'Duplicate'])
            ->assertSessionHasErrors('username');
    }

    public function test_avatar_is_cropped_to_jpeg_thumbnail_and_only_path_is_saved(): void
    {
        $upload = UploadedFile::fake()->image('original.png', 800, 600);
        $this->put('/users/1', ['username' => 'alex.rivera', 'full_name' => 'Alex Rivera', 'email' => 'alex.rivera@example.com', 'avatar' => $upload])
            ->assertRedirect('/users')->assertSessionHasNoErrors();
        $user = User::findOrFail(1);
        $this->assertMatchesRegularExpression('/^avatars\/[a-f0-9-]+\.jpg$/', $user->avatar);
        Storage::disk('public')->assertExists($user->avatar);
        $size = getimagesizefromstring(Storage::disk('public')->get($user->avatar));
        $this->assertSame(256, $size[0]);
        $this->assertSame(256, $size[1]);
        $this->assertSame('image/jpeg', $size['mime']);
        $this->get('/users')->assertSee($user->avatar)->assertSee('avatar-placeholder.svg');
    }

    public function test_invalid_and_oversized_avatars_are_rejected_before_writing(): void
    {
        $data = ['username' => 'alex.rivera', 'full_name' => 'Alex Rivera', 'email' => 'alex.rivera@example.com'];
        $this->put('/users/1', $data + ['avatar' => UploadedFile::fake()->create('document.txt', 10, 'text/plain')])
            ->assertSessionHasErrors('avatar');
        $this->put('/users/1', $data + ['avatar' => UploadedFile::fake()->image('large.jpg')->size(2049)])
            ->assertSessionHasErrors('avatar');
        $this->put('/users/1', $data + ['avatar' => UploadedFile::fake()->image('too-wide.jpg', 4097, 1)])
            ->assertSessionHasErrors('avatar');
        $this->assertNull(User::findOrFail(1)->avatar);
        $this->assertCount(0, Storage::disk('public')->allFiles('avatars'));
    }

    public function test_edit_without_file_keeps_avatar_and_replacement_removes_previous_thumbnail(): void
    {
        $data = ['username' => 'alex.rivera', 'full_name' => 'Alex Rivera', 'email' => 'alex.rivera@example.com'];
        $this->put('/users/1', $data + ['avatar' => UploadedFile::fake()->image('first.png')])->assertRedirect('/users');
        $firstPath = User::findOrFail(1)->avatar;
        $this->put('/users/1', $data)->assertRedirect('/users');
        $this->assertSame($firstPath, User::findOrFail(1)->avatar);
        $this->put('/users/1', $data + ['avatar' => UploadedFile::fake()->image('second.png')])->assertRedirect('/users');
        $newPath = User::findOrFail(1)->avatar;
        $this->assertNotSame($firstPath, $newPath);
        Storage::disk('public')->assertMissing($firstPath);
        Storage::disk('public')->assertExists($newPath);
    }

    public function test_invalid_details_do_not_replace_existing_avatar(): void
    {
        Storage::disk('public')->put('avatars/previous.jpg', 'previous content');
        User::findOrFail(1)->update(['avatar' => 'avatars/previous.jpg']);
        $this->put('/users/1', ['username' => '', 'full_name' => '', 'avatar' => UploadedFile::fake()->image('new.png')])
            ->assertSessionHasErrors(['username', 'full_name']);
        $this->assertSame('avatars/previous.jpg', User::findOrFail(1)->avatar);
        Storage::disk('public')->assertExists('avatars/previous.jpg');
        $this->assertCount(1, Storage::disk('public')->allFiles('avatars'));
    }

    public function test_placeholder_is_used_without_avatar_and_unknown_records_return_404(): void
    {
        $this->get('/users')->assertOk()->assertSee('avatar-placeholder.svg');
        $this->get('/users/9999/edit')->assertNotFound();
        $this->get('/customers/9999/edit')->assertNotFound();
        $this->put('/users/9999', ['username' => 'missing', 'full_name' => 'Missing'])->assertNotFound();
    }
}

