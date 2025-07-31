<?php

namespace App\Http\Controllers\Api\Dashboard;

use App\Helpers\ApiResponse;
use App\Http\Controllers\Controller;
use App\Http\Resources\Dashboard\CategoryResource;
use App\Http\Resources\Dashboard\ProductResource;
use App\Models\Category;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use OpenApi\Annotations as OA;


class CategoryController extends Controller
{
    /**
     * Display a listing of the resource.
     */

    /**
 * @OA\Get(
 *     path="/dashboard/categories",
 *     tags={"Dashboard - Category"},
 *     summary="Get all categories with parent, children, and products",
 *     security={{"sanctum":{}}},
 *     @OA\Response(
 *         response=200,
 *         description="All categories retrieved successfully"
 *     ),
 *     @OA\Response(
 *         response=400,
 *         description="No categories found"
 *     )
 * )
 */

    public function index()
    {
        $data = Category::with(['parent', 'children', 'products'])->get();
        if (!$data) {

            return ApiResponse::sendResponse(400, 'the category not found', []);
        }
        return ApiResponse::sendResponse(200, 'all category retrieved', CategoryResource::collection($data));
    }

    /**
     * Store a newly created resource in storage.
     */

    /**
 * @OA\Post(
 *     path="/dashboard/categories",
 *     tags={"Dashboard - Category"},
 *     summary="Create a new category",
 *     security={{"sanctum":{}}},
 *     @OA\RequestBody(
 *         required=true,
 *         @OA\JsonContent(
 *             required={"category_name"},
 *             @OA\Property(property="category_name", type="string", example="Laptops"),
 *             @OA\Property(property="parent_id", type="integer", example=1)
 *         )
 *     ),
 *     @OA\Response(
 *         response=200,
 *         description="Category added successfully"
 *     ),
 *     @OA\Response(
 *         response=422,
 *         description="Validation error"
 *     )
 * )
 */

    public function store(Request $request)
    {
        $validate = Validator::make($request->all(), [
            'category_name' => 'required|string|max:255',
            'parent_id' => 'nullable|exists:categories,id',
        ]);
        // dd($data);
        if ($validate == false) {
            return ApiResponse::sendResponse(422, 'fail to register, please try again', $validate->errors()->all());
        }
        $cat = Category::create([
            'category_name' => $request->category_name,
            'parent_id' => $request->parent_id,
        ]);
        return ApiResponse::sendResponse(200, 'the Category added successfully', new CategoryResource($cat));
    }

    /**
     * Display the specified resource.
     */
    public function show(Category $category)
    {

    }

    /**
     * Update the specified resource in storage.
     */

    /**
 * @OA\Put(
 *     path="/dashboard/categories/{id}",
 *     tags={"Dashboard - Category"},
 *     summary="Update an existing category",
 *     security={{"sanctum":{}}},
 *     @OA\Parameter(
 *         name="id",
 *         in="path",
 *         description="Category ID",
 *         required=true,
 *         @OA\Schema(type="integer")
 *     ),
 *     @OA\RequestBody(
 *         required=true,
 *         @OA\JsonContent(
 *             required={"category_name"},
 *             @OA\Property(property="category_name", type="string", example="Phones"),
 *             @OA\Property(property="parent_id", type="integer", example=2)
 *         )
 *     ),
 *     @OA\Response(
 *         response=200,
 *         description="Category updated successfully"
 *     ),
 *     @OA\Response(
 *         response=422,
 *         description="Validation error"
 *     )
 * )
 */

    public function update(Request $request, Category $category)
    {
        // dd($request->all());
        $validate = Validator::make($request->all(), [
            'category_name' => 'required|string|max:255',
            'parent_id' => 'nullable|exists:categories,id',
        ]);
        if ($validate == false) {
            return ApiResponse::sendResponse(422, 'fail to register, please try again', $validate->errors()->all());
        }
        // dd($request);
        $category->update([
            'category_name' => $request->category_name,
            'parent_id' => $request->parent_id,
        ]);
        return ApiResponse::sendResponse(200, 'the Category updated successfully', new CategoryResource($category));
    }


    /**
     * Remove the specified resource from storage.
     */

    /**
 * @OA\Delete(
 *     path="/dashboard/categories/{id}",
 *     tags={"Dashboard - Category"},
 *     summary="Delete a category by ID",
 *     security={{"sanctum":{}}},
 *     @OA\Parameter(
 *         name="id",
 *         in="path",
 *         description="Category ID",
 *         required=true,
 *         @OA\Schema(type="integer")
 *     ),
 *     @OA\Response(
 *         response=200,
 *         description="Category deleted successfully"
 *     ),
 *     @OA\Response(
 *         response=404,
 *         description="Category not found"
 *     )
 * )
 */

    public function destroy($id)
    {
        $category = Category::find($id);
        if (!$category) {
            return ApiResponse::sendResponse(404, 'the category not found', []);
        }
        $deleted = $category->delete();

        if ($deleted) {
            return ApiResponse::sendResponse(200, 'category deleted successfully', []);
        } else {
            return ApiResponse::sendResponse(500, 'category to delete user', []);
        }
    }
}
