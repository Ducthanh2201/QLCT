<?php
/**
 * Controller cơ sở
 * Tải models và views
 */
class Controller {
    /**
     * Load model
     * @param string $model Tên model cần load
     * @return object Model đã khởi tạo
     */
    public function model($model) {
        // Đường dẫn đến file model
        $modelPath = '../app/models/' . $model . '.php';
        
        // Kiểm tra file tồn tại
        if (file_exists($modelPath)) {
            // Yêu cầu file model
            require_once $modelPath;
            // Khởi tạo model
            return new $model();
        } else {
            // Model không tồn tại
            die('Model ' . $model . ' không tồn tại');
        }
    }
    
    /**
     * Load view
     * @param string $view Tên view cần load
     * @param array $data Dữ liệu truyền vào view
     * @return void
     */
    public function view($view, $data = []) {
        // Đường dẫn đến file view
        $viewPath = '../app/views/' . $view . '.php';
        
        // Kiểm tra file view tồn tại
        if (file_exists($viewPath)) {
            require_once $viewPath;
        } else {
            // View không tồn tại
            die('View ' . $view . ' không tồn tại');
        }
    }
    
    /**
     * Chuyển hướng người dùng
     * @param string $url URL cần chuyển hướng
     * @return void
     */
    public function redirect($url) {
        header('Location: ' . BASEURL . '/' . $url);
        exit;
    }
    
    /**
     * Đặt flash message trong session
     * @param string $message Nội dung thông báo
     * @param string $type Loại thông báo (success, danger, warning, info)
     * @param string $title Tiêu đề thông báo (tùy chọn)
     * @param string $icon Icon cho thông báo (tùy chọn)
     * @return void
     */
    public function setFlash($message, $type = 'info', $title = '', $icon = '') {
        if (!isset($_SESSION)) {
            session_start();
        }
        
        $_SESSION['message'] = $message;
        $_SESSION['message_type'] = $type;
        
        if (!empty($title)) {
            $_SESSION['message_title'] = $title;
        }
        
        if (!empty($icon)) {
            $_SESSION['message_icon'] = $icon;
        }
    }
    
    /**
     * Kiểm tra yêu cầu là POST
     * @return bool
     */
    public function isPost() {
        return $_SERVER['REQUEST_METHOD'] === 'POST';
    }
    
    /**
     * Kiểm tra yêu cầu là GET
     * @return bool
     */
    public function isGet() {
        return $_SERVER['REQUEST_METHOD'] === 'GET';
    }
    
    /**
     * Lấy dữ liệu từ POST request
     * @param string $key Tên trường cần lấy
     * @param mixed $default Giá trị mặc định nếu không có
     * @return mixed
     */
    public function getPost($key = null, $default = null) {
        if ($key === null) {
            return $_POST;
        }
        
        return isset($_POST[$key]) ? $_POST[$key] : $default;
    }
    
    /**
     * Lấy dữ liệu từ GET request
     * @param string $key Tên tham số cần lấy
     * @param mixed $default Giá trị mặc định nếu không có
     * @return mixed
     */
    public function getQuery($key = null, $default = null) {
        if ($key === null) {
            return $_GET;
        }
        
        return isset($_GET[$key]) ? $_GET[$key] : $default;
    }
}