<?php

namespace App\Http\Controllers\Api\ecommerce;

use App\Events\ContactUsEvent;
use App\Helpers\ApiResponse;
use App\Http\Controllers\Controller;
use App\Http\Requests\ProductRequest;
use App\Http\Resources\Dashboard\CategoryResource;
use App\Http\Resources\Dashboard\ProductResource;
use App\Models\Banner;
use App\Models\Category;
use App\Models\ContactUs;
use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class HomeController extends Controller
{
    public function index()
    {
        // $Categories = Category::all();
        $banner = Banner::all();
        $Products = Product::all();
        $categories = Category::with([ 'products'])->get();



        return ApiResponse::sendResponse(200, 'all date retrieved', ['banner' => $banner, 'categories' =>CategoryResource::collection( $categories), 'products' =>ProductResource::collection( $Products)]);
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
        $product = $category->products;
        return ApiResponse::sendResponse(200, 'category retrieved successfully',  ProductResource::collection($product));
    }
}
