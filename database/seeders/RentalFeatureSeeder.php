<?php

namespace Database\Seeders;

use App\Models\RentalFeature;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class RentalFeatureSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $features = [
            'rental' => [
                'basic_info' => [
                    'door' => [2, 4],
                    'passenger' => [2, 4, 5, 6],
                    'gear' => ['auto', 'manual'],
                    'fuel' => ['petrol', 'diesel']
                ],
                'services' => [
                    'mileage' => [
                        'Unlimited Mileage'
                    ],
                    'insurance' => [
                        'insurance against theft',
                        'vehicle insurance coverage',
                        'third party liability coverage'
                    ]
                ]
            ]
        ];

        foreach ($features['rental'] as $key => $category) {
            foreach ($category as $k => $value) {
                foreach ($value as $v) {
                    RentalFeature::create([
                        'module' => 'rental',
                        'category' => $key,
                        'key' => $k,
                        'value' => $v,
                    ]);
                }
            }
        }
    }
}
