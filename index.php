<?php
// Chuyển hướng tất cả yêu cầu đến public/index.php
header('Location: public/index.php');
exit;

// File index chính (điểm vào của ứng dụng)
require_once 'app/config/config.php';
require_once 'app/core/App.php';
require_once 'app/core/Router.php';
require_once 'app/core/Database.php';
require_once 'app/controllers/BaseController.php';

// Khởi tạo ứng dụng
$app = new App();