<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Document</title>
</head>
<body style="font-family: Arial, sans-serif; background-color: #f4f4f4; padding: 20px;">
    <div style="max-width: 600px; margin: 0 auto; background-color: #fff; padding: 20px; border-radius: 10px; box-shadow: 0 0 10px rgba(0,0,0,0.1);">
        <h2 style="color: #333;">Order Confirmation</h2>
        <p style="color: #666;">Dear {{$order->user->name}},</p>
        <p style="color: #666;">We're thrilled to inform you that your order has been successfully received and is now being processed.</p>
        <p style="color: #666; margin-top: 20px;">Thank you for choosing us. If you have any questions or concerns, feel free to contact our customer support team.</p>
        <p style="color: #666;">Best Regards,<br>Bookmania</p>
    </div>
</body>
</html>