<?php

namespace App\Http\Controllers;

use App\Repositories\Role\RoleRepository;
use Illuminate\Http\Request;

class RoleController extends Controller
{
    private RoleRepository $roleRepository;

    public function __construct(RoleRepository $roleRepository)
    {
        $this->roleRepository = $roleRepository;
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

            $users = $this->roleRepository->getAll($sortBy, $sortDir, $perPage, $page, ['permissions']);

            return response()->json([
                'success' => true,
                'data'    => $users,
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
        $role = $this->roleRepository->create($request->all());

        return response()->json([
            'success' => true,
            'data'    => $role,
            'message' => 'Rol creado éxito',
        ]);
    }

    public function togglePermission(Request $request, $id)
    {
        try {
            $value = $request->get('value');
            $permission = $request->get('permission_name');
            $this->roleRepository->togglePermission($id, $permission, $value);
            return response()->json([
                'success' => true,
                'message' => 'Permiso actualizado con éxito',
            ]);
        } catch (\Exception $e) {
            \Log::info($e->getMessage());

            return response()->json([
                'message' => 'Hubo un problema al cargar los datos',
            ], 422);
        }
    }

    public function update(Request $request, $id)
    {
        $role = $this->roleRepository->update($request, $id);

        return response()->json([
            'success' => true,
            'data'    => $role,
            'message' => 'Rol actualizado con éxito',
        ]);
    }

    public function destroy($id)
    {
        $this->roleRepository->delete($id);

        return response()->json([
            'success' => true,
            'message' => 'Usuario eliminado con éxito',
        ]);
    }
}
