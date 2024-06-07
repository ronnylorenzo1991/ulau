<?php
namespace App\Services;

use Elibyy\TCPDF\Facades\TCPDF as PDF;

class ReportService {
    public function createPdf($reporteData){
        try{
            PDF::AddPage('L');
            
            PDF::SetMargins(0, 0, 0, true);
            PDF::SetHeaderMargin(0);
            PDF::SetFooterMargin(0);
            PDF::SetAutoPageBreak(true, 0);

            $cover = storage_path('app/public/recursos/robot.png');
            PDF::Image($cover, 0, 0, 119, 220);

            $logo = storage_path('app/public/recursos/logo.png');
            PDF::Image($logo, 240, 190, 50);

            PDF::SetFont('helvetica', 'B', 20);
            PDF::SetXY(120, 30);
            PDF::Cell(180, 12, 'INFORME DIARIO', 0, 0, 'C');
            PDF::SetXY(120, 40);
            PDF::Cell(180, 12, 'INSPECCIÓN ROBÓTICA SUBMARINA', 0, 0, 'C');

            PDF::SetFont('helvetica', '', 8);
            PDF::SetXY(115, 70);
            PDF::Cell(40, 10, 'ENCARGADO DE CENTRO', 'RB', 0, 'L');
            PDF::SetXY(115, 80);
            PDF::Cell(40, 10, 'CENTRO', 'RB', 0, 'L');
            PDF::SetXY(115, 90);
            PDF::Cell(40, 10, 'CLIENTE', 'RB', 0, 'L');
            PDF::SetXY(115, 100);
            PDF::Cell(40, 10, 'N° JAULAS', 'RB', 0, 'L');
            PDF::SetXY(115, 110);
            PDF::Cell(40, 10, 'DIMENSIONES', 'RB', 0, 'L');
            PDF::SetXY(115, 120);
            PDF::Cell(40, 10, 'AREA', 'RB', 0, 'L');
            PDF::SetXY(115, 130);
            PDF::Cell(40, 10, 'CONDICION PUERTO', 'RB', 0, 'L');
            PDF::SetXY(115, 140);
            PDF::Cell(40, 10, 'FECHA', 'R', 0, 'L');

            $jaulas = 0;
            $dimensiones = "";

            PDF::SetXY(155, 70);
            PDF::Cell(50, 10, $reporteData->encargado_centro, 'B', 0, 'L');
            PDF::SetXY(155, 80);
            PDF::Cell(50, 10, $reporteData->centro->nombre, 'B', 0, 'L');
            PDF::SetXY(155, 90);
            PDF::Cell(50, 10, $reporteData->empresa->nombre, 'B', 0, 'L');
            PDF::SetXY(155, 100);
            PDF::Cell(50, 10, $jaulas, 'B', 0, 'L');
            PDF::SetXY(155, 110);
            PDF::Cell(50, 10, $dimensiones, 'B', 0, 'L');
            PDF::SetXY(155, 120);
            PDF::Cell(50, 10, $reporteData->centro->area, 'B', 0, 'L');
            PDF::SetXY(155, 130);
            PDF::Cell(50, 10, $reporteData->condicion_puerto, 'B', 0, 'L');
            PDF::SetXY(155, 140);
            PDF::Cell(50, 10, $this->fromMysql($reporteData->fecha), '', 0, 'L');

            PDF::SetXY(206, 70);
            PDF::Cell(40, 10, 'PILOTO ROV', 'RB', 0, 'L');
            PDF::SetXY(206, 80);
            PDF::Cell(40, 10, 'EQUIPO ROV', 'RB', 0, 'L');
            PDF::SetXY(206, 90);
            PDF::Cell(40, 10, 'HORARIO AM', 'RB', 0, 'L');
            PDF::SetXY(206, 100);
            PDF::Cell(40, 10, 'HORARIO PM', 'RB', 0, 'L');
            PDF::SetXY(206, 110);
            PDF::Cell(40, 10, 'TEAM', 'RB', 0, 'L');
            PDF::SetXY(206, 120);
            PDF::Cell(40, 10, 'BITACORA N°', 'RB', 0, 'L');
            PDF::SetXY(206, 130);
            PDF::Cell(40, 10, '% DE OPERATIVIDAD', 'R', 0, 'L');

            PDF::SetXY(246, 70);
            PDF::Cell(50, 10, $reporteData->piloto_rov, 'B', 0, 'L');
            PDF::SetXY(246, 80);
            PDF::Cell(50, 10, @$reporteData->rov->nombre, 'B', 0, 'L');
            PDF::SetXY(246, 90);
            PDF::Cell(50, 10, $reporteData->horario_am, 'B', 0, 'L');
            PDF::SetXY(246, 100);
            PDF::Cell(50, 10, $reporteData->horario_pm, 'B', 0, 'L');
            PDF::SetXY(246, 110);
            PDF::Cell(50, 10, @$reporteData->team, 'B', 0, 'L');
            PDF::SetXY(246, 120);
            PDF::Cell(50, 10, $reporteData->numero_bitacora, 'B', 0, 'L');
            PDF::SetXY(246, 130);
            PDF::Cell(50, 10, $reporteData->porcentaje_operatividad, '', 0, 'L');

            PDF::RoundedRect(115, 70, 90, 80, 3.50, '1111', 'D');
            PDF::RoundedRect(206, 70, 90, 70, 3.50, '1111', 'D');
            
            $filename = "reporte_inspeccion.pdf";
            $out = storage_path('app/public/'.$filename);

            //Log::debug($out);
            PDF::Output($out, 'F');
            return $filename;
        }catch(Exception $ex){
            Log::debug("Linea: " . $ex->getLine() . ". Mensaje: " . $ex->getMessage());
        }
    }

    private function fromMysql($fecha){
        list($year, $month, $day) = explode("-", $fecha);
        return "$day-$month-$year";
    }
}