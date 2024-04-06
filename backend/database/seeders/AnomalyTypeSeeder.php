<?php

namespace Database\Seeders;

use App\Models\AnomalyType;
use Illuminate\Database\Seeder;

class AnomalyTypeSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $type = [
            'Rotura',
            'Descostura',
            'Falta Tensión',
            'Cortado',
            'Manguera Torcida',
            'Manguera Cortada',
            'Cono mal Instalado',
            'Exceso de Manguera',
            'Presencia de Alimento',
            'Adherencia',
            'Objeto Caído',
            'Otra',
        ];

        foreach ($type as $value) {
            AnomalyType::firstOrCreate([
                'name' => $value,
            ]);
        }
    }
}
