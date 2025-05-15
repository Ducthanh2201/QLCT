<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Lỗi máy chủ - Quản lý chi tiêu</title>
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
        .error-details {
            margin-top: 30px;
            padding: 20px;
            background-color: #f8f9fa;
            border-radius: 8px;
            text-align: left;
        }
        .error-details h3 {
            margin-top: 0;
        }
        .error-details pre {
            overflow: auto;
            background-color: #f1f1f1;
            padding: 15px;
            border-radius: 4px;
        }
    </style>
</head>
<body>
    <div class="container">
        <h1>500</h1>
        <h2>Lỗi máy chủ</h2>
        <p>Đã xảy ra lỗi khi xử lý yêu cầu của bạn. Chúng tôi đã ghi nhận lỗi và đang khắc phục.</p>
        <a href="<?php echo BASEURL; ?>" class="btn">Quay về trang chủ</a>
        
        <?php if(defined('ENVIRONMENT') && ENVIRONMENT === 'development'): ?>
            <div class="error-details">
                <h3>Chi tiết lỗi</h3>
                <p><strong><?php echo htmlspecialchars($exception->getMessage()); ?></strong></p>
                <p>File: <?php echo htmlspecialchars($exception->getFile()); ?> (line <?php echo $exception->getLine(); ?>)</p>
                <pre><?php echo htmlspecialchars($exception->getTraceAsString()); ?></pre>
            </div>
        <?php endif; ?>
    </div>
</body>
</html>