<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class LineTableSeeder extends Seeder
{
    public function run(): void
    {
        // DB::table('lines')->insert([
        //     [
        //         'store_id'  => 2,
        //         'user_name' => '駿',
        //         'token' => 'PQ13bmsLpTrxqjK2sNlZ2juPtgrrYNkhZ9waLWFGi12',
        //         'is_valid' => true
        //     ],
        //     // [
        //     //     'store_id'  => 2,
        //     //     'user_name' => 'NG',
        //     //     'token' => 'aaaaa',
        //     //     'is_valid' => true
        //     // ]
        // ]);

        // $params = [];
        // for($i = 2; $i < 10; $i++){
        //     if ($i == 5)
        //     {
        //         array_push($params,
        //             [
        //                 'store_id'  => 2,
        //                 'user_name' => '正常系dummy_' . $i,
        //                 'token' => 'sUi6PBNX4VfB0B8amd0Mscci2rTqRbQEbJ61HK33V2w',
        //                 'is_valid' => true
        //             ]
        //         );
        //     }
        //     else {
        //         array_push($params,
        //         [
        //             'store_id'  => 2,
        //             'user_name' => '異常系dummy_' . $i,
        //             'token' => 'sUihllalala0B★0Mscci2rTqRbQEbJ61HK33V2w',
        //             'is_valid' => true
        //         ]
        //     );

        //     }

        // };
        // DB::table('lines')->insert($params);

    }
}
