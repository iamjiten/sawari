<?php

namespace Database\Seeders;

use App\Models\TypeSetting;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class TypeSettingSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $typeSettings = [
            [
                'name' => 'Cloth',
                'description' => 'Cloth',
                'price' => 50,
                'type' => 'category'
            ],
            [
                'name' => 'Document',
                'description' => 'Document',
                'price' => 50,
                'type' => 'category'
            ],
            [
                'name' => 'Food',
                'description' => 'Food',
                'price' => 50,
                'type' => 'category'
            ],
            [
                'name' => 'Fruits',
                'description' => 'Fruits',
                'price' => 50,
                'type' => 'category'
            ],
            [
                'name' => 'Fragile',
                'description' => 'Fragile',
                'price' => 50,
                'type' => 'sensible'
            ],
            [
                'name' => 'Frozen',
                'description' => 'Frozen',
                'price' => 50,
                'type' => 'sensible'
            ],
            [
                'name' => 'Liquid',
                'description' => 'Liquid',
                'price' => 50,
                'type' => 'sensible'
            ],
        ];

        foreach ($typeSettings as $type) {
            $newArray = array_merge($type, ['created_by' => 1, 'updated_by' => 1]);
            TypeSetting::create($newArray);
        }
    }
}
