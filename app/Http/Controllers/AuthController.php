<?php

namespace App\Http\Controllers;

use App\Http\Requests\Api\AuthUserRequest;
use App\Models\User;
use App\Http\Requests\Api\RegisterUserRequest;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\ValidationException;

class AuthController extends Controller
{
    public function register(RegisterUserRequest $request)
    {
        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'phone' => $request->phone,
            'password' => bcrypt($request->password),
        ]);

        $token = $user->createToken('token');

        return response()->json([
            'user' => $user,
            'token' => $token->accessToken,
            'token_type' => 'Bearer',
        ]);
    }

    /**
     * @throws ValidationException
     */
    public function login(AuthUserRequest $request): \Illuminate\Http\JsonResponse
    {

        $user = User::where('email', $request->email)->first();

        if (!$user || !Hash::check($request->password, $user->password)) {
            throw ValidationException::withMessages([
                'email' => ['The provided credentials are incorrect.'],
            ]);
        }

        $token = $user->createToken('token')->plainTextToken;

        return response()->json([
            'user' => $user,
            'token' => $token,
            'token_type' => 'Bearer',
        ]);
    }
}
