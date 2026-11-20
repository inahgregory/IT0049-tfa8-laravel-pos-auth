<?php
namespace Database\Seeders;
use App\Models\Customer;
use App\Models\User;
use Illuminate\Database\Seeder;
class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        // Fictional sample records belong in the seeder, not the controllers.
        $customers = [
            ['full_name' => 'Ana Reyes', 'email' => 'ana.reyes@example.com', 'phone' => '0917 555 0101', 'status' => 'Active', 'balance' => 1250],
            ['full_name' => 'Marco Santos', 'email' => 'marco.santos@example.com', 'phone' => '0917 555 0102', 'status' => 'Active', 'balance' => 0],
            ['full_name' => 'Sofia Cruz', 'email' => 'sofia.cruz@example.com', 'phone' => '0917 555 0103', 'status' => 'Inactive', 'balance' => 375.5],
            ['full_name' => 'Luis Garcia', 'email' => 'luis.garcia@example.com', 'phone' => '0917 555 0104', 'status' => 'Active', 'balance' => 820],
            ['full_name' => 'Mia Torres', 'email' => 'mia.torres@example.com', 'phone' => '0917 555 0105', 'status' => 'Active', 'balance' => 0],
        ];
        foreach ($customers as $customer) {
            Customer::updateOrCreate(['email' => $customer['email']], $customer);
        }
        $users = [
            ['username' => 'alex.rivera', 'full_name' => 'Alex Rivera', 'email' => 'alex.rivera@example.com', 'role' => 'Administrator', 'status' => 'Active'],
            ['username' => 'bea.mendoza', 'full_name' => 'Bea Mendoza', 'email' => 'bea.mendoza@example.com', 'role' => 'Manager', 'status' => 'Active'],
            ['username' => 'carlo.lim', 'full_name' => 'Carlo Lim', 'email' => 'carlo.lim@example.com', 'role' => 'Cashier', 'status' => 'Active'],
            ['username' => 'dani.flores', 'full_name' => 'Dani Flores', 'email' => 'dani.flores@example.com', 'role' => 'Cashier', 'status' => 'Inactive'],
            ['username' => 'ella.ramos', 'full_name' => 'Ella Ramos', 'email' => 'ella.ramos@example.com', 'role' => 'Inventory Clerk', 'status' => 'Active'],
        ];
        foreach ($users as $user) {
            $record = User::firstOrNew(['username' => $user['username']]);
            $record->fill($user);
            // Fictional directory accounts have no shared login password.
            if (! $record->exists) { $record->password = \Illuminate\Support\Str::password(40); }
            $record->save();
        }
    }
}

