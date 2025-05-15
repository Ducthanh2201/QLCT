<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Không tìm thấy trang - Quản lý chi tiêu</title>
    <style>
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            line-height: 1.6;
            margin: 0;
            padding: 0;
            background-color: #f8f9fa;
            color: #333;
            display: flex;
            justify-content: center;
            align-items: center;
            min-height: 100vh;
        }
        .container {
            max-width: 800px;
            margin: 40px auto;
            padding: 40px;
            background: #fff;
            border-radius: 8px;
            box-shadow: 0 0 20px rgba(0,0,0,0.1);
            text-align: center;
        }
        h1 {
            color: #dc3545;
            font-size: 3rem;
            margin-bottom: 10px;
        }
        h2 {
            color: #343a40;
            font-size: 1.8rem;
            margin-bottom: 20px;
        }
        p {
            color: #6c757d;
            font-size: 1.1rem;
            margin-bottom: 30px;
        }
        .btn {
            display: inline-block;
            padding: 12px 24px;
            background-color: #007bff;
            color: white;
            text-decoration: none;
            border-radius: 4px;
            font-weight: 600;
            transition: background-color 0.2s;
        }
        .btn:hover {
            background-color: #0056b3;
        }
        .error-img {
            max-width: 300px;
            margin-bottom: 30px;
        }
    </style>
</head>
<body>
    <div class="container">
        <h1>404</h1>
        <h2>Không tìm thấy trang</h2>
        <p>Trang bạn đang tìm không tồn tại hoặc đã bị di chuyển.</p>
        <a href="<?php echo BASEURL; ?>" class="btn">Quay về trang chủ</a>
    </div>
</body>
</html>