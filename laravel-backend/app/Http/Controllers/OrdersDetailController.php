<?php

namespace App\Http\Controllers;

use App\Models\OrderDetail;
use Illuminate\Http\Request;

class OrderDetailController extends Controller
{
    public function index($orderId)
    {
        $details = OrderDetail::where('order_id', $orderId)->with('product')->get();
        return response()->json($details);
    }

    public function store(Request $request)
    {
        $request->validate([
            'order_id' => 'required|exists:orders,id',
            'product_id' => 'required|exists:products,id',
            'quantity' => 'required|integer|min:1',
            'price' => 'required|numeric|min:0',
        ]);

        $detail = OrderDetail::create($request->all());

        return response()->json(['message' => 'Order detail created successfully', 'detail' => $detail], 201);
    }
}
