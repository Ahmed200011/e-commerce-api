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

class CartController extends Controller
{
    protected $userId;

    public function __construct()
    {
        $this->userId = Auth::id(); // أو: auth()->id();
    }
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
    public function removeFromCart($product_id)
    {
        CartItem::where('user_id',  $this->userId)
            ->where('product_id', $product_id)
            ->delete();

        return ApiResponse::sendResponse(200, 'Item removed from cart', []);
    }
    public function clearCart()
    {
        CartItem::where('user_id', $this->userId)->delete();
        return ApiResponse::sendResponse(200, 'Cart cleared', []);
    }
}
