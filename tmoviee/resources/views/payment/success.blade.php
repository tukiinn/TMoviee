<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <title>Thanh toán thành công</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <style>
        body {
            background: linear-gradient(135deg, #e0ffe6 0%, #f6fff6 100%);
            color: #1a7f37;
            font-family: 'Segoe UI', Arial, sans-serif;
            min-height: 100vh;
            margin: 0;
            display: flex;
            align-items: center;
            justify-content: center;
        }
        .success-box {
            background: #fff;
            border-radius: 18px;
            box-shadow: 0 4px 32px 0 #d1fae5;
            padding: 48px 36px 36px 36px;
            max-width: 400px;
            width: 100%;
            text-align: center;
        }
        .success-icon {
            font-size: 4rem;
            color: #22c55e;
            margin-bottom: 18px;
            animation: pop 0.6s cubic-bezier(.68,-0.55,.27,1.55);
        }
        @keyframes pop {
            0% { transform: scale(0.5); opacity: 0; }
            80% { transform: scale(1.1); opacity: 1; }
            100% { transform: scale(1); }
        }
        h1 {
            font-size: 2rem;
            margin-bottom: 10px;
            color: #1a7f37;
        }
        p {
            font-size: 1.1rem;
            color: #333;
            margin-bottom: 28px;
        }
        .btn-home {
            display: inline-block;
            background: linear-gradient(90deg, #4ade80 0%, #22c55e 100%);
            color: #fff;
            font-weight: bold;
            border: none;
            border-radius: 24px;
            padding: 12px 36px;
            font-size: 1.1rem;
            text-decoration: none;
            box-shadow: 0 2px 8px #d1fae5;
            transition: background 0.2s, color 0.2s, box-shadow 0.2s;
        }
        .btn-home:hover {
            background: linear-gradient(90deg, #22c55e 0%, #4ade80 100%);
            color: #fff;
            box-shadow: 0 4px 16px #bbf7d0;
        }
        @media (max-width: 600px) {
            .success-box { padding: 32px 10px 24px 10px; }
            h1 { font-size: 1.3rem; }
            .success-icon { font-size: 2.5rem; }
        }
    </style>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" />
</head>
<body>
    <div class="success-box">
        <div class="success-icon"><i class="fas fa-check-circle"></i></div>
        <h1>Thanh toán thành công!</h1>
        <p>Cảm ơn bạn đã đăng ký gói thành viên.<br>Quyền lợi của bạn sẽ được kích hoạt ngay lập tức.<br><br>Chúc bạn có trải nghiệm tuyệt vời cùng chúng tôi!</p>
        <a href="/" class="btn-home">Về trang chủ</a>
    </div>
</body>
</html> 