<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class UsersTableSeeder extends Seeder
{

    public function run(): void
    {
        DB::table('users')->insert([
            [
                'name'  => 'adminShun',
                'login_id' => 'admin',
                'password'  => Hash::make('admin'),
                'role' => 'admin',
                'store_id' => 1,
                'email' => 'admin@example.com'
            ],
        ]);

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



        // for($i = 2; $i < 1000; $i++){
        //     DB::table('users')->insert([
        //         [
        //             'name'  => 'owner'. '_' . $i,
        //             'login_id' => 'ownerlogin'.$i,
        //             'password'  => Hash::make('password'),
        //             'role' => 'owner',
        //             'store_id' =>  $i,
        //             'email' => 'user@example.com'
        //         ],
        //     ]);
        // }



    }
}
