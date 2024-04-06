<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\FromCollection;
use App\Models\Tarea;
use App\Models\AnomaliaDetectada;
use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Events\AfterSheet;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;

class ReporteAnomaliaFaenaFechas implements FromCollection,WithHeadings,ShouldAutoSize,WithEvents
{
    public $fecha_inicio;
    public $fecha_fin;

    public function __construct($fecha_inicio, $fecha_fin)
    {
        $this->fecha_inicio = $fecha_inicio;
        $this->fecha_fin = $fecha_fin;
    }
    public function headings(): array
    {
        return [
            'Servicio',
            'Anomalias',
            'No Operativo',
            'Tareas',
        ];
    }
    /**
    * @return \Illuminate\Support\Collection
    */
    public function collection()
    {
        $arreglo = [];
            $siEM = AnomaliaDetectada::join('tareas', 'tareas.id', '=', 'anomalias_detectadas.tarea_id')
                                        ->where('dia_operativo', 'Si')
                                        ->where('tareas.fecha', '>', $this->fecha_inicio)
                                        ->where('tareas.fecha', '<', $this->fecha_fin)
                                        ->where('servicio_id', 1)
                                        ->count();
            $generalEM = Tarea::where('dia_operativo', 'Si')
                                        ->where('tareas.fecha', '>', $this->fecha_inicio)
                                        ->where('tareas.fecha', '<', $this->fecha_fin)
                                        ->where('servicio_id', 1)
                                        ->count();
            $noEM = Tarea::where('dia_operativo', 'No')
                                ->where('tareas.fecha', '>', $this->fecha_inicio)
                                ->where('tareas.fecha', '<', $this->fecha_fin)
                                ->where('servicio_id', 1)
                                ->count();

            $siIP = AnomaliaDetectada::join('tareas', 'tareas.id', '=', 'anomalias_detectadas.tarea_id')
                                    ->where('dia_operativo', 'Si')
                                        ->where('tareas.fecha', '>', $this->fecha_inicio)
                                        ->where('tareas.fecha', '<', $this->fecha_fin)
                                    ->where('servicio_id', 2)
                                    ->count();
            $generalIP = Tarea::where('dia_operativo', 'Si')
                                        ->where('tareas.fecha', '>', $this->fecha_inicio)
                                        ->where('tareas.fecha', '<', $this->fecha_fin)
                                    ->where('servicio_id', 2)
                                    ->count();
            $noIP = Tarea::where('dia_operativo', 'No')
                                ->where('tareas.fecha', '>', $this->fecha_inicio)
                                ->where('tareas.fecha', '<', $this->fecha_fin)
                                ->where('servicio_id', 2)
                                ->count();

            $siISLU = AnomaliaDetectada::join('tareas', 'tareas.id', '=', 'anomalias_detectadas.tarea_id')
                                    ->where('dia_operativo', 'Si')
                                        ->where('tareas.fecha', '>', $this->fecha_inicio)
                                        ->where('tareas.fecha', '<', $this->fecha_fin)
                                    ->where('servicio_id', 3)
                                    ->count();
            $generalISLU = Tarea::where('dia_operativo', 'Si')
                                        ->where('tareas.fecha', '>', $this->fecha_inicio)
                                        ->where('tareas.fecha', '<', $this->fecha_fin)
                                    ->where('servicio_id', 3)
                                    ->count();
            $noISLU = Tarea::where('dia_operativo', 'No')
                                ->where('tareas.fecha', '>', $this->fecha_inicio)
                                ->where('tareas.fecha', '<', $this->fecha_fin)
                                    ->where('servicio_id', 3)
                                    ->count();


            $siIRL = AnomaliaDetectada::join('tareas', 'tareas.id', '=', 'anomalias_detectadas.tarea_id')
                                        ->where('dia_operativo', 'Si')
                                        ->where('tareas.fecha', '>', $this->fecha_inicio)
                                        ->where('tareas.fecha', '<', $this->fecha_fin)
                                        ->where('servicio_id', 4)
                                        ->count();
            $generalIRL = Tarea::where('dia_operativo', 'Si')
                                        ->where('tareas.fecha', '>', $this->fecha_inicio)
                                        ->where('tareas.fecha', '<', $this->fecha_fin)
                                        ->where('servicio_id', 4)
                                        ->count();
            $noIRL = Tarea::where('dia_operativo', 'No')
                                ->where('tareas.fecha', '>', $this->fecha_inicio)
                                ->where('tareas.fecha', '<', $this->fecha_fin)
                                    ->where('servicio_id', 4)
                                    ->count();

            $siITF = AnomaliaDetectada::join('tareas', 'tareas.id', '=', 'anomalias_detectadas.tarea_id')
                                        ->where('dia_operativo', 'Si')
                                        ->where('tareas.fecha', '>', $this->fecha_inicio)
                                        ->where('tareas.fecha', '<', $this->fecha_fin)
                                        ->where('servicio_id', 5)
                                        ->count();
            $generalITF = Tarea::where('dia_operativo', 'Si')
                                        ->where('tareas.fecha', '>', $this->fecha_inicio)
                                        ->where('tareas.fecha', '<', $this->fecha_fin)
                                        ->where('servicio_id', 5)
                                        ->count();
            $noITF = Tarea::where('dia_operativo', 'No')
                                ->where('tareas.fecha', '>', $this->fecha_inicio)
                                ->where('tareas.fecha', '<', $this->fecha_fin)
                                    ->where('servicio_id', 5)
                                    ->count();

            $siILF = AnomaliaDetectada::join('tareas', 'tareas.id', '=', 'anomalias_detectadas.tarea_id')
                                        ->where('dia_operativo', 'Si')
                                        ->where('tareas.fecha', '>', $this->fecha_inicio)
                                        ->where('tareas.fecha', '<', $this->fecha_fin)
                                        ->where('servicio_id', 6)
                                        ->count();
            $generalILF = Tarea::where('dia_operativo', 'Si')
                                        ->where('tareas.fecha', '>', $this->fecha_inicio)
                                        ->where('tareas.fecha', '<', $this->fecha_fin)
                                        ->where('servicio_id', 6)
                                        ->count();
            $noILF = Tarea::where('dia_operativo', 'No')
                                ->where('tareas.fecha', '>', $this->fecha_inicio)
                                ->where('tareas.fecha', '<', $this->fecha_fin)
                                    ->where('servicio_id', 6)
                                    ->count();

            $siIFM = AnomaliaDetectada::join('tareas', 'tareas.id', '=', 'anomalias_detectadas.tarea_id')
                                        ->where('dia_operativo', 'Si')
                                        ->where('tareas.fecha', '>', $this->fecha_inicio)
                                        ->where('tareas.fecha', '<', $this->fecha_fin)
                                        ->where('servicio_id', 7)
                                        ->count();
            $generalIFM = Tarea::where('dia_operativo', 'Si')
                                        ->where('tareas.fecha', '>', $this->fecha_inicio)
                                        ->where('tareas.fecha', '<', $this->fecha_fin)
                                        ->where('servicio_id', 7)
                                        ->count();
            $noIFM = Tarea::where('dia_operativo', 'No')
                                ->where('tareas.fecha', '>', $this->fecha_inicio)
                                ->where('tareas.fecha', '<', $this->fecha_fin)
                                    ->where('servicio_id', 7)
                                    ->count();


            $siOS = AnomaliaDetectada::join('tareas', 'tareas.id', '=', 'anomalias_detectadas.tarea_id')
                                    ->where('dia_operativo', 'Si')
                                        ->where('tareas.fecha', '>', $this->fecha_inicio)
                                        ->where('tareas.fecha', '<', $this->fecha_fin)
                                    ->where('servicio_id', 8)
                                    ->count();
            $generalOS = Tarea::where('dia_operativo', 'Si')
                                        ->where('tareas.fecha', '>', $this->fecha_inicio)
                                        ->where('tareas.fecha', '<', $this->fecha_fin)
                                    ->where('servicio_id', 8)
                                    ->count();
            $noOS = Tarea::where('dia_operativo', 'No')
                                ->where('tareas.fecha', '>', $this->fecha_inicio)
                                ->where('tareas.fecha', '<', $this->fecha_fin)
                                    ->where('servicio_id', 8)
                                    ->count();


        $countsiEM = $siEM == 0 ? '0' : $siEM;
        $countnoEM = $noEM == 0 ? '0' : $noEM;
        $countgeneralEM = $generalEM == 0 ? '0' : $generalEM;
        $countsiIP = $siIP == 0 ? '0' : $siIP;
        $countnoIP = $noIP == 0 ? '0' : $noIP;
        $countgeneralIP = $generalIP == 0 ? '0' : $generalIP;
        $countsiISLU = $siISLU == 0 ? '0' : $siISLU;
        $countnoISLU = $noISLU == 0 ? '0' : $noISLU;
        $countgeneralISLU = $generalISLU == 0 ? '0' : $generalISLU;
        $countsiIRL = $siIRL == 0 ? '0' : $siIRL;
        $countnoIRL = $noIRL == 0 ? '0' : $noIRL;
        $countgeneralIRL = $generalIRL == 0 ? '0' : $generalIRL;

        $countsiITF = $siITF == 0 ? '0' : $siITF;
        $countnoITF = $noITF == 0 ? '0' : $noITF;
        $countgeneralITF = $generalITF == 0 ? '0' : $generalITF;
        $countsiILF = $siILF == 0 ? '0' : $siILF;
        $countnoILF = $noILF == 0 ? '0' : $noILF;
        $countgeneralILF = $generalILF == 0 ? '0' : $generalILF;
        $countsiIFM = $siIFM == 0 ? '0' : $siIFM;
        $countnoIFM = $noIFM == 0 ? '0' : $noIFM;
        $countgeneralIFM = $generalIFM == 0 ? '0' : $generalIFM;

        $countsiOS = $siOS == 0 ? '0' : $siOS;
        $countnoOS = $noOS == 0 ? '0' : $noOS;
        $countgeneralOS = $generalOS == 0 ? '0' : $generalOS;

        if($generalEM > 0){
            array_push($arreglo, ['Extracción de Mortalidad', $countsiEM, $countnoEM, $countgeneralEM]);
        }
        if($generalIP > 0){
            array_push($arreglo, ['Inspección de Pecera', $countsiIP, $countnoIP, $countgeneralIP]);
        }
        if($generalISLU > 0){
            array_push($arreglo, ['Inspección de sistema lift-up', $countsiISLU, $countnoISLU, $countgeneralISLU]);
        }
        if($generalIRL > 0){
            array_push($arreglo, ['Inspección de Red Lobera del Módulo', $countsiIRL, $countnoIRL, $countgeneralIRL]);
        }
        if($generalITF > 0){
            array_push($arreglo, ['Inspección tensores de fondeo', $countsiITF, $countnoITF, $countgeneralITF]);
        }
        if($generalILF > 0){
            array_push($arreglo, ['Inspección líneas de fondeo', $countsiILF, $countnoILF, $countgeneralILF]);
        }
        if($generalIFM > 0){
            array_push($arreglo, ['Inspección fondo marino', $countsiIFM, $countnoIFM, $countgeneralIFM]);
        }
        if($generalOS > 0){
            array_push($arreglo, ['Otro Servicio', $countsiOS, $countnoOS, $countgeneralOS]);
        }
        $reporte = collect($arreglo);
        return $reporte;
    }
    public function registerEvents(): array
    {
        return [
            AfterSheet::class    => function(AfterSheet $event) {
                $cellRange = 'A1:D1'; // All headers
                $event->sheet->getDelegate()->getStyle($cellRange)->getFill()->setFillType(\PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID)->getStartColor()->setARGB('faf214');
                $event->sheet->getDelegate()->getStyle($cellRange)->getFont()->setSize(14);
            },
        ];
    }
}
