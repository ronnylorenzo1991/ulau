<?php

namespace Database\Seeders;

use App\Models\TurnStatus;
use Illuminate\Database\Seeder;

class TurnStatusSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $status = [
            'pendiente',
            'terminado',
            'cancelado',
        ];

        foreach ($status as $value) {
            TurnStatus::firstOrCreate([
                'name' => $value,
            ]);
        }
    }
}
