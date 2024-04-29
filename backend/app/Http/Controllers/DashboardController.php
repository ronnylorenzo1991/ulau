<?php

namespace App\Http\Controllers;

use App\Models\Module;
use App\Repositories\Anomaly\AnomalyRepository;
use App\Repositories\Event\EventRepository;

use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class DashboardController extends Controller
{
    private AnomalyRepository $anomalyRepository;
    private EventRepository $eventRepository;

    public function __construct(
        AnomalyRepository $anomalyRepository,
        EventRepository $eventRepository,
    ) {
        $this->anomalyRepository = $anomalyRepository;
        $this->eventRepository   = $eventRepository;
    }

    public function eventImages(Request $request)
    {
        try {
            $last = $request->get('last', 10);

            $filters = $request->only([
                'date',
                'class_label',
            ]);

            $images = $this->eventRepository->getEventsLastImages($last, $filters);
            return response()->json([
                'success' => true,
                'images'  => array_values($images),
                'message' => 'Datos cargados con éxito',
            ], 200);
        } catch (\Exception $e) {
            \Log::info($e->getMessage());

            return response()->json([
                'message' => 'Hubo un problema al cargar los datos',
            ], 422);
        }
    }
    public function eventTotals(Request $request)
    {
        try {
            $getBy      = $request->get('by', 'week');
            $labels     = get_labels_by($getBy);
            $weeKTotals = array_fill(0, 7, 0);
           
            $filters    = $request->only([
                'date',
                'class_label',
            ]);

            $events = $this->eventRepository->getEventsTotals($filters);
            foreach ($events as $eventCount) {
                $weeKTotals[$eventCount['day']] = $eventCount['count'];
            }

            return response()->json([
                'success' => true,
                'labels'  => $labels,
                'count'   => $weeKTotals,
                'message' => 'Datos cargados con éxito',
            ], 200);
        } catch (\Exception $e) {
            \Log::info($e->getMessage());

            return response()->json([
                'message' => 'Hubo un problema al cargar los datos',
            ], 422);
        }
    }

    public function anomaliesByClass(Request $request)
    {
        try {
            $filters    = $request->only([
                'date',
                'class_label',
            ]);
            
            $labels    = [];
            $series    = [];
            $anomalies = $this->anomalyRepository->getAnomaliesByClass($filters);
            foreach ($anomalies as $anomaly) {
                $labels[] = $anomaly->label;
                $series[] = $anomaly->quantity;
            }
            return response()->json([
                'success' => true,
                'labels'  => $labels,
                'series'  => $series,
                'data'    => $anomalies,
                'message' => 'Datos cargados con éxito',
            ], 200);
        } catch (\Exception $e) {
            \Log::info($e->getMessage());

            return response()->json([
                'message' => 'Hubo un problema al cargar los datos',
            ], 422);
        }
    }
}
