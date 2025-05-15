<?php
class BaseController {
    protected $db;
    
    public function __construct() {
        // Đảm bảo session đã được khởi tạo
        if (session_status() == PHP_SESSION_NONE) {
            session_start();
        }
        
        // Khởi tạo đối tượng Database để sử dụng trong các controllers con
        $this->db = $this->loadDatabase();
    }
    
    // Hàm load model
    public function model($model) {
        $modelFile = '../app/models/' . $model . '.php';
        if(file_exists($modelFile)) {
            require_once $modelFile;
            return new $model();
        } else {
            die('Model ' . $model . ' không tồn tại');
        }
    }
    
    // Hàm load view đã cải tiến
    public function view($view, $data = []) {
        try {
            error_log("[View] Attempting to load view: " . $view);
            
            // Xây dựng đường dẫn đầy đủ đến file view
            $viewFile = APPROOT . '/views/' . $view . '.php';
            error_log("[View] Full path: " . $viewFile);
            
            // Kiểm tra xem file view có tồn tại không
            if (!file_exists($viewFile)) {
                error_log("[View] View file not found: " . $viewFile);
                
                // Thử tìm với tên khác cho expense detail view
                if ($view === 'expenses/expense_detail' || $view === 'expenses/detail') {
                    $alternativePaths = [
                        APPROOT . '/views/expenses/expense_detail.php',
                        APPROOT . '/views/expenses/detail.php',
                        APPROOT . '/views/expenses/view.php'
                    ];
                    
                    foreach ($alternativePaths as $path) {
                        error_log("[View] Trying alternative path: " . $path);
                        if (file_exists($path)) {
                            $viewFile = $path;
                            error_log("[View] Found alternative view file: " . $viewFile);
                            break;
                        }
                    }
                }
                
                // Nếu vẫn không tìm thấy file view
                if (!file_exists($viewFile)) {
                    // Thêm kiểm tra cuối cùng để debug
                    $testPath = APPROOT . '/views/expenses';
                    error_log("[View] Checking directory: " . $testPath);
                    if (is_dir($testPath)) {
                        error_log("[View] Directory exists, listing files:");
                        foreach (scandir($testPath) as $f) {
                            error_log("- " . $f);
                        }
                    }
                    
                    throw new Exception("View file not found: " . $view);
                }
            }
            
            // Extract dữ liệu để sử dụng trong view
            extract($data);
            
            // Thực hiện include file view
            require $viewFile;
            
        } catch (Exception $e) {
            error_log("[View] Error: " . $e->getMessage());
            
            // Thay vì chuyển hướng, hiển thị thông báo lỗi thân thiện
            echo '<div style="margin: 30px; padding: 20px; border: 1px solid #dc3545; background-color: #f8d7da; color: #721c24; border-radius: 5px;">';
            echo '<h3>Lỗi khi hiển thị trang</h3>';
            echo '<p>' . htmlspecialchars($e->getMessage()) . '</p>';
            echo '<p>Vui lòng liên hệ quản trị viên hệ thống.</p>';
            
            if(defined('ENVIRONMENT') && ENVIRONMENT === 'development') {
                echo '<hr>';
                echo '<h4>Chi tiết lỗi (chỉ hiển thị trong môi trường phát triển):</h4>';
                echo '<pre>' . htmlspecialchars($e->getTraceAsString()) . '</pre>';
            }
            
            echo '<p><a href="' . BASEURL . '/expenses">Quay lại danh sách chi tiêu</a></p>';
            echo '</div>';
        }
    }
    
    // Hàm load database
    protected function loadDatabase() {
        // Kiểm tra nếu class Database đã được định nghĩa
        if(class_exists('Database')) {
            return new Database();
        }
        
        // Nếu chưa thì load file Database.php
        $databaseFile = '../app/libraries/Database.php';
        if(file_exists($databaseFile)) {
            require_once $databaseFile;
            return new Database();
        } else {
            die('Database library không tồn tại');
        }
    }
    
    /**
     * Chuyển hướng đến một URL cụ thể
     * @param string $url Đường dẫn cần chuyển hướng (không bao gồm BASEURL)
     * @return void
     */
    protected function redirect($url) {
        // Loại bỏ dấu / ở đầu nếu có
        $url = ltrim($url, '/');
        // Thêm log để debug
        error_log("Redirecting to: " . BASEURL . '/' . $url);
        header('Location: ' . BASEURL . '/' . $url);
        exit;
    }
}