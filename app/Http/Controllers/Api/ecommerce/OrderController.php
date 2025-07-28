<?php

namespace App\Http\Controllers\Api\ecommerce;

use App\Helpers\ApiResponse;
use App\Http\Controllers\Controller;
use App\Http\Resources\MyOrderResource;
use App\Http\Resources\OrderResource;
use App\Models\CartItem;
use App\Models\Order;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class OrderController extends Controller
{
    protected $userId;

    public function __construct()
    {
        $this->userId = Auth::id(); // أو: auth()->id();
    }

    public function checkout()
    {

        // $user = Auth::user();
        $cartItems = CartItem::with('product')->where('user_id', $this->userId)->get();
        if ($cartItems->isEmpty()) {
            return ApiResponse::sendResponse(400, 'Cart is empty', []);
        }

        $total = $cartItems->sum(fn($item) => $item->quantity * $item->product->price);

        DB::beginTransaction();

        try {
            $order = Order::create([
                'user_id' => $this->userId,
                'total_price' => $total,
                'status' => 'pending',
            ]);

            foreach ($cartItems as $item) {
                $order->items()->create([
                    'product_id' => $item->product_id,
                    'quantity' => $item->quantity,
                    'price' => $item->product->price,
                ]);
            }

            CartItem::where('user_id', $this->userId)->delete();

            DB::commit();

            return ApiResponse::sendResponse(200, 'Order created', new OrderResource($order));
        } catch (\Exception $e) {
            DB::rollback();
            return ApiResponse::sendResponse(500, 'Checkout failed', ['error' => $e->getMessage()]);
        }
    }
    public function markAsPaid($orderId)
    {
        $order = Order::where('user_id', $this->userId)->findOrFail($orderId);
        $order->update(['status' => 'paid']);
        return ApiResponse::sendResponse(200, 'Payment successful', $order);
    }
    public function myOrders()
    {
        $orders = Order::with('items.product')->where('user_id', $this->userId)->latest()->get();
        return ApiResponse::sendResponse(200, 'My orders',  MyOrderResource::collection($orders));
    }
    public function OrderCancel($id)
    {
        $order = Order::where('user_id', $this->userId)->findOrFail($id);
        $order->delete();
        return ApiResponse::sendResponse(200, 'orders canceled',  []);
    }
}
