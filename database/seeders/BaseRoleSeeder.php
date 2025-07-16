<?php

namespace Database\Seeders;

use BezhanSalleh\FilamentShield\Support\Utils;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Spatie\Permission\PermissionRegistrar;

class BaseRoleSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        //Set Manager role
        DB::table('roles')
            ->insertGetId([
                'name' => 'Manager',
                'guard_name' => 'web',
                'created_at' => \Carbon\Carbon::now(),
                'updated_at' => \Carbon\Carbon::now(),
            ]);

        $volunteer = Utils::getRoleModel()::where('name', 'Manager')->first();
        $permissions = Utils::getPermissionModel()::get(); //assigning all existing permission
        $volunteer->givePermissionTo($permissions);

        app(PermissionRegistrar::class)->forgetCachedPermissions();

        //Set Cashier role
        DB::table('roles')
            ->insertGetId([
                'name' => 'Cashier',
                'guard_name' => 'web',
                'created_at' => \Carbon\Carbon::now(),
                'updated_at' => \Carbon\Carbon::now(),
            ]);

        $volunteer = Utils::getRoleModel()::where('name', 'Cashier')->first();
        $permissions = Utils::getPermissionModel()::get(); //assigning all existing permission
        $volunteer->givePermissionTo($permissions);

        app(PermissionRegistrar::class)->forgetCachedPermissions();
    }
}
