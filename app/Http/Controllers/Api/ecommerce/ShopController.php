<?php

namespace App\Http\Controllers\Api\ecommerce;

use App\Helpers\ApiResponse;
use App\Http\Controllers\Controller;
use App\Http\Resources\Dashboard\ProductResource;
use App\Models\Product;
use App\Traits\Pagination;
use Illuminate\Http\Request;
use OpenApi\Annotations as OA;


class ShopController extends Controller
{
    use Pagination;

    /**
 * @OA\Get(
 *     path="/e_commerce/products/search",
 *     tags={"E-Commerce - Shop"},
 *     summary="Search for products by name or category",
 *     @OA\Parameter(
 *         name="query",
 *         in="query",
 *         required=false,
 *         description="Search by product name",
 *         @OA\Schema(type="string", example="iphone")
 *     ),
 *     @OA\Parameter(
 *         name="category",
 *         in="query",
 *         required=false,
 *         description="Filter by category name",
 *         @OA\Schema(type="string", example="electronics")
 *     ),
 *     @OA\Response(
 *         response=200,
 *         description="Matching products retrieved successfully"
 *     ),
 *     @OA\Response(
 *         response=404,
 *         description="No matching products found"
 *     )
 * )
 */


    public function search(Request $request)
    {
        $query = $request->query('query');
        $category = $request->query('category');

        $productsQuery  = Product::query();

        if (!empty($query)) {
            $productsQuery->where('product_name', 'LIKE', '%' . $query . '%');
        }
        if (!empty($category)) {
            $productsQuery->whereHas('category', function ($q) use ($category) {
                $q->where('name', 'LIKE', '%' . $category . '%');
            });
        }

        $products = $productsQuery->orderBy('id')->cursorPaginate(20);

        if ($products->isEmpty()) {
            return ApiResponse::sendResponse(404, 'There is no product matching your query.', []);
        }
        $customData = [
            'Rows' => ProductResource::collection($products),
            'pagination' => $this->formatPagination($products)
        ];
        return ApiResponse::sendResponse(200, 'Products retrieved successfully', $customData);
    }
}
