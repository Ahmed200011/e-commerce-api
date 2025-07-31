<?php

namespace App\Http\Controllers\Api;


use App\Events\SendMailEvent;
use App\Helpers\ApiResponse;
use App\Http\Controllers\Controller;
use App\Http\Requests\RegisterRequest;
use App\Http\Resources\UserResource;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use OpenApi\Annotations as OA;

class AuthController extends Controller
{
    /**
 * @OA\Post(
 *     path="/register",
 *     tags={"Authentication"},
 *     summary="Register a new user",
 *     @OA\RequestBody(
 *         required=true,
 *         @OA\JsonContent(
 *             required={"name", "email", "password", "phone"},
 *             @OA\Property(property="name", type="string", example="Ahmed Rizk"),
 *             @OA\Property(property="email", type="string", format="email", example="ahmed@example.com"),
 *             @OA\Property(property="password", type="string", format="password", example="12345678"),
 *             @OA\Property(property="phone", type="string", example="01009198079")
 *         )
 *     ),
 *     @OA\Response(
 *         response=201,
 *         description="User registered successfully"
 *     ),
 *     @OA\Response(
 *         response=422,
 *         description="Validation failed"
 *     )
 * )
 */

    public function register(RegisterRequest $request)
    {
        $data = $request->validated();
        if ($data) {
            $user = User::create([
                'name' => $data['name'],
                'email' => $data['email'],
                'password' => Hash::make($data['password']),
                'phone' => $data['phone'],
            ]);
            $user->addRole('user');
        }
        $user['token_name'] = 'register_token';


        if($user){
            // dd(Auth::user()->id);
        event(new SendMailEvent( $user));
    }
        return ApiResponse::sendResponse(201, 'User registered successfully', new UserResource($user));
    }

    /**
 * @OA\Post(
 *     path="/login",
 *     tags={"Authentication"},
 *     summary="Login and get token",
 *     @OA\RequestBody(
 *         required=true,
 *         @OA\JsonContent(
 *             required={"email", "password"},
 *             @OA\Property(property="email", type="string", format="email", example="ahmed@example.com"),
 *             @OA\Property(property="password", type="string", format="password", example="12345678")
 *         )
 *     ),
 *     @OA\Response(
 *         response=200,
 *         description="Login successful"
 *     ),
 *     @OA\Response(
 *         response=401,
 *         description="Unauthorized - wrong credentials"
 *     )
 * )
 */

    public function login(Request $request)
    {
        $data = $request->validate([
            'email' => 'required|email',
            'password' => 'required|string|min:8',
        ]);
        $credentials = $request->only('email', 'password');
        if (Auth::attempt($credentials)) {
            $user = Auth::user();
            $user['token_name'] = 'login_token';

            return ApiResponse::sendResponse(200, 'Login success', new UserResource($user));
            // dd($user);
        } else {
            return ApiResponse::sendResponse(401, 'Unauthorized ,Email or password is not correct ', []);
        }
    }

    /**
 * @OA\Post(
 *     path="/logout",
 *     tags={"Authentication"},
 *     summary="Logout current user",
 *     security={{"sanctum":{}}},
 *     @OA\Response(
 *         response=200,
 *         description="Logout successful"
 *     ),
 *     @OA\Response(
 *         response=401,
 *         description="Unauthorized"
 *     )
 * )
 */

    public function logout(Request $request)
    {
        $user = $request->user();
        if ($user) {
            $user->currentAccessToken()->delete();
            return ApiResponse::sendResponse(200, 'Logout successful', []);
        }
        return ApiResponse::sendResponse(401, 'Unauthorized', []);
    }
}
