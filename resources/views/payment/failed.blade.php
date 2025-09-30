<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Payment Failed</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background: linear-gradient(135deg, #f093fb 0%, #f5576c 100%);
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
        .failed-icon {
            width: 80px;
            height: 80px;
            background: #ef4444;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            margin: 0 auto 30px;
            animation: shake 0.5s ease-out;
        }
        .failed-icon svg {
            width: 50px;
            height: 50px;
            stroke: white;
            stroke-width: 3;
            stroke-linecap: round;
            stroke-linejoin: round;
            fill: none;
        }
        @keyframes shake {
            0%, 100% { transform: translateX(0); }
            25% { transform: translateX(-10px); }
            75% { transform: translateX(10px); }
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
            background: #fef2f2;
            border-radius: 10px;
            padding: 20px;
            margin: 20px 0;
            text-align: left;
            border: 1px solid #fee2e2;
        }
        .order-info-item {
            display: flex;
            justify-content: space-between;
            padding: 10px 0;
            border-bottom: 1px solid #fee2e2;
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
        .reasons {
            background: #f9fafb;
            border-radius: 10px;
            padding: 20px;
            margin: 20px 0;
            text-align: left;
        }
        .reasons h3 {
            color: #1f2937;
            font-size: 16px;
            margin-bottom: 15px;
        }
        .reasons ul {
            list-style: none;
            padding: 0;
        }
        .reasons li {
            padding: 8px 0;
            padding-left: 25px;
            position: relative;
            color: #6b7280;
            font-size: 14px;
        }
        .reasons li:before {
            content: "•";
            position: absolute;
            left: 10px;
            color: #ef4444;
            font-size: 20px;
        }
        .btn-group {
            display: flex;
            gap: 10px;
            margin-top: 20px;
            flex-wrap: wrap;
            justify-content: center;
        }
        .btn {
            display: inline-block;
            padding: 15px 30px;
            border-radius: 10px;
            text-decoration: none;
            font-weight: 600;
            transition: all 0.3s ease;
            flex: 1;
            min-width: 150px;
        }
        .btn-primary {
            background: #ef4444;
            color: white;
        }
        .btn-primary:hover {
            background: #dc2626;
            transform: translateY(-2px);
            box-shadow: 0 10px 20px rgba(239, 68, 68, 0.4);
        }
        .btn-secondary {
            background: #f3f4f6;
            color: #1f2937;
        }
        .btn-secondary:hover {
            background: #e5e7eb;
            transform: translateY(-2px);
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
        <div class="failed-icon">
            <svg viewBox="0 0 24 24">
                <line x1="18" y1="6" x2="6" y2="18"></line>
                <line x1="6" y1="6" x2="18" y2="18"></line>
            </svg>
        </div>

        <h1>Payment Failed</h1>
        <p>Maaf, pembayaran Anda tidak dapat diproses.</p>

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
                <span class="value" style="color: #ef4444;">FAILED</span>
            </div>

            <div class="order-info-item">
                <span class="label">Time:</span>
                <span class="value">{{ now()->format('d M Y, H:i') }}</span>
            </div>
        </div>

        <div class="reasons">
            <h3>Kemungkinan Penyebab:</h3>
            <ul>
                <li>Saldo tidak mencukupi</li>
                <li>Koneksi internet terputus</li>
                <li>Informasi pembayaran tidak valid</li>
                <li>Transaksi dibatalkan oleh pengguna</li>
                <li>Batas waktu pembayaran habis</li>
            </ul>
        </div>

        <div class="btn-group">
            <a href="/" class="btn btn-primary">Coba Lagi</a>
            <a href="/" class="btn btn-secondary">Kembali ke Beranda</a>
        </div>

        <div class="note">
            <p>Butuh bantuan? Hubungi customer service kami atau cek FAQ.</p>
        </div>
    </div>
</body>
</html>
