<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Repositories\User\UserRepository;
use Illuminate\Http\Request;

class UserController extends Controller
{
    private UserRepository $userRepository;

    public function __construct(UserRepository $userRepository)
    {
        $this->userRepository = $userRepository;
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

            $users = $this->userRepository->getAll($sortBy, $sortDir, $perPage, $page, ['roles', 'center']);

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
        $user = $this->userRepository->create($request->all());

        return response()->json([
            'success' => true,
            'data'    => $user,
            'message' => 'Usuario creado éxito',
        ]);
    }

    public function update(Request $request, $id)
    {
        $userData = $request->all();

        $user = $this->userRepository->update($userData, $id);

        return response()->json([
            'success' => true,
            'data'    => $user,
            'message' => 'Usuario actualizado con éxito',
        ]);
    }

    public function destroy($id)
    {
        $this->userRepository->delete($id);

        return response()->json([
            'success' => true,
            'message' => 'Usuario eliminado con éxito',
        ]);
    }
}
