<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class StoresTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {        
        DB::table('stores')->insert([
            'name'  => '店舗A',
            'url_name' => 'tempoa',
            'client_id' => '9A4DqqT4ssrl39CAZ9JyxG',
            'client_secret' => 'UDN5p3nA5sN3P580oa57YFzcf8GFrdKrJAAF5Uv530B'
            // 'client_secret'  => Hash::make('UDN5p3nA5sN3P580oa57YFzcf8GFrdKrJAAF5Uv530B')
        ]);
    }
}
