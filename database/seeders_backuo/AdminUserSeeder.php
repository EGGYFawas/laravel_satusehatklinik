<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

class AdminUserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run(): void
    {
        // Membuat satu user default untuk Administrator
        // firstOrCreate() akan mencari user dengan email tersebut,
        // jika tidak ada, maka akan membuatnya. Ini mencegah duplikat.
        $adminUser = User::firstOrCreate(
            ['email' => 'admin@klinik.com'],
            [
                'name' => 'Administrator',
                'password' => Hash::make('password123'), // Ganti dengan password yang aman
            ]
        );

        // Berikan role 'admin' kepada user tersebut
        // Pastikan RolePermissionSeeder sudah dijalankan sebelumnya
        $adminUser->assignRole('admin');
    }
}
