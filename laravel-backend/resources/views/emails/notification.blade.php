<!-- resources/views/emails/notification.blade.php -->
<!DOCTYPE html>
<html lang="vi">

<head>
    <meta charset="UTF-8">
    <title>{{ $subject }}</title>
</head>

<body>
    <h1>{{ $subject }}</h1>
    <p>Xin chào,</p>
    <p>{{ $messageContent }}</p>
    <p>Cảm ơn bạn!</p>
</body>

</html>