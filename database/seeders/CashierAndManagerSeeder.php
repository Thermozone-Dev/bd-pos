<?php

namespace Database\Seeders;

use App\Models\User;
use BezhanSalleh\FilamentShield\Support\Utils;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class CashierAndManagerSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {

        $cashier = User::factory()->create([
            'name' => 'Cashiers Account',
            'email' => 'cashier@pos-sikat.com',
            'password' => Hash::make('password123'),
        ]);
        $role = Utils::getRoleModel()::where('name', 'Cashier')->first();

        DB::table('model_has_roles')->insert([
            'role_id' => $role->id,
            'model_type' => 'App\Models\User',
            'model_id' => $cashier->id,
        ]);

        $manager = User::factory()->create([
            'name' => 'Managers Account',
            'email' => 'manager@pos-sikat.com',
            'password' => Hash::make('password123'),
        ]);

        $role = Utils::getRoleModel()::where('name', 'Manager')->first();

        DB::table('model_has_roles')->insert([
            'role_id' => $role->id,
            'model_type' => 'App\Models\User',
            'model_id' => $manager->id,
        ]);

    }
}
