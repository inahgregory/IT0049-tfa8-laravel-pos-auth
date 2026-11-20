<?php
namespace Tests\Feature;
use Tests\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;
class PosPagesTest extends TestCase
{
    use RefreshDatabase;
    protected function setUp(): void { parent::setUp(); $this->seed(); $this->actingAs(\App\Models\User::findOrFail(1)); }
    public function test_every_page_and_navigation_link_work(): void {
        foreach (['/' => 'Every account.', '/about' => 'Purpose', '/customers' => 'Customer Accounts', '/users' => 'User Accounts'] as $url => $text) {
            $response = $this->get($url)->assertOk()->assertSee($text);
            foreach (['home', 'about', 'customers.index', 'users.index'] as $route) {
                $response->assertSee('href="'.route($route).'"', false);
            }
        }
    }
    public function test_customer_directory_renders_all_five_records_and_balance(): void {
        $this->get('/customers')->assertOk()->assertSeeInOrder(['Ana Reyes', 'Marco Santos', 'Sofia Cruz', 'Luis Garcia', 'Mia Torres'])->assertSee('1,250.00')->assertSee('375.50');
    }
    public function test_user_directory_renders_all_five_records_and_roles(): void {
        $this->get('/users')->assertOk()->assertSeeInOrder(['Alex Rivera', 'Bea Mendoza', 'Carlo Lim', 'Dani Flores', 'Ella Ramos'])->assertSee('Administrator')->assertSee('Cashier')->assertSee('Inventory Clerk');
    }
    public function test_unknown_page_returns_not_found(): void { $this->get('/missing-page')->assertNotFound(); }
}


