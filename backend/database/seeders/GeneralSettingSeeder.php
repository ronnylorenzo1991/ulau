<?php

namespace Database\Seeders;

use App\Models\GeneralSetting;
use Illuminate\Database\Seeder;

class GeneralSettingSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $settings = [
            [
                'name'        => 'active_classes',
                'description' => 'Clases en uso',
                'value'       => '["Salmon","Melanosis","Hematoma","Gapping","Salmon Inv"]',
                'active'      => 1,
            ],
        ];

        foreach ($settings as $setting) {
            GeneralSetting::create($setting);
        }
    }
}
