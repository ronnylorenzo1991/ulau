<?php
namespace App\Exports;
use App\Models\Reporte;
use Maatwebsite\Excel\Concerns\FromCollection;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use App\Models\Centro;
use App\Models\Tarea;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Events\AfterSheet;
class ReporteResumenServicio implements FromCollection,WithHeadings,ShouldAutoSize,WithEvents
{
    public $id;

    public function __construct($id)
    {
        $this->id = $id;
    }
    public function headings(): array
    {
        return [
            'Centro',
            'Cantidad de Días RCA',
            'Cantidad de Días Operativos',
            'Cantidad de Días General',
            'Cantidad de Servicios',
            '',
            'Centro',
            'Servicios Extracción de Mortalidad',
            'Servicios Inspección Pecera',
            'Inspección de sistema lift-up',
            'Servicios Inspección Red Lobera del Módulo',
            'Inspección tensores de fondeo',
            'Inspección líneas de fondeo',
            'Inspección fondo marino',
            'Otro Servicio',
            'Días Extracción de Mortalidad',
            'Días Inspección Pecera',
            'Días de sistema lift-up',
            'Días Inspección Red Lobera del Módulo',
            'Días tensores de fondeo',
            'Días líneas de fondeo',
            'Días fondo marino',
            'Días Otro Servicio',
        ];
    }
    /**
    * @return \Illuminate\Support\Collection
    */
    public function collection()
    {
        if($this->id == 0){
            $arreglo = [];
            $centros = Centro::select('id','nombre')->get();
            foreach ($centros as $centro) {
                $countRCA = Tarea::distinct('tareas.created_at')
                                        ->where('centros_id', $centro->id)
                                        ->where('dia_operativo', 'RCA')
                                        ->count();
                $countSI = Tarea::distinct('tareas.created_at')
                                        ->where('centros_id', $centro->id)
                                        ->where('dia_operativo', 'Si')
                                        ->count();
                $countGeneral = Tarea::distinct('tareas.created_at')
                                        ->where('centros_id', $centro->id)
                                        ->count();
                $countSIServicios = Tarea::where('centros_id', $centro->id)
                                        ->where('dia_operativo', 'Si')
                                        ->count();
                $countEM = Tarea::where('centros_id', $centro->id)
                                        ->where('dia_operativo', 'Si')
                                        ->where('servicio_id', 1)
                                        ->count();
                $countIP = Tarea::where('centros_id', $centro->id)
                                        ->where('dia_operativo', 'Si')
                                        ->where('servicio_id', 2)
                                        ->count();
                $countISLU = Tarea::where('centros_id', $centro->id)
                                        ->where('dia_operativo', 'Si')
                                        ->where('servicio_id', 3)
                                        ->count();
                $countIRL = Tarea::where('centros_id', $centro->id)
                                        ->where('dia_operativo', 'Si')
                                        ->where('servicio_id', 4)
                                        ->count();
                $countITF = Tarea::where('centros_id', $centro->id)
                                        ->where('dia_operativo', 'Si')
                                        ->where('servicio_id', 5)
                                        ->count();
                $countILF = Tarea::where('centros_id', $centro->id)
                                        ->where('dia_operativo', 'Si')
                                        ->where('servicio_id', 6)
                                        ->count();
                $countIFM = Tarea::where('centros_id', $centro->id)
                                        ->where('dia_operativo', 'Si')
                                        ->where('servicio_id', 7)
                                        ->count();
                $countOS = Tarea::where('centros_id', $centro->id)
                                        ->where('dia_operativo', 'Si')
                                        ->where('servicio_id', 8)
                                        ->count();
                $countDayEM = Tarea::distinct('tareas.created_at')
                                        ->where('centros_id', $centro->id)
                                        ->where('dia_operativo', 'Si')
                                        ->where('servicio_id', 1)
                                        ->count();
                $countDayIP = Tarea::distinct('tareas.created_at')
                                        ->where('centros_id', $centro->id)
                                        ->where('dia_operativo', 'Si')
                                        ->where('servicio_id', 2)
                                        ->count();
                $countDayISLU = Tarea::distinct('tareas.created_at')
                                        ->where('centros_id', $centro->id)
                                        ->where('dia_operativo', 'Si')
                                        ->where('servicio_id', 3)
                                        ->count();
                $countDayIRL = Tarea::distinct('tareas.created_at')
                                        ->where('centros_id', $centro->id)
                                        ->where('dia_operativo', 'Si')
                                        ->where('servicio_id', 4)
                                        ->count();
                $countDayITF = Tarea::distinct('tareas.created_at')
                                        ->where('centros_id', $centro->id)
                                        ->where('dia_operativo', 'Si')
                                        ->where('servicio_id', 5)
                                        ->count();
                $countDayILF = Tarea::distinct('tareas.created_at')
                                        ->where('centros_id', $centro->id)
                                        ->where('dia_operativo', 'Si')
                                        ->where('servicio_id', 6)
                                        ->count();
                $countDayIFM = Tarea::distinct('tareas.created_at')
                                        ->where('centros_id', $centro->id)
                                        ->where('dia_operativo', 'Si')
                                        ->where('servicio_id', 7)
                                        ->count();
                $countDayOS = Tarea::distinct('tareas.created_at')
                                        ->where('centros_id', $centro->id)
                                        ->where('dia_operativo', 'Si')
                                        ->where('servicio_id', 8)
                                        ->count();
                $RCA = $countRCA == 0 ? '0' : $countRCA;
                $SI = $countSI == 0 ? '0' : $countSI;
                $General = $countGeneral == 0 ? '0' : $countGeneral;
                $Servicios = $countSIServicios == 0 ? '0' : $countSIServicios;
                $EM = $countEM == 0 ? '0' : $countEM;
                $IP = $countIP == 0 ? '0' : $countIP;
                $IRL = $countIRL == 0 ? '0' : $countIRL;
                $OS = $countOS == 0 ? '0' : $countOS;
                $ISLU = $countISLU == 0 ? '0' : $countISLU;
                $ITF = $countITF == 0 ? '0' : $countITF;
                $ILF = $countILF == 0 ? '0' : $countILF;
                $IFM = $countIFM == 0 ? '0' : $countIFM;
                $DayEM = $countDayEM == 0 ? '0' : $countDayEM;
                $DayIP = $countDayIP == 0 ? '0' : $countDayIP;
                $DayIRL = $countDayIRL == 0 ? '0' : $countDayIRL;
                $DayOS = $countDayOS == 0 ? '0' : $countDayOS;
                $DayISLU = $countDayISLU == 0 ? '0' : $countDayISLU;
                $DayITF = $countDayITF == 0 ? '0' : $countDayITF;
                $DayILF = $countDayILF == 0 ? '0' : $countDayILF;
                $DayIFM = $countDayIFM == 0 ? '0' : $countDayIFM;
                array_push($arreglo, [$centro->nombre, $RCA, $SI, $General, $Servicios, '',$centro->nombre, $EM, $IP, $ISLU, $IRL, $ITF, $ILF, $IFM, $OS, $DayEM, $DayIP, $DayISLU, $DayIRL, $DayITF, $DayILF, $DayIFM, $DayOS]);
                //guardar sumatoria de cada fila y agregar como última fila a la variable arreglo
            }
        } else {
            $arreglo = [];
            $centro = Centro::find($this->id);
            $countRCA = Tarea::distinct('tareas.created_at')
                                        ->where('centros_id', $this->id)
                                        ->where('dia_operativo', 'RCA')
                                        ->count();
                $countSI = Tarea::distinct('tareas.created_at')
                                        ->where('centros_id', $this->id)
                                        ->where('dia_operativo', 'Si')
                                        ->count();
                $countGeneral = Tarea::distinct('tareas.created_at')
                                        ->where('centros_id', $this->id)
                                        ->count();
                $countSIServicios = Tarea::where('centros_id', $this->id)
                                        ->where('dia_operativo', 'Si')
                                        ->count();
                $countEM = Tarea::where('centros_id', $this->id)
                                        ->where('dia_operativo', 'Si')
                                        ->where('servicio_id', 1)
                                        ->count();
                $countIP = Tarea::where('centros_id', $this->id)
                                        ->where('dia_operativo', 'Si')
                                        ->where('servicio_id', 2)
                                        ->count();
                $countISLU = Tarea::where('centros_id', $this->id)
                                        ->where('dia_operativo', 'Si')
                                        ->where('servicio_id', 3)
                                        ->count();
                $countIRL = Tarea::where('centros_id', $this->id)
                                        ->where('dia_operativo', 'Si')
                                        ->where('servicio_id', 4)
                                        ->count();
                $countITF = Tarea::where('centros_id', $this->id)
                                        ->where('dia_operativo', 'Si')
                                        ->where('servicio_id', 5)
                                        ->count();
                $countILF = Tarea::where('centros_id', $this->id)
                                        ->where('dia_operativo', 'Si')
                                        ->where('servicio_id', 6)
                                        ->count();
                $countIFM = Tarea::where('centros_id', $this->id)
                                        ->where('dia_operativo', 'Si')
                                        ->where('servicio_id', 7)
                                        ->count();
                $countOS = Tarea::where('centros_id', $this->id)
                                        ->where('dia_operativo', 'Si')
                                        ->where('servicio_id', 8)
                                        ->count();
                $countDayEM = Tarea::distinct('tareas.created_at')
                                        ->where('centros_id', $this->id)
                                        ->where('dia_operativo', 'Si')
                                        ->where('servicio_id', 1)
                                        ->count();
                $countDayIP = Tarea::distinct('tareas.created_at')
                                        ->where('centros_id', $this->id)
                                        ->where('dia_operativo', 'Si')
                                        ->where('servicio_id', 2)
                                        ->count();
                $countDayISLU = Tarea::distinct('tareas.created_at')
                                        ->where('centros_id', $this->id)
                                        ->where('dia_operativo', 'Si')
                                        ->where('servicio_id', 3)
                                        ->count();
                $countDayIRL = Tarea::distinct('tareas.created_at')
                                        ->where('centros_id', $this->id)
                                        ->where('dia_operativo', 'Si')
                                        ->where('servicio_id', 4)
                                        ->count();
                $countDayITF = Tarea::distinct('tareas.created_at')
                                        ->where('centros_id', $this->id)
                                        ->where('dia_operativo', 'Si')
                                        ->where('servicio_id', 5)
                                        ->count();
                $countDayILF = Tarea::distinct('tareas.created_at')
                                        ->where('centros_id', $this->id)
                                        ->where('dia_operativo', 'Si')
                                        ->where('servicio_id', 6)
                                        ->count();
                $countDayIFM = Tarea::distinct('tareas.created_at')
                                        ->where('centros_id', $this->id)
                                        ->where('dia_operativo', 'Si')
                                        ->where('servicio_id', 7)
                                        ->count();
                $countDayOS = Tarea::distinct('tareas.created_at')
                                        ->where('centros_id', $this->id)
                                        ->where('dia_operativo', 'Si')
                                        ->where('servicio_id', 8)
                                        ->count();
                $RCA = $countRCA == 0 ? '0' : $countRCA;
                $SI = $countSI == 0 ? '0' : $countSI;
                $General = $countGeneral == 0 ? '0' : $countGeneral;
                $Servicios = $countSIServicios == 0 ? '0' : $countSIServicios;
                $EM = $countEM == 0 ? '0' : $countEM;
                $IP = $countIP == 0 ? '0' : $countIP;
                $IRL = $countIRL == 0 ? '0' : $countIRL;
                $OS = $countOS == 0 ? '0' : $countOS;
                $ISLU = $countISLU == 0 ? '0' : $countISLU;
                $ITF = $countITF == 0 ? '0' : $countITF;
                $ILF = $countILF == 0 ? '0' : $countILF;
                $IFM = $countIFM == 0 ? '0' : $countIFM;
                $DayEM = $countDayEM == 0 ? '0' : $countDayEM;
                $DayIP = $countDayIP == 0 ? '0' : $countDayIP;
                $DayIRL = $countDayIRL == 0 ? '0' : $countDayIRL;
                $DayOS = $countDayOS == 0 ? '0' : $countDayOS;
                $DayISLU = $countDayISLU == 0 ? '0' : $countDayISLU;
                $DayITF = $countDayITF == 0 ? '0' : $countDayITF;
                $DayILF = $countDayILF == 0 ? '0' : $countDayILF;
                $DayIFM = $countDayIFM == 0 ? '0' : $countDayIFM;
                array_push($arreglo, [$centro->nombre, $RCA, $SI, $General, $Servicios, '',$centro->nombre, $EM, $IP, $ISLU, $IRL, $ITF, $ILF, $IFM, $OS, $DayEM, $DayIP, $DayISLU, $DayIRL, $DayITF, $DayILF, $DayIFM, $DayOS]);
        }
        $reporte = collect($arreglo);
        return $reporte;
    }
    public function registerEvents(): array
    {
        return [
            AfterSheet::class    => function(AfterSheet $event) {
                $cellRange = 'A1';
                $event->sheet->getDelegate()->getStyle($cellRange)->getFont()->setSize(14);
                $cellRange = 'G1';
                $event->sheet->getDelegate()->getStyle($cellRange)->getFont()->setSize(14);
                $cellRange = 'B1:E1'; // All headers
                $event->sheet->getDelegate()->getStyle($cellRange)->getFill()->setFillType(\PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID)->getStartColor()->setARGB('faf214');
                $event->sheet->getDelegate()->getStyle($cellRange)->getFont()->setSize(14);
                $cellRange = 'H1:W1'; // All headers
                $event->sheet->getDelegate()->getStyle($cellRange)->getFill()->setFillType(\PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID)->getStartColor()->setARGB('faf214');
                $event->sheet->getDelegate()->getStyle($cellRange)->getFont()->setSize(14);
            },
        ];
    }
}
