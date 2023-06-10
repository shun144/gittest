<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class UsersTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        DB::table('users')->insert([
            'name'  => 'shun',
            'login_id' => 'shunid',
            'password'  => Hash::make('test'),
            'role' => 'owner',
            'store_id' => 1,
            'email' => 'user@example.com',
        ]);
    }
}
