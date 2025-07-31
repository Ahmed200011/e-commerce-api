<?php

namespace App\Http\Controllers\Api\ecommerce;

use App\Helpers\ApiResponse;
use App\Http\Controllers\Controller;
use App\Http\Resources\MyOrderResource;
use App\Models\CartItem;
use App\Models\Order;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Services\PaymobPaymentService;
use OpenApi\Annotations as OA;


class OrderController extends Controller
{
    protected $userId;
    protected  $paymentGateway;

    public function __construct(PaymobPaymentService $paymentGateway)
    {

        $this->paymentGateway = $paymentGateway;
        $this->userId = auth()->id();
    }

    /**
 * @OA\Post(
 *     path="/e_commerce/order/checkout",
 *     tags={"E-Commerce - Orders"},
 *     summary="Checkout and create an order",
 *     security={{"sanctum":{}}},
 *     @OA\Response(
 *         response=200,
 *         description="Order created and payment link returned"
 *     ),
 *     @OA\Response(
 *         response=400,
 *         description="Cart is empty"
 *     ),
 *     @OA\Response(
 *         response=500,
 *         description="Checkout failed"
 *     )
 * )
 */

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

            $data = [
                "amount_cents" => $order->total_price * 100, // Assuming total_price is in EGP
                "shipping_data" => [
                    "first_name" => $order->user->name,
                    "last_name" => $order->user->name,
                    "phone_number" => $order->user->phone,
                    "email" =>  $order->user->email
                ],

                "items" => $order->items->map(function ($item) {
                    return [
                        "name" => $item->product->product_name,
                        "amount_cents" => $item->price,
                        "quantity" => $item->quantity,
                        "description" => $item->product->description
                    ];
                })->toArray(),
                "delivery_needed" => "false"
            ];
            $response = $this->paymentGateway->sendPayment($data);


            DB::commit();
            // Assuming you have a payment service to handle the payment

            return ApiResponse::sendResponse(200, 'Order created', $response['url']);
        } catch (\Exception $e) {
            DB::rollback();
            return ApiResponse::sendResponse(500, 'Checkout failed', ['error' => $e->getMessage()]);
        }
    }

    /**
 * @OA\Post(
 *     path="/e_commerce/callBack",
 *     tags={"E-Commerce - Orders"},
 *     summary="Paymob callback to confirm payment",
 *     @OA\RequestBody(
 *         description="Paymob callback payload",
 *         required=false,
 *         @OA\JsonContent()
 *     ),
 *     @OA\Response(
 *         response=200,
 *         description="Payment result received"
 *     )
 * )
 */

    public function callBack(Request $request)
    {
        $response = $this->paymentGateway->callBack($request);


        if ($response) {
            return ApiResponse::sendResponse(200, 'success payment', []);
        }
        return ApiResponse::sendResponse(
            200,
            'the payment failed',
            []
        );
    }
    /**
 * @OA\Post(
 *     path="/e_commerce/order/pay/{orderId}",
 *     tags={"E-Commerce - Orders"},
 *     summary="Mark an order as paid",
 *     security={{"sanctum":{}}},
 *     @OA\Parameter(
 *         name="orderId",
 *         in="path",
 *         required=true,
 *         description="Order ID",
 *         @OA\Schema(type="integer", example=10)
 *     ),
 *     @OA\Response(
 *         response=200,
 *         description="Order marked as paid"
 *     )
 * )
 */

    public function markAsPaid($orderId)
    {
        $order = Order::where('user_id', $this->userId)->findOrFail($orderId);
        $order->update(['status' => 'paid']);
        return ApiResponse::sendResponse(200, 'Payment successful', $order);
    }

    /**
 * @OA\Get(
 *     path="/e_commerce/order/my-orders",
 *     tags={"E-Commerce - Orders"},
 *     summary="Get authenticated user's orders",
 *     security={{"sanctum":{}}},
 *     @OA\Response(
 *         response=200,
 *         description="List of user's orders"
 *     )
 * )
 */

    public function myOrders()
    {
        $orders = Order::with('items.product')->where('user_id', $this->userId)->latest()->get();
        return ApiResponse::sendResponse(200, 'My orders',  MyOrderResource::collection($orders));
    }

    /**
 * @OA\Delete(
 *     path="/e_commerce/order/cancel/{orderId}",
 *     tags={"E-Commerce - Orders"},
 *     summary="Cancel an order",
 *     security={{"sanctum":{}}},
 *     @OA\Parameter(
 *         name="orderId",
 *         in="path",
 *         required=true,
 *         description="Order ID",
 *         @OA\Schema(type="integer", example=5)
 *     ),
 *     @OA\Response(
 *         response=200,
 *         description="Order cancelled successfully"
 *     )
 * )
 */

    public function OrderCancel($id)
    {
        $order = Order::where('user_id', $this->userId)->findOrFail($id);
        $order->delete();
        return ApiResponse::sendResponse(200, 'orders canceled',  []);
    }
}
