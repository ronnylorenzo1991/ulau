<?php

namespace App\Exports;

use App\Models\Reporte;
use Maatwebsite\Excel\Concerns\FromCollection;
use Illuminate\Support\Facades\DB;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Events\AfterSheet;

class ReporteTareaTodoExport implements FromCollection,WithHeadings,ShouldAutoSize,WithEvents
{
    public $fecha_inicio;
    public $fecha_fin;
    public $id;

    public function __construct($fecha_inicio, $fecha_fin, $id)
    {
        $this->fecha_inicio = $fecha_inicio;
        $this->fecha_fin = $fecha_fin;
        $this->id = $id;
    }
    public function headings(): array
    {
        return [
            'Fecha',
            'Piloto',
            'Centro',
            'Mandante',
            'SN ROV',
            'hora',
            'Día Operativo',
            'Folio',
            'Servicio',
            'Módulo',
            'Jaula',
            'Parte',
            'Orientación'
        ];
    }
    /**
    * @return \Illuminate\Support\Collection
    */
    public function collection()
    {
        $reporte = DB::table('tareas')
                ->leftJoin('centros', 'centros.id', '=', 'tareas.centros_id')
                ->leftJoin('users', 'users.id', '=', 'tareas.users_id')
                ->leftJoin('jaulas', 'tareas.jaulas_id', '=', 'jaulas.id')
                ->leftJoin('partes', 'tareas.partes_id', '=', 'partes.id')
                ->leftJoin('modulos', 'modulos.id', '=', 'jaulas.modulo_id')
                ->leftJoin('orientaciones', 'orientaciones.id', '=', 'tareas.orientacion_id')
                ->leftJoin('servicios', 'servicios.id', '=', 'tareas.servicio_id')
                ->where('tareas.fecha', '>', $this->fecha_inicio)
                ->where('tareas.fecha', '<', $this->fecha_fin)
                ->where('tareas.centros_id', $this->id)
                ->select('tareas.fecha', 'users.name AS piloto', 'centros.nombre AS centro', 'tareas.encargado_centro', 'tareas.rov', 'tareas.hora', 'tareas.dia_operativo','tareas.folio', 'servicios.nombre AS servicio', 'modulos.nombre AS modulo', 'jaulas.numero AS jaula','partes.nombre AS partes', 'orientaciones.nombre')
                ->get();
        return $reporte;
    }
    public function registerEvents(): array
    {
        return [
            AfterSheet::class    => function(AfterSheet $event) {
                $cellRange = 'A1:M1'; // All headers
                $event->sheet->getDelegate()->getStyle($cellRange)->getFill()->setFillType(\PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID)->getStartColor()->setARGB('faf214');
                $event->sheet->getDelegate()->getStyle($cellRange)->getFont()->setSize(14);
            },
        ];
    }
}
