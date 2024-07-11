<?php

namespace Database\Seeders;

// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use App\Models\Setting;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
//        \App\Models\User::factory()->create([
//            'name' => 'Super Admin',
//            'gender' => 'male',
//            'mobile' => '9815045726',
//            'dob' => '2020-02-02',
//            'email' => 'superadmin@hellosawari.com',
//            'password' => bcrypt('Test@123'),
//            'type' => 3,
//        ]);

        $this->call([
//            SettingSeeder::class,
//            PackageSizeSeeder::class,
//            TypeSettingSeeder::class,
//            VehicleTypeSeeder::class,
//            DeliveryTypeSeeder::class,
//            RentalFeatureSeeder::class,
            RolePermissionSeeder::class
        ]);

    }
}
