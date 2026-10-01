<?php

namespace Database\Seeders;

use App\Models\Technician;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

/**
 * Run with: php artisan db:seed --class=FixItNowSeeder
 * Gives the group a ready admin login and one sample technician
 * for demoing the booking flow without manual data entry.
 */
class FixItNowSeeder extends Seeder
{
    public function run(): void
    {
        $admin = User::create([
            'name' => 'Admin',
            'email' => 'admin@fixitnow.test',
            'password' => Hash::make('password'),
            'role' => 'admin',
        ]);

        $technicianUser = User::create([
            'name' => 'Juan Dela Cruz',
            'email' => 'technician@fixitnow.test',
            'password' => Hash::make('password'),
            'role' => 'technician',
        ]);

        Technician::create([
            'user_id' => $technicianUser->id,
            'specialty' => 'Plumbing',
            'bio' => 'Sample technician for demo purposes.',
            'verification_status' => 'approved',
        ]);

        $customer = User::create([
            'name' => 'Maria Santos',
            'email' => 'customer@fixitnow.test',
            'password' => Hash::make('password'),
            'role' => 'customer',
        ]);

        $this->command->info('Seeded: admin@fixitnow.test / technician@fixitnow.test / customer@fixitnow.test (password: "password")');
    }
}
