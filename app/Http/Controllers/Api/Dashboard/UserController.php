<?php

namespace App\Http\Controllers\Api\Dashboard;

use App\Helpers\ApiResponse;
use App\Http\Controllers\Controller;
use App\Http\Requests\AddUserRequest;
use App\Http\Resources\Dashboard\UserDataResource;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

class UserController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $users = User::with('roles:id')->get();
        if ($users->isEmpty()) {
            return ApiResponse::sendResponse(404, 'No users found', []);
        }
        return ApiResponse::sendResponse(200, 'Users retrieved successfully', UserDataResource::collection($users));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(AddUserRequest $request)
    {
        $data = $request->validated();
        if ($data) {
            $user = User::create([
                'name' => $data['name'],
                'email' => $data['email'],
                'password' => Hash::make($data['password']),
                'phone' => $data['phone'],
            ]);
            $user->addRole($data['role']);
        }

        return ApiResponse::sendResponse(201, 'User registered successfully', new UserDataResource($user));
    }

    /**
     * Display the specified resource.
     */
    public function show(User $user) {}

    /**
     * Update the specified resource in storage.
     */
    public function update(AddUserRequest $request, $id)
    {
        $user = User::find($id);
        if(!$user) {
            return ApiResponse::sendResponse(404, 'User not found', []);
        }
        $data = $request->validated();
        // dd($data);
        if ($data) {
            $user->update([
                'name' => $data['name'] ?? $user->name,
                'email' => $data['email'] ?? $user->email,
                'password' => isset($data['password']) ? Hash::make($data['password']) : $user->password,
                'phone' => $data['phone'] ?? $user->phone,
            ]);
            if (isset($data['role'])) {
                $user->syncRoles([$data['role']]);
            }
        }

        return ApiResponse::sendResponse(201, 'User updated successfully', new UserDataResource($user));
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy($id)
    {
        $user = User::find($id);
        if (!$user) {
            return ApiResponse::sendResponse(404, 'User not found', []);
        }
        $deleted = $user->delete();

        if ($deleted) {
            return ApiResponse::sendResponse(200, 'User deleted successfully', []);
        } else {
            return ApiResponse::sendResponse(500, 'Failed to delete user', []);
        }
    }
}
