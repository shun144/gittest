<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class ScheduleSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {

        $params = [];
        for($i = 2; $i < 4; $i++){
            array_push($params,
                [
                    'name'  => 'owner'. '_' . $i,
                    'login_id' => 'ownerlogin'.$i,
                    'password'  => Hash::make('password'),
                    'role' => 'owner',
                    'store_id' =>  $i,
                    'email' => 'user@example.com'
                ]
            );   
        };
        DB::table('users')->insert($params);
    }
}
