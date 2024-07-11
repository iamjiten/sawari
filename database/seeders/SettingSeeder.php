<?php

namespace Database\Seeders;

use App\Models\Setting;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class SettingSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $reasons = [
            'I Entered Wrong location',
            'I Waited too long',
            'I am not ready yet to send package',
            'Change of Mind',
        ];

        foreach($reasons as $reason){
            Setting::create([
                'key' => 'reason',
                'value' => $reason
            ]);
        }


        $brands = [
            'bajaj' => ['Discover 125 Disc', 'Pulsar 150 SD', 'Pulsar NS 200 ABS', 'Dominar 250 Dual ABS', 'Avenger 160 Street ABS'],
            'yamaha' => ['Yamaha R15 V3 BS6', 'Yamaha FZ V2', 'Yamaha MT-09', 'Yamaha XTZ 150 FI'],
            'ktm' => ['KTM Duke 200', 'KTM Duke 250', 'KTM RC 200', 'KTM 250 Adventure'],
            'honda' => ['Honda CB Shine 125', 'Honda XBlade', 'Honda CB Hornet 160R', 'Honda CB350 RS', 'Honda CRF300L']
        ];

        $colors = ['red', 'black', 'blue'];

        foreach($brands as $brand => $models){
            $b = Setting::create([
                'key' => 'brand',
                'value' => $brand
            ]);

            foreach ($models as $model){
                $m = $b->brandToModel()->create([
                    'key' => 'model',
                    'value' => $model
                ]);

                foreach($colors as $color){
                    $m->modelToColor()->create([
                        'key' => 'color',
                        'value' => $color
                    ]);
                }
            }
        }
    }
}
