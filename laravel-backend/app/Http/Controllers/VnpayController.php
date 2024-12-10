<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Order;
use Illuminate\Support\Facades\Mail;
use App\Mail\PaymentConfirmation;

class VnpayController extends Controller
{
    public function create(Request $request)
    {
        $vnp_TmnCode = config('services.vnpay.tmn_code');
        $vnp_HashSecret = config('services.vnpay.hash_secret');
        $vnp_Url = config('services.vnpay.url');
        $vnp_ReturnUrl = url('/api/vnpay/callback');

        $vnp_TxnRef = $request->order_id;
        $vnp_OrderInfo = 'Thanh toán đơn hàng ' . $vnp_TxnRef;
        $vnp_OrderType = 'billpayment';
        $vnp_Amount = $request->amount * 100;
        $vnp_Locale = 'vn';
        $vnp_IpAddr = $request->ip();
        $vnp_CreateDate = now()->format('YmdHis');

        $inputData = [
            "vnp_Version" => "2.1.0",
            "vnp_TmnCode" => $vnp_TmnCode,
            "vnp_Amount" => $vnp_Amount,
            "vnp_Command" => "pay",
            "vnp_CreateDate" => $vnp_CreateDate,
            "vnp_CurrCode" => "VND",
            "vnp_IpAddr" => $vnp_IpAddr,
            "vnp_Locale" => $vnp_Locale,
            "vnp_OrderInfo" => $vnp_OrderInfo,
            "vnp_OrderType" => $vnp_OrderType,
            "vnp_ReturnUrl" => $vnp_ReturnUrl,
            "vnp_TxnRef" => $vnp_TxnRef,
        ];

        ksort($inputData);

        $query = "";
        $i = 0;
        $hashdata = "";

        foreach ($inputData as $key => $value) {
            if ($i == 1) {
                $hashdata .= '&' . urlencode($key) . "=" . urlencode($value);
            } else {
                $hashdata .= urlencode($key) . "=" . urlencode($value);
                $i = 1;
            }
            $query .= urlencode($key) . "=" . urlencode($value) . '&';
        }

        $vnp_Url = $vnp_Url . "?" . rtrim($query, '&');

        if (isset($vnp_HashSecret)) {
            $vnpSecureHash = hash_hmac('sha512', $hashdata, $vnp_HashSecret);
            $vnp_Url .= '&vnp_SecureHash=' . $vnpSecureHash;
        }

        return response()->json(['url' => $vnp_Url]);
    }


    // VnpayController callback function
    public function callback(Request $request)
    {
        $vnp_HashSecret = config('services.vnpay.hash_secret');
        $inputData = $request->all();
        $vnp_SecureHash = $inputData['vnp_SecureHash'];

        unset($inputData['vnp_SecureHashType']);
        unset($inputData['vnp_SecureHash']);

        ksort($inputData);

        $hashData = '';
        foreach ($inputData as $key => $value) {
            $hashData .= ($hashData ? '&' : '') . urlencode($key) . "=" . urlencode($value);
        }

        $secureHash = hash_hmac('sha512', $hashData, $vnp_HashSecret);

        if ($secureHash === $vnp_SecureHash) {
            $order = Order::where('id', $inputData['vnp_TxnRef'])->first();

            if ($order) {
                $order->status = 'paid';
                $order->save();

                Mail::to($order->email)->send(new PaymentConfirmation($order));

                $frontendUrl = "http://localhost:3000/payment/result?status={$inputData['vnp_ResponseCode']}&order_id={$inputData['vnp_TxnRef']}";
                return redirect()->to($frontendUrl);
            } else {
                $frontendUrl = "http://localhost:3000/payment/result?status=01&order_id={$inputData['vnp_TxnRef']}";
                return redirect()->to($frontendUrl);
            }
        } else {
            $frontendUrl = "http://localhost:3000/payment/result?status=01&order_id={$inputData['vnp_TxnRef']}";
            return redirect()->to($frontendUrl);
        }
    }
}
