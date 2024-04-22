<?php

namespace App\Http\Controllers;

use App\Repositories\Event\EventRepository;
use Illuminate\Http\Request;

class EventController extends Controller
{
    private EventRepository $eventRepository;

    public function __construct(EventRepository $eventRepository)
    {
        $this->eventRepository = $eventRepository;
    }
    /**
     * Get elements with pagination.
     */
    public function index(Request $request)
    {
        try {
            $sortBy  = $request->get('sortBy') ?? 'id';
            $sortDir = $request->get('sortDir') ?? 'desc';

            $perPage = (int) $request->get('per_page');
            $page    = (int) $request->get('page');

            $events = $this->eventRepository->getAll($sortBy, $sortDir, $perPage, $page, ['anomalies']);

            return response()->json([
                'success' => true,
                'data'    => $events,
                'message' => 'Datos cargados con éxito',
            ]);
        } catch (\Exception $e) {
            \Log::info($e->getMessage());

            return response()->json([
                'message' => 'Hubo un problema al cargar los datos',
            ], 422);
        }
    }

    public function store(Request $request)
    {
        try {
            $file = $request->file('file') ?? null;
            $data = $request->get('params') ?? null;
            $event = $this->eventRepository->store($data, $file);

            return json_encode(['event_id' => $event->id ?? null, 'message' => 'Evento procesado satisfactoriamente']);
        } catch (\Exception $e) {
            \Log::info($e);

            return json_encode(['event_id' => null ?? null, 'message' => $e]);
        }
    }
}
