<?php

namespace App\Http\Controllers;

use App\Models\Order;
use App\Models\OrderDetail;
use App\Models\Products;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class OrderController extends Controller
{
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'user_id' => 'required|exists:users,id',
            'full_name' => 'required|string|max:255',
            'email' => 'required|email|max:255',
            'address' => 'required|string|max:255',
            'payment_method' => 'required|in:creditCard,cod,vnpay',
            'total_price' => 'required|numeric',
            'discount' => 'required|numeric',
            'products' => 'required|array',
            'products.*.id' => 'required|exists:products,id',
            'products.*.quantity' => 'required|integer|min:1',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        try {
            $order = Order::create([
                'user_id' => $request->user_id,
                'full_name' => $request->full_name,
                'email' => $request->email,
                'address' => $request->address,
                'payment_method' => $request->payment_method,
                'total_price' => $request->total_price,
                'discount' => $request->discount,
            ]);

            foreach ($request->products as $product) {
                $productDetails = Products::find($product['id']);
                OrderDetail::create([
                    'order_id' => $order->id,
                    'product_id' => $product['id'],
                    'quantity' => $product['quantity'],
                    'price' => $productDetails->price,
                ]);
            }

            return response()->json(['message' => 'Order created successfully', 'order' => $order], 201);
        } catch (\Exception $e) {
            return response()->json(['message' => 'Failed to create order', 'error' => $e->getMessage()], 500);
        }
    }

    public function getOrderStatus($orderId)
    {
        $order = Order::find($orderId);
        if (!$order) {
            return response()->json(['message' => 'Đơn hàng không tồn tại'], 404);
        }

        return response()->json($order);
    }

    public function getOrdersByUserId($userId)
    {
        // $orders = Order::where('user_id', $userId)->get();
        $orders = Order::with(['orderDetails.product'])->where('user_id', $userId)->get();

        return response()->json($orders);
    }

    public function getOdersAll()
    {
        $orders = Order::with(['orderDetails.product'])->get();
        return response()->json($orders);
    }

    public function updateOrderStatus(Request $request, $orderId)
    {
        $order = Order::find($orderId);
        if (!$order) {
            return response()->json(['message' => 'Đơn hàng không tồn tại'], 404);
        }

        $order->status = $request->status;
        $order->save();

        return response()->json(['message' => 'Cập nhật trạng thái thành công'], 200);
    }

    public function deleteOrder($orderId)
    {
        $order = Order::find($orderId);
        if (!$order) {
            return response()->json(['message' => 'Đơn hàng không tồn tại'], 404);
        }

        $order->delete();

        return response()->json(['message' => 'Xóa đơn hàng'], 200);
    }
}
