<?php

namespace App\Http\Controllers\Api\Dashboard;

use App\Helpers\ApiResponse;
use App\Http\Controllers\Controller;
use App\Http\Resources\Dashboard\CategoryResource;
use App\Http\Resources\Dashboard\ProductResource;
use App\Models\Category;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class CategoryController extends Controller
{
    /**
     * Display a listing of the resource.
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
