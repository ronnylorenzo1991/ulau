<?php

namespace App\Http\Controllers;

use App\Http\Requests\BillRequest;
use App\Repositories\Bill\BillRepository;
use Illuminate\Http\Request;

class BillController extends Controller
{
    private BillRepository $billRepository;

    public function __construct(BillRepository $billRepository)
    {
        $this->billRepository = $billRepository;
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

            $bills = $this->billRepository->getAll($sortBy, $sortDir, $perPage, $page, []);

            return response()->json([
                'success' => true,
                'data'    => $bills,
                'message' => 'Datos cargados con éxito',
            ]);
        } catch (\Exception $e) {
            \Log::info($e->getMessage());

            return response()->json([
                'message' => 'Hubo un problema al cargar los datos',
            ], 422);
        }
    }

    public function store(BillRequest $request)
    {
        try {
            $bill = $this->billRepository->create($request);
            return response()->json([
                'success' => true,
                'data'    => $bill,
                'message' => 'Elemento guradado con éxito',
            ]);
        } catch (\Exception $e) {
            \Log::info($e);

            return json_encode(['event_id' => null ?? null, 'message' => $e]);
        }
    }

    public function update(BillRequest $request, $id)
    {
        $bill = $this->billRepository->update($request, $id);

        return response()->json([
            'success' => true,
            'data'    => $bill,
            'message' => 'Elemento actualizado satisfactoriamente',
        ]);
    }

    public function destroy($id)
    {
        $this->billRepository->delete($id);

        return response()->json([
            'success' => true,
            'message' => 'Elemento eliminado con éxito',
        ]);
    }
}
