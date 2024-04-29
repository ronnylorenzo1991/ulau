<?php

namespace App\Http\Controllers;

use App\Http\Requests\AuthRequest;
use App\Rules\CheckTokenRule;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Http\Resources\AuthResource;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\DB;
use App\Models\Nav;
use Illuminate\Support\Facades\Password;

class AuthController extends Controller
{
    public function register(Request $request)
    {
        $validatedData             = $request->validate([
            'email'    => 'email|required|unique:users',
            'password' => 'required',
            'name'     => 'required',
        ]);
        $validatedData['password'] = bcrypt($request->password);
        $user                      = User::create($validatedData);
        $token                     = $user->createToken("robot-token");
        return response(['user' => $user, 'access_token' => $token->plainTextToken]);
    }

    public function login(Request $request)
    {
        try {
            $request->validate([
                "user_email" => "required",
                "password"   => "required",
            ]);
            $field = filter_var($request->input('user_email'), FILTER_VALIDATE_EMAIL) ? 'email' : 'name';
            $request->merge([$field => $request->input('user_email')]);
            $user = User::where("email", $request->user_email)->orWhere('name', '=', $request->user_email)->first();
            if (!Auth::attempt($request->only($field, 'password'))) {
                return response()->json(['message' => __('auth.failed')], 401);
            }
            $tokenResult = auth()->user()->createToken('apiToken')->accessToken;

            return response()->json([
                "user"  => new AuthResource($user),
                'token' => "Bearer " . $tokenResult
            ], 200);
        } catch (Exception $error) {
            return response()->json([
                "message" => $error->getMessage(),
                "error"   => $error,
            ], 401);
        }
    }
    public function changePassword(Request $request)
    {
        // Validate the request data
        $validatedData = $request->validate([
            'old_password' => 'required',
            'new_password' => 'required|min:8|confirmed',
        ]);

        // Get the currently authenticated user
        $user = Auth::user();

        // Verify the old password
        if (!Hash::check($validatedData['old_password'], $user->password)) {
            return response()->json(['error' => 'el password no coincide'], 400);
        }

        // Update the user's new_password
        $user->password = Hash::make($validatedData['new_password']);
        $user->save();

        return response()->json(['message' => 'Contraseñá modificada correctamente'], 200);
    }

    public function forgot(AuthRequest $request)
    {
        Password::sendResetLink($request->only('email'));

        $json = [
            'title'   => 'Operación exitosa',
            'message' => 'Se ha enviado un correo para restablecer su contraseña',
        ];

        return response()->json($json, 200);
    }

    public function reset(Request $request)
    {
        $request->validate([
            'email'    => 'required|email|exists:users',
            'password' => 'required|min:8|confirmed',
            'token'    => ['required', new CheckTokenRule($request->email)],
        ]);

        User::where('email', $request->email)
            ->update(['password' => bcrypt($request->password)]);

        DB::table('password_resets')->where(['email' => $request->email])->delete();

        $json = [
            'title'   => 'Operación exitosa',
            'message' => 'Se ha restablecido la contraseña correctamente',
        ];
        return response()->json($json, 200);
    }
}
