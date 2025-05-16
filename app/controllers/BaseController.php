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
            error_log("[BaseController] Loading view: " . $view);
            
            // Xây dựng đường dẫn đầy đủ đến file view
            $viewFile = APPROOT . '/views/' . $view . '.php';
            error_log("[BaseController] Full path: " . $viewFile);
            
            // Kiểm tra xem file view có tồn tại không
            if (!file_exists($viewFile)) {
                error_log("[BaseController] View file not found: " . $viewFile);
                throw new Exception("View file not found: " . $view);
            }
            
            // Hiện thị danh sách các file trong thư mục đó để debug
            $viewDir = dirname($viewFile);
            error_log("[BaseController] Checking directory: " . $viewDir);
            if (is_dir($viewDir)) {
                error_log("[BaseController] Directory exists, files:");
                foreach (scandir($viewDir) as $file) {
                    error_log("- " . $file);
                }
            }
            
            // Extract dữ liệu để sử dụng trong view
            extract($data);
            
            // Thực hiện include file view
            ob_start(); // Bắt đầu output buffering
            require $viewFile;
            $content = ob_get_clean(); // Lấy nội dung và xóa buffer
            
            echo $content; // Xuất nội dung
            
        } catch (Exception $e) {
            error_log("[BaseController] Error in view method: " . $e->getMessage());
            
            echo '<div style="margin: 30px; padding: 20px; border: 1px solid #dc3545; background-color: #f8d7da; color: #721c24; border-radius: 5px;">';
            echo '<h3>Lỗi khi hiển thị trang</h3>';
            echo '<p>' . htmlspecialchars($e->getMessage()) . '</p>';
            echo '<p>Vui lòng liên hệ quản trị viên hệ thống.</p>';
            echo '<p><a href="' . BASEURL . '/dashboard">Quay lại trang chủ</a></p>';
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