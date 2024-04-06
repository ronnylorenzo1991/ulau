<?php

namespace App\Http\Controllers;

use App\Http\Requests\AnomalyTypesRequest;
use App\Repositories\AnomalyType\AnomalyTypeRepository;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class AnomalyTypeController extends Controller
{
    private AnomalyTypeRepository $anomalyTypeRepository;

    public function __construct(AnomalyTypeRepository $anomalyTypeRepository)
    {
        $this->anomalyTypeRepository = $anomalyTypeRepository;
    }

    /**
     * Get elements with pagination.
     */
    public function index(Request $request)
    {
        try {
            $sortBy = $request->get('sortBy') ?? 'id';
            $sortDir = $request->get('sortDir') ?? 'desc';

            $perPage = (int) $request->get('per_page');
            $page = (int) $request->get('page');

            $filters = $request->only([
                'name'
            ]);

            // dd($filters);
            $anomalyTypes = $this->anomalyTypeRepository->getAll($sortBy, $sortDir, $perPage, $page, [], $filters);


            return response()->json([
                'success' => true,
                'data'    => $anomalyTypes,
                'message' => 'Datos cargados con éxito',
            ]);
        } catch (\Exception $e) {
            \Log::info($e->getMessage());

            return response()->json([
                'message' => 'Hubo un problema al cargar los datos',
            ], 422);
        }
    }

    /**
     * Get List.
     */
    public function list()
    {
        try {
            $anomalyTypes = $this->anomalyTypeRepository->getList();

            return response()->json([
                'success' => true,
                'data'    => $anomalyTypes,
                'message' => 'Datos cargados con éxito',
            ]);
        } catch (\Exception $e) {
            \Log::info($e->getMessage());

            return response()->json([
                'message' => 'Hubo un problema al cargar los datos',
            ], 422);
        }
    }

    /**
     * Store a new element.
     */
    public function store(AnomalyTypesRequest $request): JsonResponse
    {
        try {
            $anomalyTypes = $this->anomalyTypeRepository->create($request);

            return response()->json([
                'success' => true,
                'data'    => $anomalyTypes,
                'message' => 'Elemento guardado con éxito',
            ]);
        } catch (\Exception $e) {
            \Log::info($e->getMessage());

            return response()->json([
                'message' => 'Hubo un problema al guardar los datos',
            ], 422);
        }
    }

    /**
     * Edit element.
     */
    public function update(AnomalyTypesRequest $request, int $id): JsonResponse
    {
        try {
            $anomalyType = $this->anomalyTypeRepository->update($request, $id);

            return response()->json([
                'success' => true,
                'data'    => $anomalyType,
                'message' => 'Elemento guardado con éxito',
            ]);
        } catch (\Exception $e) {
            \Log::info($e->getMessage());

            return response()->json([
                'message' => 'Hubo un problema al guardar los datos',
            ], 422);
        }
    }

    /**
     * Delete element.
     */
    public function destroy(int $id)
    {
        try {
            $this->anomalyTypeRepository->delete($id);

            return ['success' => true, 'data' => null, 'message' => 'Elemento eliminado con éxito'];
        } catch (\Exception $e) {
            \Log::info($e->getMessage());

            return response()->json([
                'message' => 'Hubo un problema al cargar los datos',
            ], 422);
        }
    }
}
