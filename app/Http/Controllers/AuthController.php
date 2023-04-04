<?php

//namespace App\Http\Controllers;
//
//use App\Models\User;
//use Firebase\JWT\JWT;
//use Illuminate\Http\Request;
//use Illuminate\Support\Facades\Hash;
//use Illuminate\Support\Facades\Password;
//use Illuminate\Support\Facades\Validator;
//use Illuminate\Validation\ValidationException;
//
//class AuthController extends Controller
//{
//    /**
//     * Register a new user.
//     *
//     * @param \Illuminate\Http\Request $request
//     * @return \Illuminate\Http\JsonResponse
//     */
//    public function register(Request $request)
//    {
//        $validator = Validator::make($request->all(), [
//            'name' => 'required|string|max:255',
//            'email' => 'required|string|email|max:255|unique:users',
//            'phone' => 'required|string|unique:users,phone|regex:/^\+7[0-9]{10}$/i',
//            'password' => [
//                'required',
//                'confirmed',
//                Password::min(6)
//                    ->mixedCase()
//                    ->letters()
//                    ->numbers()
//                    ->symbols()
//            ],
//        ]);
//
//        if ($validator->fails()) {
//            return response()->json($validator->errors(), 422);
//        }
//
//        $user = User::create([
//            'name' => $request->name,
//            'email' => $request->email,
//            'phone' => $request->phone,
//            'password' => bcrypt($request->password),
//        ]);
//
//        $header = ['alg' => 'HS256', 'typ' => 'JWT', 'kid' => 'your-key-id'];
//        $payload = ['id' => $user->id];
//        $token = JWT::encode($payload, env('JWT_SECRET'), 'HS256', null, $header);
//
//        return response()->json([
//            'user' => $user,
//            'token' => $token,
//        ]);
//    }
//
//    /**
//     * Login user and create token
//     *
//     * @param \Illuminate\Http\Request $request
//     * @return \Illuminate\Http\JsonResponse
//     */
//    public function login(Request $request)
//    {
//
//        $validator = $request->validate([
//            'email' => 'required|string|email|max:255',
//            'password' => 'required|string|min:8|max:255'
//        ]);
//
//        $user = User::where('email', $request->email)->first();
//
//        if (!$user || !Hash::check($request->password, $user->password)) {
//            throw ValidationException::withMessages([
//                'email' => ['The provided credentials are incorrect.'],
//            ]);
//        }
//
//        $header = ['alg' => 'HS256', 'typ' => 'JWT', 'kid' => 'your-key-id'];
//        $payload = ['id' => $user->id];
//        $token = JWT::encode($payload, env('JWT_SECRET'), 'HS256', null, $header);
//
//        return response()->json([
//            'user' => $user,
//            'token' => $token
//        ]);
//    }
//
//}


namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\ValidationException;

class AuthController extends Controller
{
    public function register(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users',
            'phone' => 'required|string|unique:users,phone|regex:/^\+7[0-9]{10}$/i',
            'password' => [
                'required',
                'confirmed',
                'min:6',
                'regex:/^(?=.*[a-z])(?=.*[A-Z])(?=.*\d)(?=.*[@$!%*?&])[A-Za-z\d@$!%*?&]{6,}$/',
            ],
        ]);

        if ($validator->fails()) {
            return response()->json($validator->errors(), 422);
        }

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

    public function login(Request $request)
    {
        $request->validate([
            'email' => 'required|string|email|max:255',
            'password' => 'required|string|min:6|max:255',
        ]);

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
