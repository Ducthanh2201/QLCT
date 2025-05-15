<?php
class AuthMiddleware {
    public static function isLoggedIn() {
        if(isset($_SESSION['user_id'])) {
            return true;
        }
        return false;
    }
    
    public static function requireLogin() {
        if(!self::isLoggedIn()) {
            // Lưu URL hiện tại vào session để sau khi đăng nhập có thể quay lại
            $_SESSION['redirect_url'] = $_SERVER['REQUEST_URI'];
            
            header('Location: ' . BASEURL . '/auth/login');
            exit;
        }
    }
    
    public static function requireGuest() {
        if(self::isLoggedIn()) {
            header('Location: ' . BASEURL . '/dashboard');
            exit;
        }
    }
}