<?php

namespace Database\Seeders;

use App\Models\PaymentMethod;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class PaymentMethodSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        PaymentMethod::factory()->create([
            'name' => 'Cash',
            'is_digital' => false,
            'is_enabled' => true,
        ]);

        PaymentMethod::factory()->create([
            'name' => 'Gcash',
            'is_digital' => true,
            'is_enabled' => true,
        ]);

        PaymentMethod::factory()->create([
            'name' => 'Maya',
            'is_digital' => true,
            'is_enabled' => true,
        ]);

        PaymentMethod::factory()->create([
            'name' => 'Debit Card',
            'is_digital' => true,
            'is_enabled' => true,
        ]);

        PaymentMethod::factory()->create([
            'name' => 'Credit Card',
            'is_digital' => true,
            'is_enabled' => true,
        ]);

    }
}
