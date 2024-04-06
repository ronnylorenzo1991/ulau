<?php
namespace App\Exports;
use App\Models\Reporte;
use Maatwebsite\Excel\Concerns\FromCollection;
use Illuminate\Support\Facades\DB;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Events\AfterSheet;
class ReportePilotos implements FromCollection,WithHeadings,ShouldAutoSize,WithEvents
{
    public function headings(): array
    {
        return [
            'Piloto',
            'Email'
        ];
    }
    /**
    * @return \Illuminate\Support\Collection
    */
    public function collection()
    {   $reporte = DB::table('users')
                    ->where('roles_id', 3)
                    ->select('users.name AS piloto', 'users.email AS correo')
                    ->get();
        return $reporte;
    }
    public function registerEvents(): array
    {
        return [
            AfterSheet::class    => function(AfterSheet $event) {
                $cellRange = 'A1:B1'; // All headers
                $event->sheet->getDelegate()->getStyle($cellRange)->getFill()->setFillType(\PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID)->getStartColor()->setARGB('faf214');
                $event->sheet->getDelegate()->getStyle($cellRange)->getFont()->setSize(14);
            },
        ];
    }
}
