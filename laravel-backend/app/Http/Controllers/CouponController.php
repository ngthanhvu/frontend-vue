<?php
// app/Http/Controllers/CouponController.php
namespace App\Http\Controllers;

use App\Models\Coupon;
use Illuminate\Http\Request;

class CouponController extends Controller
{
    // Lấy danh sách tất cả coupon
    public function index()
    {
        $coupons = Coupon::all();
        return response()->json($coupons);
    }

    // Tạo coupon mới
    public function store(Request $request)
    {
        $request->validate([
            'code' => 'required|string|unique:coupons,code',
            'discount' => 'required|numeric',
            'expires_at' => 'nullable|date',
        ]);

        $coupon = Coupon::create($request->all());

        return response()->json($coupon, 201);
    }

    // Lấy thông tin chi tiết coupon
    public function show($id)
    {
        $coupon = Coupon::find($id);

        if (!$coupon) {
            return response()->json(['message' => 'Coupon not found'], 404);
        }

        return response()->json($coupon);
    }

    // Áp dụng coupon
    public function apply(Request $request)
    {
        $request->validate([
            'code' => 'required|string|exists:coupons,code',
        ]);

        $coupon = Coupon::where('code', $request->code)->first();

        if ($coupon && $coupon->expires_at && $coupon->expires_at < now()) {
            return response()->json(['message' => 'Coupon has expired'], 400);
        }

        return response()->json(['message' => 'Coupon applied successfully', 'coupon' => $coupon]);
    }
}
