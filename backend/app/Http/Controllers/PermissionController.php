<?php

namespace App\Http\Controllers;

use App\Repositories\Permission\PermissionRepository;
use Illuminate\Http\Request;

class PermissionController extends Controller
{
    private PermissionRepository $permissionRepository;

    public function __construct(PermissionRepository $permissionRepository)
    {
        $this->permissionRepository = $permissionRepository;
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

            $permissions = $this->permissionRepository->getAll($sortBy, $sortDir, $perPage, $page, []);

            return response()->json([
                'success' => true,
                'data'    => $permissions,
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
        $permission = $this->permissionRepository->create($request);

        return response()->json([
            'success' => true,
            'data'    => $permission,
            'message' => 'Elemento guardado satisfactoriamente',
        ]);
    }

    public function update(Request $request, $id)
    {
        $permission = $this->permissionRepository->update($request, $id);

        return response()->json([
            'success' => true,
            'data'    => $permission,
            'message' => 'Elemento actualizado satisfactoriamente',
        ]);
    }

    public function destroy($id)
    {
        try {
            $this->permissionRepository->delete($id);

            return ['success' => true, 'message' => 'Elemento eliminado con éxito'];
        } catch (\Exception $e) {
            \Log::info($e->getMessage());

            return response()->json([
                'message' => 'Hubo un problema al eliminar los datos',
            ], 422);
        }
    }
}
