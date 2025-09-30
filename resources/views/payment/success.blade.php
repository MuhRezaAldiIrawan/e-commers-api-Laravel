<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Payment Success</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 20px;
        }
        .container {
            background: white;
            border-radius: 20px;
            box-shadow: 0 20px 60px rgba(0,0,0,0.3);
            max-width: 500px;
            width: 100%;
            padding: 40px;
            text-align: center;
        }
        .success-icon {
            width: 80px;
            height: 80px;
            background: #10b981;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            margin: 0 auto 30px;
            animation: scaleIn 0.5s ease-out;
        }
        .success-icon svg {
            width: 50px;
            height: 50px;
            stroke: white;
            stroke-width: 3;
            stroke-linecap: round;
            stroke-linejoin: round;
            fill: none;
        }
        @keyframes scaleIn {
            0% { transform: scale(0); }
            50% { transform: scale(1.2); }
            100% { transform: scale(1); }
        }
        h1 {
            color: #1f2937;
            font-size: 32px;
            margin-bottom: 10px;
        }
        p {
            color: #6b7280;
            font-size: 16px;
            line-height: 1.6;
            margin-bottom: 20px;
        }
        .order-info {
            background: #f3f4f6;
            border-radius: 10px;
            padding: 20px;
            margin: 20px 0;
            text-align: left;
        }
        .order-info-item {
            display: flex;
            justify-content: space-between;
            padding: 10px 0;
            border-bottom: 1px solid #e5e7eb;
        }
        .order-info-item:last-child {
            border-bottom: none;
        }
        .label {
            color: #6b7280;
            font-weight: 500;
        }
        .value {
            color: #1f2937;
            font-weight: 600;
        }
        .btn {
            display: inline-block;
            background: #667eea;
            color: white;
            padding: 15px 40px;
            border-radius: 10px;
            text-decoration: none;
            font-weight: 600;
            margin-top: 20px;
            transition: all 0.3s ease;
        }
        .btn:hover {
            background: #5568d3;
            transform: translateY(-2px);
            box-shadow: 0 10px 20px rgba(102, 126, 234, 0.4);
        }
        .note {
            margin-top: 30px;
            padding-top: 20px;
            border-top: 1px solid #e5e7eb;
            color: #9ca3af;
            font-size: 14px;
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="success-icon">
            <svg viewBox="0 0 24 24">
                <polyline points="20 6 9 17 4 12"></polyline>
            </svg>
        </div>

        <h1>Payment Successful!</h1>
        <p>Terima kasih! Pembayaran Anda telah berhasil diproses.</p>

        <div class="order-info">
            @if($external_id)
            <div class="order-info-item">
                <span class="label">Invoice ID:</span>
                <span class="value">{{ $external_id }}</span>
            </div>
            @endif

            @if($order_id)
            <div class="order-info-item">
                <span class="label">Order ID:</span>
                <span class="value">#{{ $order_id }}</span>
            </div>
            @endif

            <div class="order-info-item">
                <span class="label">Status:</span>
                <span class="value" style="color: #10b981;">PAID</span>
            </div>

            <div class="order-info-item">
                <span class="label">Payment Time:</span>
                <span class="value">{{ now()->format('d M Y, H:i') }}</span>
            </div>
        </div>

        <p>Email konfirmasi telah dikirim ke alamat email Anda.</p>

        <a href="/" class="btn">Kembali ke Beranda</a>

        <div class="note">
            <p>Jika ada pertanyaan, silakan hubungi customer service kami.</p>
        </div>
    </div>
</body>
</html>
