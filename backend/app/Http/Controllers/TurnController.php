<?php

namespace App\Http\Controllers;

use App\Repositories\Turn\TurnRepository;
use Illuminate\Http\Request;

class TurnController extends Controller
{
    private TurnRepository $turnRepository;

    public function __construct(TurnRepository $turnRepository)
    {
        $this->turnRepository = $turnRepository;
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

            $turns = $this->turnRepository->getAll($sortBy, $sortDir, $perPage, $page, []);

            return response()->json([
                'success' => true,
                'data'    => $turns,
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
            $turn = $this->turnRepository->create($request);
            \Log::info($request->all());
            return response()->json([
                'success' => true,
                'data'    => $turn,
                'message' => 'Elemento guradado con éxito',
            ]);
        } catch (\Exception $e) {
            \Log::info($e);

            return json_encode(['event_id' => null ?? null, 'message' => $e]);
        }
    }

    public function update(Request $request, $id)
    {
        $turn = $this->turnRepository->update($request, $id);

        return response()->json([
            'success' => true,
            'data'    => $turn,
            'message' => 'Elemento actualizado satisfactoriamente',
        ]);
    }

    public function destroy($id)
    {
        $this->turnRepository->delete($id);

        return response()->json([
            'success' => true,
            'message' => 'Elemento eliminado con éxito',
        ]);
    }
    public function cancel($id)
    {
        $this->turnRepository->cancel($id);

        return response()->json([
            'success' => true,
            'message' => 'Elemento cancelado con éxito',
        ]);
    }

    public function complete(Request $request, $id)
    {
        $this->turnRepository->complete($id, $request);

        return response()->json([
            'success' => true,
            'message' => 'Elemento cancelado con éxito',
        ]);
    }
}
