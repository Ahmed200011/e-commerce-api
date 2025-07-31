<?php

namespace App\Http\Controllers\Api\Dashboard;

use App\Helpers\ApiResponse;
use App\Http\Controllers\Controller;
use App\Http\Requests\ProductRequest;
use App\Http\Resources\Dashboard\ProductResource;
use App\Models\Product;
use OpenApi\Annotations as OA;
use App\Traits\Pagination;
use App\Traits\UploadImageTrait;

class ProductController extends Controller
{
    use Pagination;
    use UploadImageTrait;
    /**
     * Display a listing of the resource.
     */
    /**
     * @OA\Get(
     *     path="/dashboard/products",
     *     tags={"Dashboard - Product"},
     *     summary="Get all products with category and pagination",
     *     security={{"sanctum":{}}},
     *     @OA\Response(
     *         response=200,
     *         description="All products retrieved with pagination"
     *     ),
     *     @OA\Response(
     *         response=400,
     *         description="No products found"
     *     )
     * )
     */

    public function index()
    {
        $products = Product::with('category')->orderBy('id')->cursorPaginate(20);
        if (!$products) {

            return ApiResponse::sendResponse(400, 'no products found', []);
        }
        $customData = [
            'Rows' => ProductResource::collection($products),
            // 'Rows'=>$products,
            'pagination' => $this->formatPagination($products)
        ];
        // return $products;
        return ApiResponse::sendResponse(200, 'all products retrieved', $customData);
        // return ApiResponse::sendResponse(200, 'all products retrieved', ProductResource::collection($products));
    }

    /**
     * Store a newly created resource in storage.
     */

    /**
     * @OA\Post(
     *     path="/dashboard/products",
     *     tags={"Dashboard - Product"},
     *     summary="Create a new product",
     *     security={{"sanctum":{}}},
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\MediaType(
     *             mediaType="multipart/form-data",
     *             @OA\Schema(
     *                 required={"product_name", "price", "description", "category_id", "image"},
     *                 @OA\Property(property="product_name", type="string", example="iPhone 14"),
     *                 @OA\Property(property="price", type="number", format="float", example=999.99),
     *                 @OA\Property(property="description", type="string", example="Latest Apple phone"),
     *                 @OA\Property(property="category_id", type="integer", example=3),
     *                 @OA\Property(property="image", type="file", format="binary")
     *             )
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Product created successfully"
     *     ),
     *     @OA\Response(
     *         response=422,
     *         description="Validation failed or image missing"
     *     )
     * )
     */

    public function store(ProductRequest $request)
    {
        $data = $request->validated();
        if ($data == false) {
            return ApiResponse::sendResponse(422,  'fail to register, please try again', $data->errors()->all());
        }
        // dd($data);
        // dd($request->all());

        if ($request->hasFile('image')) {
            $file = $request->file('image');
            $image_name = uniqid() . '_' . $file->getClientOriginalName();

            $this->uploadImage($file, 'dashboard/assets/images/products/cards/', $image_name, 600, 600,);
            $this->uploadImage($file, 'dashboard/assets/images/products/details/', $image_name, 800, 1000);
        } else {
            return ApiResponse::sendResponse(422,  'There is no image uploaded', []);
        }
        // dd($data);
        $product = Product::create([
            'product_name' => $data['product_name'],
            'price' => $data['price'],
            'description' => $data['description'],
            'category_id' => $data['category_id'],
            'image' => isset($image_name) ? $image_name : null
        ]);
        return ApiResponse::sendResponse(200, 'the product created successfully', new ProductResource($product));
    }

    /**
     * Display the specified resource.
     */
    /**
     * @OA\Get(
     *     path="/dashboard/products/{id}",
     *     tags={"Dashboard - Product"},
     *     summary="Get a single product by ID",
     *     security={{"sanctum":{}}},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         required=true,
     *         description="Product ID",
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Product retrieved successfully"
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="Product not found"
     *     )
     * )
     */

    public function show(Product $product)
    {
        if (!$product) {
            return ApiResponse::sendResponse(404, 'product not found', []);
        }
        return ApiResponse::sendResponse(200, 'product retrieved successfully', new ProductResource($product));
    }

    /**
     * Update the specified resource in storage.
     */
    /**
     * @OA\Put(
     *     path="/dashboard/products/{id}",
     *     tags={"Dashboard - Product"},
     *     summary="Update an existing product",
     *     security={{"sanctum":{}}},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         required=true,
     *         description="Product ID",
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\MediaType(
     *             mediaType="multipart/form-data",
     *             @OA\Schema(
     *                 @OA\Property(property="product_name", type="string", example="iPhone 15"),
     *                 @OA\Property(property="price", type="number", format="float", example=1099.99),
     *                 @OA\Property(property="description", type="string", example="Updated phone"),
     *                 @OA\Property(property="category_id", type="integer", example=4),
     *                 @OA\Property(property="image", type="file", format="binary")
     *             )
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Product updated successfully"
     *     ),
     *     @OA\Response(
     *         response=422,
     *         description="Validation failed"
     *     )
     * )
     */
    public function update(ProductRequest $request, Product $product)
    {
        $data = $request->validated();
        if ($data == false) {
            return ApiResponse::sendResponse(422, 'fail to register, please try again', $data->errors()->all());
        }
        if ($request->hasFile('image')) {
            $file = $request->file('image');
            $image_name = uniqid() . '_' . $file->getClientOriginalName();

            $this->uploadImage($file, 'dashboard/assets/images/products/cards/', $image_name, 600, 600,);
            $this->uploadImage($file, 'dashboard/assets/images/products/details/', $image_name, 800, 1000);
            if ($product->image) {
                $this->deleteImage('dashboard/assets/images/products/cards/', $product->image);
                $this->deleteImage('dashboard/assets/images/products/details/', $product->image);
            }
        }
        $product->update([
            'product_name' => $data['product_name'] ? $data['product_name'] : $product->product_name,
            'price' => $data['price'] ? $data['price'] : $product->price,
            'description' => $data['description'] ? $data['description'] : $product->description,
            'category_id' => $data['category_id'] ? $data['category_id'] : $product->category_id,
            'image' => isset($image_name) ? $image_name : $product->image
        ]);
        return ApiResponse::sendResponse(200, 'the product updated successfully', new ProductResource($product));
    }

    /**
     * Remove the specified resource from storage.
     */

    /**
     * @OA\Delete(
     *     path="/dashboard/products/{id}",
     *     tags={"Dashboard - Product"},
     *     summary="Delete a product by ID",
     *     security={{"sanctum":{}}},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         required=true,
     *         description="Product ID",
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Product deleted successfully"
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="Product not found"
     *     )
     * )
     */

    public function destroy($id)
    {
        $product = Product::find($id);
        if (!$product) {
            return ApiResponse::sendResponse(404, 'product not found', []);
        }
        if ($product->image) {
            $this->deleteImage('dashboard/assets/images/products/cards/', $product->image);
            $this->deleteImage('dashboard/assets/images/products/details/', $product->image);
        }

        $deleted = $product->delete();
        if ($deleted) {
            return ApiResponse::sendResponse(200, 'product deleted successfully', []);
        } else {
            return ApiResponse::sendResponse(500, 'product to delete user', []);
        }
    }
}
