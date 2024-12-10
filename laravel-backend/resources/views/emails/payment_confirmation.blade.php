<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Xác nhận thanh toán</title>
</head>
<body>
    <h1>Xin chào {{ $order->full_name }}</h1>
    <p>Cảm ơn bạn đã thanh toán đơn hàng của chúng tôi. Đây là thông tin thanh toán của bạn:</p>
    <ul>
        <li><strong>Mã đơn hàng:</strong> {{ $order->id }}</li>
        <li><strong>Tổng giá trị:</strong> {{ number_format($order->total_price, 0, ',', '.') }} VND</li>
        <li><strong>Trạng thái:</strong> Đã thanh toán</li>
    </ul>
    <p>Chúng tôi sẽ tiến hành xử lý đơn hàng của bạn và giao hàng trong thời gian sớm nhất.</p>
    <p>Cảm ơn bạn đã tin tưởng sử dụng dịch vụ của chúng tôi!</p>
</body>
</html>
