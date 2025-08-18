<?php

namespace Database\Seeders;

use App\Models\PaymentMethod;
use DragonCode\Contracts\Cashier\Config\Payment;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $this->call([
            UserSeeder::class,
            BaseRoleSeeder::class,
            CashierAndManagerSeeder::class,
            PaymentMethodSeeder::class,
            DiscountSeeder::class,
        ]);
    }
}
