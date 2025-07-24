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

class AuthController extends Controller
{
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
