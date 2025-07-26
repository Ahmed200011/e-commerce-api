<?php

namespace App\Http\Controllers\Api\ecommerce;

use App\Helpers\ApiResponse;
use App\Http\Controllers\Controller;
use App\Http\Resources\Dashboard\CategoryResource;
use App\Http\Resources\Dashboard\ProductResource;
use App\Models\Banner;
use App\Models\Category;
use App\Models\Product;
use App\Traits\Pagination;

class HomeController extends Controller
{
    use Pagination;


    public function index()
    {
        // $Categories = Category::all();
        $banner = Banner::all();
        $Products = Product::orderBy('created_at', 'desc')->take(20)->get();;
        $categories = Category::with(['products'])->get();



        return ApiResponse::sendResponse(200, 'all date retrieved', ['banner' => $banner, 'categories' => CategoryResource::collection($categories), 'products' => ProductResource::collection($Products)]);
    }
    public function bannerShowDetails($id)
    {

        $banner = Banner::find($id);

        if (!$banner) {
            return ApiResponse::sendResponse(404, 'No product associated with this banner', []);
        }
        $product = $banner->product;
        return ApiResponse::sendResponse(200, 'product retrieved successfully', new ProductResource($product));
    }
    public function productShowDetails($id)
    {

        $product = Product::find($id);

        if (!$product) {
            return ApiResponse::sendResponse(404, 'No details associated with this product', []);
        }

        return ApiResponse::sendResponse(200, 'product retrieved successfully', new ProductResource($product));
    }
    public function categoryShowDetails($id)
    {

        $category = Category::find($id);

        if (!$category) {
            return ApiResponse::sendResponse(404, 'No details associated with this category', []);
        }
        $products = $category->products()->orderBy('id')->cursorPaginate(20);
        if ($products->isEmpty()) {
            return ApiResponse::sendResponse(404, 'No products found in this category.', []);
        }
        $customData = [
            'Rows' => ProductResource::collection($products),
            'pagination' => $this->formatPagination($products)
        ];
        return ApiResponse::sendResponse(200, 'products retrieved successfully',  $customData);
    }
}
