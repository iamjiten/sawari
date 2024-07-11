<?php

namespace Database\Seeders;

use App\Models\DeliveryType;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DeliveryTypeSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $deliveryType = [
            [
                "name" => "Economic",
                "description" => "Budget friendly",
                "min_day" => 5,
                "max_day" => 7,
                "price" => 100,
                "status" => 1
            ],
            [
                "name" => "Express",
                "description" => "Rapid fast",
                "min_day" => 1,
                "max_day" => 2,
                "price" => 100,
                "status" => 1
            ],
        ];

        foreach ($deliveryType as $delivery) {
            $newArray = array_merge($delivery, ['created_by' => 1, 'updated_by' => 1]);
            DeliveryType::create($newArray);
        }
    }
}
