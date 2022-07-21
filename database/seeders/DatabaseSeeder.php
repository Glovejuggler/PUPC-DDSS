<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     *
     * @return void
     */
    public function run()
    {
        // \App\Models\User::factory(10)->create();
        DB::table('users')->insert([
            'first_name' => 'Admin',
            'last_name' => 'Admin',
            'email' => 'admin@admin.com',
            'address' => 'Calauan, Laguna',
            'role_id' => '1',
            'password' => Hash::make('admin123'),
        ]);

        DB::table('roles')->insert([
            'roleName' => 'Admin',
        ]);
    }
}
