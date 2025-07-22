<?php

namespace App\Http\Controllers\Api\Dashboard;

use App\Helpers\ApiResponse;
use App\Http\Controllers\Controller;
use App\Http\Requests\ProductRequest;
use App\Http\Resources\Dashboard\ProductResource;
use App\Models\Product;

use App\Traits\Pagination;
use App\Traits\UploadImageTrait;

class ProductController extends Controller
{
    use Pagination;
    use UploadImageTrait;
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        // $products = Product::with('category')->Paginate(5);
        // $products = Product::with('category')->cursorPaginate(5);
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
    public function store(ProductRequest $request)
    {
        $data = $request->validated();
        if ($data == false) {
            return ApiResponse::sendResponse(422,  'fail to register, please try again', $data->errors()->all());
        }
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
        } else {
            return ApiResponse::sendResponse(422,  'There is no image uploaded', []);
        }

        // dd($data);

        if ($product->image) {
            $this->deleteImage('dashboard/assets/images/products/cards/', $product->image);
            $this->deleteImage('dashboard/assets/images/products/details/', $product->image);
        }
        $product->update([
            'product_name' => $data['product_name'],
            'price' => $data['price'],
            'description' => $data['description'],
            'category_id' => $data['category_id'],
            'image' => isset($image_name) ? $image_name : null
        ]);
        return ApiResponse::sendResponse(200, 'the product updated successfully', new ProductResource($product));
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Product $product)
    {
        // dd($product->image);
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
