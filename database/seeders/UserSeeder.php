<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    public function run(): void
    {
        // Cek kalau user dengan email ini belum ada
        if (!User::where('email', 'admin@example.com')->exists()) {
            User::create([
                'name' => 'Admin',
                'email' => 'admin@lehzostore.com',
                'password' => Hash::make('Uj@ng120404'), // ganti dengan password yang aman
            ]);
        }
    }
}
