<?php
namespace Tests\Feature;
use App\Models\Customer;
use App\Models\User;
use Illuminate\Database\QueryException;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
class PosDatabaseTest extends TestCase
{
    use RefreshDatabase;
    public function test_seeding_is_repeatable(): void
    {
        $this->seed();
        $this->seed();
        $this->assertDatabaseCount('customers', 5);
        $this->assertDatabaseCount('users', 5);
        $this->assertDatabaseHas('customers', ['full_name' => 'Ana Reyes', 'balance' => 1250]);
        $this->assertDatabaseHas('users', ['username' => 'alex.rivera', 'full_name' => 'Alex Rivera']);
    }
    public function test_pages_display_changes_read_from_database(): void
    {
        $this->seed();
        $this->actingAs(User::findOrFail(1));
        Customer::where('email', 'ana.reyes@example.com')->update(['full_name' => 'Changed Customer']);
        User::where('username', 'alex.rivera')->update(['full_name' => 'Changed Staff']);
        $this->get('/customers')->assertOk()->assertSee('Changed Customer')->assertDontSee('Ana Reyes');
        $this->get('/users')->assertOk()->assertSee('Changed Staff')->assertDontSee('Alex Rivera');
    }
    public function test_username_is_unique_in_database(): void
    {
        $user = User::factory()->create(['username' => 'same.username']);
        $this->expectException(QueryException::class);
        User::factory()->create(['username' => $user->username]);
    }
    public function test_empty_customer_directory_and_single_user_directory_render(): void
    {
        $this->actingAs(User::factory()->create());
        $this->get('/customers')->assertOk()->assertSee('No records yet.');
        $this->get('/users')->assertOk()->assertSee('1 accounts');
    }
}

