<?php
// filepath: c:\xampp\htdocs\ADM-QLCT\expense-management\app\config\config.php

// Kiểm tra các hằng số đã được định nghĩa chưa
if (!defined('APPROOT')) {
    define('APPROOT', dirname(dirname(__FILE__))); // Đường dẫn đến thư mục app
}

if (!defined('ROOTPATH')) {
    define('ROOTPATH', dirname(dirname(dirname(__FILE__)))); // Đường dẫn đến thư mục gốc của dự án
}

// Cấu hình URL gốc
if (!defined('BASEURL')) {
    define('BASEURL', 'http://localhost/ADM-QLCT/expense-management');
}

// Cấu hình ứng dụng
if (!defined('SITENAME')) {
    define('SITENAME', 'Quản Lý Chi Tiêu');
}

if (!defined('APP_VERSION')) {
    define('APP_VERSION', '1.0.0');
}

// Cấu hình session - chỉ bắt đầu phiên nếu chưa được bắt đầu
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

// Timezone
date_default_timezone_set('Asia/Ho_Chi_Minh');

// Các cấu hình khác
if (!defined('DEFAULT_CONTROLLER')) {
    define('DEFAULT_CONTROLLER', 'Dashboard');
}

if (!defined('DEFAULT_METHOD')) {
    define('DEFAULT_METHOD', 'index');
}

if (!defined('DEFAULT_PARAMETER')) {
    define('DEFAULT_PARAMETER', []);
}

// Cấu hình debug mode
if (!defined('DEBUG_MODE')) {
    define('DEBUG_MODE', true); // Set to false in production
}

// Hiển thị lỗi nếu ở chế độ debug
if (DEBUG_MODE) {
    error_reporting(E_ALL);
    ini_set('display_errors', 1);
} else {
    error_reporting(0);
    ini_set('display_errors', 0);
}