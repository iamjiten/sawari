<?php

namespace Database\Seeders;

use App\Models\PackageSize;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class PackageSizeSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $packageSize = [
            [
                'name' => 'large',
                'weight' => 20,
                'price' => 100
            ],
            [
                'name' => 'medium',
                'weight' => 10,
                'price' => 80
            ],
            [
                'name' => 'small',
                'weight' => 5,
                'price' => 60
            ],
        ];

        foreach ($packageSize as $size) {
            $newArray = array_merge($size, ['created_by' => 1, 'updated_by' => 1]);
            PackageSize::create($newArray);
        }
    }
}
