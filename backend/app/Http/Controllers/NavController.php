<?php

namespace App\Http\Controllers;

use App\Repositories\Nav\NavRepository;
use Illuminate\Http\Request;

class NavController extends Controller
{
    private NavRepository $navRepository;

    public function __construct(NavRepository $navRepository)
    {
        $this->navRepository = $navRepository;
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

            $users = $this->navRepository->getAll($sortBy, $sortDir, $perPage, $page, ['navs', 'navs.permissions']);

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
        $navs = $this->navRepository->create($request->all());

        return response()->json([
            'success' => true,
            'data'    => $navs,
            'message' => 'Elemento creado satisfactoriamente',
        ]);
    }

    public function update(Request $request, $id)
    {
        $nav = $this->navRepository->update($request->all(), $id);

        return response()->json([
            'success' => true,
            'data'    => $nav,
            'message' => 'Elemento actualizado satisfactoriamente',
        ]);
    }


    public function destroy($id)
    {
        try {
            $nav = $this->navRepository->find($id, ['navs']);
            $nav->permissions()->sync([]);
            foreach($nav->navs as $subNav) {
                $subNav->permissions()->sync([]);
                $this->navRepository->delete($subNav->id);
            }

            $this->navRepository->delete($id);

            return ['success' => true, 'data' => null, 'message' => 'Elemento eliminado con éxito'];
        } catch (\Exception $e) {
            \Log::info($e->getMessage());

            return response()->json([
                'message' => 'Hubo un problema al eliminar los datos',
            ], 422);
        }
    }
}
