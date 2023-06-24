<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class StoresTableSeeder extends Seeder
{
    public function run(): void
    {        

        DB::table('stores')->insert([
            [
                'name'  => '管理者用店舗',
                'url_name' => 'kanrisya',
                'client_id' => 'admin',
                'client_secret' => 'admin'
            ],
            // [
            //     'name'  => 'オーナ店舗2',
            //     'url_name' => 'ownerUrl2',
            //     'client_id' => '9A4DqqT4ssrl39CAZ9JyxG',
            //     'client_secret' => 'UDN5p3nA5sN3P580oa57YFzcf8GFrdKrJAAF5Uv530B',
            // ],
            // [
            //     'name'  => 'オーナ店舗3',
            //     'url_name' => 'ownerUrl3',
            //     'client_id' => '9A4DqqT4ssrl39CAZ9JyxG',
            //     'client_secret' => 'UDN5p3nA5sN3P580oa57YFzcf8GFrdKrJAAF5Uv530B',
            // ]
        ]);

        // $params = [];
        // for($i = 3; $i < 30; $i++){
        //     array_push($params,
        //         [
        //             'name'  => 'オーナ店舗' . $i,
        //             'url_name' => 'ownerUrl'. $i,
        //             'client_id' => 'clientId' . $i,
        //             'client_secret' => 'clientSecret' . $i,
        //         ]
        //     );
        // };
        // DB::table('stores')->insert($params);

    }
}
