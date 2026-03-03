<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class AdminSeeder extends Seeder
{
    public function run(): void
    {
        // Use environment variable for admin password or a secure default
        // In production, change this password immediately after first login
        $adminPassword = env('ADMIN_DEFAULT_PASSWORD', 'TriadCoAdmin2026!');
        
        DB::table('users')->insert([
            'name' => 'Admin',
            'email' => 'admin@triadco.com',
            'password' => Hash::make($adminPassword),
            'role' => 'admin',
            'created_at' => now(),
            'updated_at' => now(),
        ]);
    }
}
