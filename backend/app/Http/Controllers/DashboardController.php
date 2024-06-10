<?php

namespace App\Http\Controllers;

use App\Repositories\Bill\BillRepository;
use App\Repositories\Turn\TurnRepository;
use Carbon\Carbon;
use Illuminate\Http\Request;

class DashboardController extends Controller
{
    private TurnRepository $turnRepository;
    private BillRepository $billRepository;

    public function __construct(TurnRepository $turnRepository, BillRepository $billRepository)
    {
        $this->turnRepository = $turnRepository;
        $this->billRepository = $billRepository;
    }

    public function calendarEvents(Request $request)
    {
        try {

            $todayDate = Carbon::now()->format('Y-m-d');

            $startAt = Carbon::parse($request->get('start') ?: $todayDate)
                ->startOfMonth()
                ->format('Y-m-d');
            $endAt   = Carbon::parse($request->get('end') ?: $todayDate)
                ->lastOfMonth()
                ->format('Y-m-d');
            $filters = [
                'date' => [
                    'start_at' => $startAt,
                    'end_at'   => $endAt,
                ],
            ];
            $turns   = $this->turnRepository->getTurnList($filters);
            $bills   = $this->billRepository->getBillsList($filters);

            return response()->json(array_merge($turns, $bills));
        } catch (\Exception $e) {
            \Log::info($e->getMessage());

            return response()->json([
                'message' => 'Hubo un problema al cargar los datos',
            ], 422);
        }
    }

    public function getStats(Request $request)
    {
        try {

            $filters = $request->only([
                'date',
            ]);

            $profit        = $this->turnRepository->getTotalProfit($filters);
            $expensesTotal = $this->billRepository->getTotalExpenses($filters);

            return response()->json([
                'success'       => true,
                'profit'        => $profit[0]['total'],
                'expensesTotal' => $expensesTotal[0]['total'],
                'message'       => 'Datos cargados con éxito',
            ], 200);
        } catch (\Exception $e) {
            \Log::info($e->getMessage());

            return response()->json([
                'message' => 'Hubo un problema al cargar los datos',
            ], 422);
        }
    }

    public function eventsTotals(Request $request)
    {
        try {
            $getBy           = $request->get('by', 'week');
            $labels          = get_labels_by($getBy);
            $weeKTurnsTotals = array_fill(0, 7, 0);
            $weeKBillsTotals = array_fill(0, 7, 0);

            $filters = $request->only([
                'date',
            ]);

            $turnsEvents = $this->turnRepository->getTotals($filters);
            foreach ($turnsEvents as $eventCount) {
                $weeKTurnsTotals[$eventCount['day']] = $eventCount['count'];
            }

            $billsEvents = $this->billRepository->getTotals($filters);
            foreach ($billsEvents as $eventCount) {
                $weeKBillsTotals[$eventCount['day']] = $eventCount['count'];
            }

            return response()->json([
                'success'    => true,
                'labels'     => $labels,
                'countTurns' => $weeKTurnsTotals,
                'countBills' => $weeKBillsTotals,
                'message'    => 'Datos cargados con éxito',
            ], 200);
        } catch (\Exception $e) {
            \Log::info($e->getMessage());

            return response()->json([
                'message' => 'Hubo un problema al cargar los datos',
            ], 422);
        }
    }
}
