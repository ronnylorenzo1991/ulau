<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\FromCollection;
use App\Models\Centro;
use App\Models\Tarea;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;

class ReporteResumenServicioFechaFecha implements FromCollection,WithHeadings,ShouldAutoSize
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
            'Centros/Fechas',
            'Extracción de Mortalidad',
        ];
    }
    /**
    * @return \Illuminate\Support\Collection
    */
    public function collection()
    {
        $arreglo = [];
            $centros = Centro::select('id','nombre')->get();
            foreach ($centros as $centro) {
                $EM = Tarea::distinct('tareas.created_at')
                        ->where('centros_id', $centro->id)
                        ->where('tareas.fecha', '>', $this->fecha_inicio)
                        ->where('tareas.fecha', '<', $this->fecha_fin)
                        ->where('dia_operativo', 'Si')
                        ->where('servicios.nombre', 'Extracción de Mortalidad')
                        ->join('servicios', 'servicios.id', 'tareas.servicio_id')
                        ->get();
                $countEM = count($EM) == 0 ? '0' : count($EM);
                array_push($arreglo, [$centro->nombre, $countEM]);
                if($countEM > 0){
                    foreach ($EM as $value) {
                        array_push($arreglo, [$value->created_at, 1]);
                    }
                }
            }
            array_push($arreglo, ['', '']);
            array_push($arreglo, ['Centros/Fechas', 'Servicio Inspección de Pecera realizado']);
            foreach ($centros as $centro) {
                $IP = Tarea::distinct('tareas.created_at')
                        ->where('centros_id', $centro->id)
                        ->where('tareas.fecha', '>', $this->fecha_inicio)
                        ->where('tareas.fecha', '<', $this->fecha_fin)
                        ->where('dia_operativo', 'Si')
                        ->where('servicios.nombre', 'Inspección Pecera')
                        ->join('servicios', 'servicios.id', 'tareas.servicio_id')
                        ->get();
                $countIP = count($IP) == 0 ? '0' : count($IP);
                array_push($arreglo, [$centro->nombre, $countIP]);
                if($countIP > 0){
                    foreach ($IP as $value) {
                        array_push($arreglo, [$value->created_at, 1]);
                    }
                }
            }
            array_push($arreglo, ['', '']);
            array_push($arreglo, ['Centros/Fechas', 'Servicio Inspección de sistema lift-up']);
            foreach ($centros as $centro) {
                $IP = Tarea::distinct('tareas.created_at')
                        ->where('centros_id', $centro->id)
                        ->where('tareas.fecha', '>', $this->fecha_inicio)
                        ->where('tareas.fecha', '<', $this->fecha_fin)
                        ->where('dia_operativo', 'Si')
                        ->where('servicios.nombre', 'Inspección de sistema lift-up')
                        ->join('servicios', 'servicios.id', 'tareas.servicio_id')
                        ->get();
                $countIP = count($IP) == 0 ? '0' : count($IP);
                array_push($arreglo, [$centro->nombre, $countIP]);
                if($countIP > 0){
                    foreach ($IP as $value) {
                        array_push($arreglo, [$value->created_at, 1]);
                    }
                }
            }

            array_push($arreglo, ['', '']);
            array_push($arreglo, ['Centros/Fechas', 'Servicio Inspección de Red Lobera del Módulo realizado']);
            foreach ($centros as $centro) {
                $IRL = Tarea::distinct('tareas.created_at')
                        ->where('centros_id', $centro->id)
                        ->where('tareas.fecha', '>', $this->fecha_inicio)
                        ->where('tareas.fecha', '<', $this->fecha_fin)
                        ->where('dia_operativo', 'Si')
                        ->where('servicios.nombre', 'Inspección Red Lobera del modulo')
                        ->join('servicios', 'servicios.id', 'tareas.servicio_id')
                        ->get();
                $countIRL = count($IRL) == 0 ? '0' : count($IRL);
                array_push($arreglo, [$centro->nombre, $countIRL]);
                if($countIRL > 0){
                    foreach ($IRL as $value) {
                        array_push($arreglo, [$value->created_at, 1]);
                    }
                }
            }

            array_push($arreglo, ['', '']);
            array_push($arreglo, ['Centros/Fechas', 'Servicio Inspección tensores de fondeo']);
            foreach ($centros as $centro) {
                $IRL = Tarea::distinct('tareas.created_at')
                        ->where('centros_id', $centro->id)
                        ->where('tareas.fecha', '>', $this->fecha_inicio)
                        ->where('tareas.fecha', '<', $this->fecha_fin)
                        ->where('dia_operativo', 'Si')
                        ->where('servicios.nombre', 'Inspección tensores de fondeo')
                        ->join('servicios', 'servicios.id', 'tareas.servicio_id')
                        ->get();
                $countIRL = count($IRL) == 0 ? '0' : count($IRL);
                array_push($arreglo, [$centro->nombre, $countIRL]);
                if($countIRL > 0){
                    foreach ($IRL as $value) {
                        array_push($arreglo, [$value->created_at, 1]);
                    }
                }
            }

            array_push($arreglo, ['', '']);
            array_push($arreglo, ['Centros/Fechas', 'Servicio Inspección líneas de fondeo']);
            foreach ($centros as $centro) {
                $IRL = Tarea::distinct('tareas.created_at')
                        ->where('centros_id', $centro->id)
                        ->where('tareas.fecha', '>', $this->fecha_inicio)
                        ->where('tareas.fecha', '<', $this->fecha_fin)
                        ->where('dia_operativo', 'Si')
                        ->where('servicios.nombre', 'Inspección líneas de fondeo')
                        ->join('servicios', 'servicios.id', 'tareas.servicio_id')
                        ->get();
                $countIRL = count($IRL) == 0 ? '0' : count($IRL);
                array_push($arreglo, [$centro->nombre, $countIRL]);
                if($countIRL > 0){
                    foreach ($IRL as $value) {
                        array_push($arreglo, [$value->created_at, 1]);
                    }
                }
            }

            array_push($arreglo, ['', '']);
            array_push($arreglo, ['Centros/Fechas', 'Servicio Inspección fondo marino']);
            foreach ($centros as $centro) {
                $IRL = Tarea::distinct('tareas.created_at')
                        ->where('centros_id', $centro->id)
                        ->where('tareas.fecha', '>', $this->fecha_inicio)
                        ->where('tareas.fecha', '<', $this->fecha_fin)
                        ->where('dia_operativo', 'Si')
                        ->where('servicios.nombre', 'Inspección fondo marino')
                        ->join('servicios', 'servicios.id', 'tareas.servicio_id')
                        ->get();
                $countIRL = count($IRL) == 0 ? '0' : count($IRL);
                array_push($arreglo, [$centro->nombre, $countIRL]);
                if($countIRL > 0){
                    foreach ($IRL as $value) {
                        array_push($arreglo, [$value->created_at, 1]);
                    }
                }
            }

            array_push($arreglo, ['', '']);
            array_push($arreglo, ['Centros/Fechas', 'Otro Servicio realizado']);
            foreach ($centros as $centro) {
                $OS = Tarea::distinct('tareas.created_at')
                        ->where('centros_id', $centro->id)
                        ->where('tareas.fecha', '>', $this->fecha_inicio)
                        ->where('tareas.fecha', '<', $this->fecha_fin)
                        ->where('dia_operativo', 'Si')
                        ->where('servicios.nombre', 'Otro Servicio')
                        ->join('servicios', 'servicios.id', 'tareas.servicio_id')
                        ->get();
                $countOS = count($OS) == 0 ? '0' : count($OS);
                array_push($arreglo, [$centro->nombre, $countOS]);
                if($countOS > 0){
                    foreach ($OS as $value) {
                        array_push($arreglo, [$value->created_at, 1]);
                    }
                }
                //guardar sumatoria de cada fila y agregar como última fila a la variable arreglo
            }
        $reporte = collect($arreglo);
        return $reporte;
    }
}
