<?php

namespace App\Http\Controllers\Api\ecommerce;

use App\Helpers\ApiResponse;
use App\Http\Controllers\Controller;
use App\Http\Resources\CartItemResource;
use App\Models\CartItem;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use OpenApi\Annotations as OA;

class CartController extends Controller
{
    protected $userId;

    public function __construct()
    {
        $this->userId = Auth::id(); // أو: auth()->id();
    }
    /**
 * @OA\Post(
 *     path="/e_commerce/cart/add",
 *     tags={"E-Commerce - Cart"},
 *     summary="Add a product to cart",
 *     security={{"sanctum":{}}},
 *     @OA\RequestBody(
 *         required=true,
 *         @OA\JsonContent(
 *             required={"product_id", "quantity"},
 *             @OA\Property(property="product_id", type="integer", example=5),
 *             @OA\Property(property="quantity", type="integer", example=2)
 *         )
 *     ),
 *     @OA\Response(
 *         response=200,
 *         description="Added to cart successfully"
 *     ),
 *     @OA\Response(
 *         response=422,
 *         description="Validation error"
 *     )
 * )
 */

    public function addToCart(Request $request)
    {
        $data = Validator::make($request->all(), [
            'product_id' => 'required|exists:products,id',
            'quantity' => 'required|integer|min:1',
        ]);
        if ($data->fails()) {
            return ApiResponse::sendResponse(422, 'fail to register, please try again', $data->errors()->all());
        }

        $validated = $data->validated();

        $item = CartItem::updateOrCreate(
            [
                'user_id' => $this->userId,
                'product_id' => $validated['product_id']
            ],
            [
                'quantity' => DB::raw('quantity + ' . $validated['quantity'])
            ]
        )->refresh();

        return ApiResponse::sendResponse(200, 'Added to cart', new CartItemResource($item));
    }

    /**
 * @OA\Get(
 *     path="/e_commerce/cart",
 *     tags={"E-Commerce - Cart"},
 *     summary="View items in cart",
 *     security={{"sanctum":{}}},
 *     @OA\Response(
 *         response=200,
 *         description="Cart contents retrieved"
 *     )
 * )
 */

    public function viewCart()
    {
        $items = CartItem::with('product')
            ->where('user_id', $this->userId)
            ->get();

        return ApiResponse::sendResponse(200, 'Cart contents', [
            'items' => CartItemResource::collection($items),
            'total' => $items->sum(fn($item) => $item->quantity * $item->product->price)
        ]);
    }

    /**
 * @OA\Put(
 *     path="/e_commerce/cart/update/{product_id}",
 *     tags={"E-Commerce - Cart"},
 *     summary="Update quantity for a cart item",
 *     security={{"sanctum":{}}},
 *     @OA\Parameter(
 *         name="product_id",
 *         in="path",
 *         required=true,
 *         description="Product ID",
 *         @OA\Schema(type="integer")
 *     ),
 *     @OA\RequestBody(
 *         required=true,
 *         @OA\JsonContent(
 *             required={"quantity"},
 *             @OA\Property(property="quantity", type="integer", example=3)
 *         )
 *     ),
 *     @OA\Response(
 *         response=200,
 *         description="Quantity updated successfully"
 *     ),
 *     @OA\Response(
 *         response=422,
 *         description="Validation failed"
 *     )
 * )
 */

    public function updateQuantity(Request $request, $product_id)
    {
        $data = Validator::make($request->all(), ['quantity' => 'required|integer|min:1']);
        if ($data->fails()) {
            return ApiResponse::sendResponse(422, 'fail to register, please try again', $data->errors()->all());
        }
        $validated = $data->validated();

        $item = CartItem::where('user_id', $this->userId)
            ->where('product_id', $product_id)
            ->firstOrFail();

        $item->update(['quantity' => $validated['quantity']]);

        return ApiResponse::sendResponse(200, 'Quantity updated', new CartItemResource($item));
    }

    /**
 * @OA\Delete(
 *     path="/e_commerce/cart/remove/{product_id}",
 *     tags={"E-Commerce - Cart"},
 *     summary="Remove product from cart",
 *     security={{"sanctum":{}}},
 *     @OA\Parameter(
 *         name="product_id",
 *         in="path",
 *         required=true,
 *         description="Product ID to remove",
 *         @OA\Schema(type="integer")
 *     ),
 *     @OA\Response(
 *         response=200,
 *         description="Item removed from cart"
 *     )
 * )
 */

    public function removeFromCart($product_id)
    {
        CartItem::where('user_id',  $this->userId)
            ->where('product_id', $product_id)
            ->delete();

        return ApiResponse::sendResponse(200, 'Item removed from cart', []);
    }

    /**
 * @OA\Delete(
 *     path="/e_commerce/cart/clear",
 *     tags={"E-Commerce - Cart"},
 *     summary="Clear entire cart",
 *     security={{"sanctum":{}}},
 *     @OA\Response(
 *         response=200,
 *         description="Cart cleared successfully"
 *     )
 * )
 */

    public function clearCart()
    {
        CartItem::where('user_id', $this->userId)->delete();
        return ApiResponse::sendResponse(200, 'Cart cleared', []);
    }
}
