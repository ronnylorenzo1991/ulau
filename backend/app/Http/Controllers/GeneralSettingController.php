<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\GeneralSetting;
use App\Repositories\GeneralSetting\GeneralSettingRepository;

class GeneralSettingController extends Controller
{
    private GeneralSettingRepository $generalSettingRepository;

    public function __construct(GeneralSettingRepository $generalSettingRepository)
    {
        $this->generalSettingRepository = $generalSettingRepository;
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

            $allGeneralSettings = $this->generalSettingRepository->getAll($sortBy, $sortDir, $perPage, $page, ['general_settings']);

            return response()->json([
                'success' => true,
                'data'    => $allGeneralSettings,
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
        $generalSettings = $this->generalSettingRepository->create($request->all());

        return response()->json([
            'success' => true,
            'data'    => $generalSettings,
            'message' => 'Elemento creado satisfactoriamente',
        ]);
    }

    public function update(Request $request, $id)
    {
        $generalSettings = $this->generalSettingRepository->update($request->all(), $id);

        return response()->json([
            'success' => true,
            'data'    => $generalSettings,
            'message' => 'Elemento actualizado satisfactoriamente',
        ]);
    }


    public function destroy($id)
    {
        try {
            $generalSettings = $this->generalSettingRepository->find($id);

            $this->generalSettingRepository->delete($generalSettings->id);

            return ['success' => true, 'data' => null, 'message' => 'Elemento eliminado con éxito'];
        } catch (\Exception $e) {
            \Log::info($e->getMessage());

            return response()->json([
                'message' => 'Hubo un problema al eliminar los datos',
            ], 422);
        }
    }
}
