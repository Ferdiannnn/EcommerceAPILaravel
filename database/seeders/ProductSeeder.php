<?php

namespace Database\Seeders;

use Illuminate\Support\Str;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;

class ProductSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
         for($i=1; $i < 100000; $i++){
             DB::table('products')->insert([
                 'user_id' => rand(1,100),
                 'category_id' => rand(1,5),
                 'title' => Str::random(10),
                 'description' => Str::random(10),
                 'qty' => rand(1,50),
                 'price' => rand(10000,1000000),
                 'img' => Str::random(10).'.jpg',
        ]);
        }
    }
}
