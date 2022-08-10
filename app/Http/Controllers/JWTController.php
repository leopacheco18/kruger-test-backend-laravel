<?php

namespace App\Http\Controllers;

use Auth;
use Validator;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class JWTController extends Controller
{
    /**
     * Create a new AuthController instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('auth:api', ['except' => ['login', 'createAdmin']]);
    }

    /**
     * Register admin.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function createAdmin(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|string',
            'lastname' => 'required|string',
            'email' => 'required|string|email',
            'cedula' => 'required',
            'user' => 'required|string',
            'password' => 'required|string'
        ]);

        if ($validator->fails()) {
            return response()->json($validator->errors(), 422);
        }

        $user = User::create([
            'id_role' => 1,
            'name' => $request->name,
            'lastname' => $request->lastname,
            'email' => $request->email,
            'cedula' => $request->cedula,
            'user' => $request->user,
            'password' => Hash::make($request->password)
        ]);

        return response()->json([
            'message' => 'User successfully registered',
            'user' => $user
        ], 201);
    }

    /**
     * login user
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function login(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'user' => 'required|string',
            'password' => 'required|string',
        ]);

        if ($validator->fails()) {
            return response()->json($validator->errors(), 422);
        }

        if (!$token = auth()->attempt(['user'=>$request->user, 'password'=>$request->password])) {
            return response()->json(['error' => 'Unauthorized']);
        }

        return $this->respondWithToken($token);
    }



    /**
     * Logout user
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function logout()
    {
        auth()->logout();

        return response()->json(['message' => 'User successfully logged out.']);
    }

    /**
     * Get the token array structure.
     *
     * @param  string $token
     *
     * @return \Illuminate\Http\JsonResponse
     */
    protected function respondWithToken($token)
    {
        return response()->json([
            'access_token' => $token,
            'token_type' => 'bearer',
            'expires_in' => auth()->factory()->getTTL() * 60,
            'user' => auth()->user()
        ]);
    }
}
