<?php

namespace App\Http\Controllers\Api\ecommerce;

use App\Helpers\ApiResponse;
use App\Http\Controllers\Controller;
use App\Http\Resources\Dashboard\ProductResource;
use App\Models\Product;
use App\Traits\Pagination;
use Illuminate\Http\Request;

class ShopController extends Controller
{
    use Pagination;

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
