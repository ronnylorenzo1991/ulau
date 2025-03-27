<?php

namespace App\Http\Controllers;

use App\Repositories\Bill\BillRepository;
use App\Repositories\Turn\TurnRepository;
use App\Repositories\User\UserRepository;
use Carbon\Carbon;
use Illuminate\Http\Request;

class DashboardController extends Controller
{
    private TurnRepository $turnRepository;
    private BillRepository $billRepository;
    private UserRepository $userRepository;

    public function __construct(TurnRepository $turnRepository, BillRepository $billRepository, UserRepository $userRepository)
    {
        $this->turnRepository = $turnRepository;
        $this->billRepository = $billRepository;
        $this->userRepository = $userRepository;
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
                'turn',
                'clients',
                'date_range',
            ]);

            $filters['date_range'] = sanitize_date_range($filters['date_range']);

            $profit        = $this->turnRepository->getTotalProfit($filters);
            $expensesTotal = $this->billRepository->getTotalExpenses($filters);
            $clientsTotal  = $this->userRepository->getClientTotals();

            return response()->json([
                'success'       => true,
                'profit'        => $profit[0]['total'] ?? 0,
                'expensesTotal' => $expensesTotal[0]['total'] ?? 0,
                'clientsTotal'  => $clientsTotal ?? 0,
                'message'       => 'Datos cargados con éxito' ?? 0,
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
            $filters = $request->only([
                'turn',
                'clients',
                'date_range',
            ]);

            $turnsData                            = $this->turnRepository->getTotals($filters);
            $billsData                            = $this->billRepository->getTotals($filters);
            [$startDate, $endDate]                = sanitize_date_range($filters['date_range']);
            [$labels, $turnsTotals, $billsTotals] = $this->getChartLineData($startDate, $endDate, $turnsData, $billsData);
            return response()->json([
                'success'    => true,
                'labels'     => $labels,
                'countTurns' => $turnsTotals,
                'countBills' => $billsTotals,
                'message'    => 'Datos cargados con éxito',
            ], 200);
        } catch (\Exception $e) {
            \Log::info($e->getMessage());

            return response()->json([
                'message' => 'Hubo un problema al cargar los datos',
            ], 422);
        }
    }

    private function getChartLineData($startDate, $endDate, $turnsData, $billsData)
    {
        $withZeroTurns = [];
        $withZeroBills = [];

        if ($startDate->isSameDay($endDate)) {
            $labels = get_labels_by('day');

            // sanitize events list with zero events
            foreach (range(0, 23) as $hour) {
                $turn            = $turnsData->firstWhere('date', $hour);
                $withZeroTurns[] = [
                    'date'  => $hour,
                    'total' => $turn ? $turn->count : 0
                ];

                $bill            = $billsData->firstWhere('date', $hour);
                $withZeroBills[] = [
                    'date'  => $hour,
                    'total' => $bill ? $bill->count : 0
                ];
            }
        } else {
            while ($startDate <= $endDate) {
                $dates[] = $labels[] = $startDate->toDateString();
                $startDate->addDay();
            }

            foreach ($dates as $date) {
                $turn            = $turnsData->firstWhere('date', $date);
                $withZeroTurns[] = [
                    'date'  => $date,
                    'total' => $turn ? $turn->count : 0
                ];

                $bill            = $billsData->firstWhere('date', $date);
                $withZeroBills[] = [
                    'date'  => $date,
                    'total' => $bill ? $bill->count : 0
                ];
            }
        }

        $withZeroTurns = array_column($withZeroTurns, 'total');
        $withZeroBills = array_column($withZeroBills, 'total');

        return [$labels, $withZeroTurns, $withZeroBills];
    }
}
