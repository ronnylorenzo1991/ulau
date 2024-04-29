<?php
namespace App\Exports;
use App\Models\Reporte;
use Maatwebsite\Excel\Concerns\FromCollection;
use Illuminate\Support\Facades\DB;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Events\AfterSheet;
class ReporteJaulas implements FromCollection,WithHeadings,ShouldAutoSize,WithEvents
{
    public function headings(): array
    {
        return [
            'Empresa',
            'Área',
            'Centro',
            'Módulo',
            'Jaula',
        ];
    }
    /**
    * @return \Illuminate\Support\Collection
    */
    public function collection()
    {   $reporte = DB::table('jaulas')
                    ->join('modulos', 'modulos.id', '=', 'jaulas.modulo_id')
                    ->join('centros', 'centros.id', '=', 'jaulas.centro_id')
                    ->join('areas', 'areas.id', '=', 'centros.area_id')
                    ->join('empresas', 'empresas.id', '=', 'areas.empresa_id')
                    ->select('empresas.nombre AS empresa', 'areas.nombre AS area', 'centros.nombre AS centro','modulos.nombre AS modulo', 'jaulas.numero AS jaula')
                    ->get();
        return $reporte;
    }
    public function registerEvents(): array
    {
        return [
            AfterSheet::class    => function(AfterSheet $event) {
                $cellRange = 'A1:E1'; // All headers
                $event->sheet->getDelegate()->getStyle($cellRange)->getFill()->setFillType(\PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID)->getStartColor()->setARGB('faf214');
                $event->sheet->getDelegate()->getStyle($cellRange)->getFont()->setSize(14);
            },
        ];
    }
}
