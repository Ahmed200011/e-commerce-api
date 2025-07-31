<?php

namespace App\Http\Controllers\Api\Dashboard;

use App\Helpers\ApiResponse;
use App\Http\Controllers\Controller;
use App\Http\Requests\AddUserRequest;
use App\Http\Resources\Dashboard\UserDataResource;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use OpenApi\Annotations as OA;


class UserController extends Controller
{
    /**
     * Display a listing of the resource.
     */

    /**
 * @OA\Get(
 *     path="/dashboard/users",
 *     tags={"Dashboard - User"},
 *     summary="Get list of all users with their roles",
 *     security={{"sanctum":{}}},
 *     @OA\Response(
 *         response=200,
 *         description="Users retrieved successfully"
 *     ),
 *     @OA\Response(
 *         response=404,
 *         description="No users found"
 *     )
 * )
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
    /**
 * @OA\Post(
 *     path="/dashboard/users",
 *     tags={"Dashboard - User"},
 *     summary="Register a new user with a role",
 *     security={{"sanctum":{}}},
 *     @OA\RequestBody(
 *         required=true,
 *         @OA\JsonContent(
 *             required={"name", "email", "password", "phone", "role"},
 *             @OA\Property(property="name", type="string", example="Ahmed Rizk"),
 *             @OA\Property(property="email", type="string", format="email", example="ahmed@example.com"),
 *             @OA\Property(property="password", type="string", format="password", example="12345678"),
 *             @OA\Property(property="phone", type="string", example="01009198079"),
 *             @OA\Property(property="role", type="string", example="admin")
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
    /**
 * @OA\Put(
 *     path="/dashboard/users/{id}",
 *     tags={"Dashboard - User"},
 *     summary="Update user data and role",
 *     security={{"sanctum":{}}},
 *     @OA\Parameter(
 *         name="id",
 *         in="path",
 *         required=true,
 *         description="User ID",
 *         @OA\Schema(type="integer")
 *     ),
 *     @OA\RequestBody(
 *         required=true,
 *         @OA\JsonContent(
 *             @OA\Property(property="name", type="string", example="Ahmed Updated"),
 *             @OA\Property(property="email", type="string", example="updated@example.com"),
 *             @OA\Property(property="password", type="string", example="newpassword"),
 *             @OA\Property(property="phone", type="string", example="01000000000"),
 *             @OA\Property(property="role", type="string", example="editor")
 *         )
 *     ),
 *     @OA\Response(
 *         response=201,
 *         description="User updated successfully"
 *     ),
 *     @OA\Response(
 *         response=404,
 *         description="User not found"
 *     )
 * )
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
    /**
 * @OA\Delete(
 *     path="/dashboard/users/{id}",
 *     tags={"Dashboard - User"},
 *     summary="Delete user by ID",
 *     security={{"sanctum":{}}},
 *     @OA\Parameter(
 *         name="id",
 *         in="path",
 *         required=true,
 *         description="User ID",
 *         @OA\Schema(type="integer")
 *     ),
 *     @OA\Response(
 *         response=200,
 *         description="User deleted successfully"
 *     ),
 *     @OA\Response(
 *         response=404,
 *         description="User not found"
 *     )
 * )
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
