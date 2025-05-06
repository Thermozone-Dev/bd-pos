<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class DiscountSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        DB::table('discounts')->insert([
            'name' => 'senior-citizen',
            'value' => '20',
            'is_percentage' => 1,
            'is_government_discount' => 1,
        ]);

        DB::table('discounts')->insert([
            'name' => 'persons-with-disabilities',
            'value' => '20',
            'is_percentage' => 1,
            'is_government_discount' => 1,
        ]);

        DB::table('discounts')->insert([
            'name' => 'national-athletes-coaches',
            'value' => '20',
            'is_percentage' => 1,
            'is_government_discount' => 1,
        ]);

        DB::table('discounts')->insert([
            'name' => 'solo-parent',
            'value' => '10',
            'is_percentage' => 1,
            'is_government_discount' => 1,
        ]);
    }
}
