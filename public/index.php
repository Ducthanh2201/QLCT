<?php
// filepath: c:\xampp\htdocs\ADM-QLCT\expense-management\public\index.php

// Hiển thị lỗi để debug
ini_set('display_errors', 1);
error_reporting(E_ALL);

// Định nghĩa các hằng số CƠ BẢN trước khi load bất kỳ file nào khác
define('DS', DIRECTORY_SEPARATOR);
define('ROOT', dirname(dirname(__FILE__)));

// Kiểm tra xem APPROOT đã được định nghĩa chưa
if (!defined('APPROOT')) {
    define('APPROOT', ROOT . DS . 'app');
}

// Khai báo trước về môi trường
define('ENVIRONMENT', 'development');

// Hiển thị lỗi trong môi trường development
if (ENVIRONMENT === 'development') {
    ini_set('display_errors', 1);
    error_reporting(E_ALL);
}

// Ghi log để debug
error_log("===== REQUEST INFO =====");
error_log("REQUEST_URI: " . $_SERVER['REQUEST_URI']);
error_log("SCRIPT_NAME: " . $_SERVER['SCRIPT_NAME']);
error_log("GET URL: " . (isset($_GET['url']) ? $_GET['url'] : 'No URL parameter'));

// Xử lý ngoại lệ toàn cục
set_exception_handler(function($exception) {
    // Log lỗi
    error_log("Uncaught Exception: " . $exception->getMessage() . " in file " . 
              $exception->getFile() . " on line " . $exception->getLine());
    
    // Định nghĩa BASEURL nếu chưa được định nghĩa
    if (!defined('BASEURL')) {
        define('BASEURL', 'http://localhost/ADM-QLCT/expense-management');
    }
    
    // Hiển thị lỗi
    header('HTTP/1.1 500 Internal Server Error');
    echo '<!DOCTYPE html>
    <html>
    <head>
        <meta charset="UTF-8">
        <title>Lỗi ứng dụng</title>
        <style>
            body { font-family: Arial, sans-serif; line-height: 1.6; margin: 0; padding: 20px; }
            .container { max-width: 800px; margin: 0 auto; border: 1px solid #ddd; padding: 20px; border-radius: 5px; }
            .error-title { color: #d32f2f; }
            .error-message { background-color: #ffebee; padding: 15px; border-left: 5px solid #d32f2f; margin-bottom: 20px; }
            .error-details { background-color: #f5f5f5; padding: 15px; border-left: 5px solid #9e9e9e; white-space: pre-wrap; overflow-x: auto; }
            .nav-links { margin-top: 20px; }
            .nav-links a { display: inline-block; margin-right: 10px; padding: 8px 16px; background-color: #2196F3; color: white; text-decoration: none; border-radius: 4px; }
        </style>
    </head>
    <body>
        <div class="container">
            <h1 class="error-title">Lỗi ứng dụng không xử lý được</h1>
            <div class="error-message">
                <p><strong>Thông báo:</strong> ' . htmlspecialchars($exception->getMessage()) . '</p>
            </div>
            <h3>Chi tiết lỗi</h3>
            <div class="error-details">
                <p><strong>File:</strong> ' . $exception->getFile() . '</p>
                <p><strong>Line:</strong> ' . $exception->getLine() . '</p>
                <h4>Stack Trace:</h4>
                <pre>' . htmlspecialchars($exception->getTraceAsString()) . '</pre>
            </div>
            <div class="nav-links">
                <a href="' . BASEURL . '">Trang chủ</a>
                <a href="' . BASEURL . '/public/debug-error.php">Kiểm tra lỗi</a>
            </div>
        </div>
    </body>
    </html>';
    exit;
});

// Load các file cấu hình
require_once APPROOT . DS . 'config' . DS . 'config.php';

// Set header cho việc debug
header('X-Debug-Baseurl: ' . BASEURL);
header('X-Debug-Approot: ' . APPROOT);

// Thử load expense_detail.php để xác nhận tồn tại
$viewFile = APPROOT . DS . 'views' . DS . 'expenses' . DS . 'expense_detail.php';
if (file_exists($viewFile)) {
    error_log("expense_detail.php exists at: " . $viewFile);
} else {
    error_log("ERROR: expense_detail.php does NOT exist at: " . $viewFile);
}

// Load các file lõi theo thứ tự đúng - QUAN TRỌNG!
require_once APPROOT . DS . 'controllers' . DS . 'BaseController.php';  // Load BaseController trước
require_once APPROOT . DS . 'core' . DS . 'Database.php';
require_once APPROOT . DS . 'core' . DS . 'Router.php';
require_once APPROOT . DS . 'core' . DS . 'App.php';

// Load các helper
require_once APPROOT . DS . 'helpers' . DS . 'session_helper.php';
require_once APPROOT . DS . 'helpers' . DS . 'helpers.php';

// Tạo một hàm autoload đơn giản để load các class khi cần
spl_autoload_register(function ($className) {
    // Log để debug
    error_log("Autoloading class: $className");
    
    // Các đường dẫn có thể chứa class
    $paths = [
        APPROOT . '/controllers/' . $className . '.php',
        APPROOT . '/models/' . $className . '.php',
        APPROOT . '/libraries/' . $className . '.php',
        APPROOT . '/core/' . $className . '.php'
    ];
    
    // Tìm và load file
    foreach ($paths as $path) {
        if (file_exists($path)) {
            error_log("Found class at: $path");
            require_once $path;
            return true;
        }
    }
    
    error_log("Class not found: $className");
    return false;
});

// Khởi tạo ứng dụng
try {
    $app = new App();
} catch (Exception $e) {
    // Log lỗi
    error_log("Fatal error: " . $e->getMessage());
    error_log("Trace: " . $e->getTraceAsString());
    
    // Hiển thị lỗi
    echo "<h1>Lỗi ứng dụng</h1>";
    echo "<p>Đã xảy ra lỗi trong khi xử lý yêu cầu của bạn:</p>";
    echo "<p><strong>" . htmlspecialchars($e->getMessage()) . "</strong></p>";
    
    if (defined('ENVIRONMENT') && ENVIRONMENT === 'development') {
        echo "<p>File: " . htmlspecialchars($e->getFile()) . " (line " . $e->getLine() . ")</p>";
        echo "<pre>" . htmlspecialchars($e->getTraceAsString()) . "</pre>";
    }
}