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
use OpenApi\Annotations as OA;


class HomeController extends Controller
{
    use Pagination;

/**
 * @OA\Get(
 *     path="/e_commerce/home",
 *     tags={"E-Commerce - Home"},
 *     summary="Homepage data: banners, categories, products",
 *     @OA\Response(
 *         response=200,
 *         description="Homepage content retrieved"
 *     )
 * )
 */

    public function index()
    {
        // $Categories = Category::all();
        $banner = Banner::all();
        $Products = Product::orderBy('created_at', 'desc')->take(20)->get();;
        $categories = Category::with(['products'])->get();



        return ApiResponse::sendResponse(200, 'all date retrieved', ['banner' => $banner, 'categories' => CategoryResource::collection($categories), 'products' => ProductResource::collection($Products)]);
    }

    /**
 * @OA\Get(
 *     path="/e_commerce/banner/{id}",
 *     tags={"E-Commerce - Home"},
 *     summary="Get product associated with a specific banner",
 *     @OA\Parameter(
 *         name="id",
 *         in="path",
 *         required=true,
 *         description="Banner ID",
 *         @OA\Schema(type="integer", example=3)
 *     ),
 *     @OA\Response(
 *         response=200,
 *         description="Product retrieved successfully"
 *     ),
 *     @OA\Response(
 *         response=404,
 *         description="Banner or product not found"
 *     )
 * )
 */

    public function bannerShowDetails($id)
    {

        $banner = Banner::find($id);

        if (!$banner) {
            return ApiResponse::sendResponse(404, 'No product associated with this banner', []);
        }
        $product = $banner->product;
        return ApiResponse::sendResponse(200, 'product retrieved successfully', new ProductResource($product));
    }

    /**
 * @OA\Get(
 *     path="/e_commerce/product/{id}",
 *     tags={"E-Commerce - Home"},
 *     summary="Get product details by ID",
 *     @OA\Parameter(
 *         name="id",
 *         in="path",
 *         required=true,
 *         description="Product ID",
 *         @OA\Schema(type="integer", example=5)
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

    public function productShowDetails($id)
    {

        $product = Product::find($id);

        if (!$product) {
            return ApiResponse::sendResponse(404, 'No details associated with this product', []);
        }

        return ApiResponse::sendResponse(200, 'product retrieved successfully', new ProductResource($product));
    }

    /**
 * @OA\Get(
 *     path="/e_commerce/category/{id}",
 *     tags={"E-Commerce - Home"},
 *     summary="Get products of a specific category",
 *     @OA\Parameter(
 *         name="id",
 *         in="path",
 *         required=true,
 *         description="Category ID",
 *         @OA\Schema(type="integer", example=2)
 *     ),
 *     @OA\Response(
 *         response=200,
 *         description="Products in category retrieved"
 *     ),
 *     @OA\Response(
 *         response=404,
 *         description="Category or products not found"
 *     )
 * )
 */

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
