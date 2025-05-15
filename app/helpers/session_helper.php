<?php
/**
 * Kiểm tra người dùng đã đăng nhập hay chưa
 * @return bool
 */
function isLoggedIn() {
    return isset($_SESSION['user_id']);
}
?>