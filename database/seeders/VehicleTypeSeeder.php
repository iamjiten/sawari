<?php

namespace Database\Seeders;

use App\Models\VehicleType;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class VehicleTypeSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $vehicleTypes = [
            [
                "name" => "Van",
                "weight_capacity" => 90,
                "weight_unit" => "kg",
                "distance_unit" => "km",
                "per_distance_unit_cost" => 75,
                "base_fare" => 75,
                "status" => 1
            ],
            [
                "name" => "Taxi",
                "weight_capacity" => 60,
                "weight_unit" => "kg",
                "distance_unit" => "km",
                "per_distance_unit_cost" => 75,
                "base_fare" => 75,
                "status" => 1
            ],
            [
                "name" => "Bike",
                "weight_capacity" => 40,
                "weight_unit" => "kg",
                "distance_unit" => "km",
                "per_distance_unit_cost" => 65,
                "base_fare" => 50,
                "status" => 1
            ],
        ];

        foreach($vehicleTypes as $vehicle){
            $newArray = array_merge($vehicle, ['created_by' => 1, 'updated_by' => 1]);
            VehicleType::create($newArray);
        }
    }
}
