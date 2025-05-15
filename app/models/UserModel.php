<?php
class UserModel {
    private $db;
    
    public function __construct() {
        $this->db = new Database();
    }
    
    /**
     * Đăng ký người dùng mới
     * @param array $data Dữ liệu người dùng
     * @return bool|int ID của người dùng nếu thành công, false nếu thất bại
     */
    public function register($data) {
        // Hash mật khẩu trước khi lưu vào CSDL
        $password_hash = password_hash($data['password'], PASSWORD_BCRYPT);
        
        // Chuẩn bị câu truy vấn
        $this->db->query("INSERT INTO users (full_name, email, password_hash, phone, created_at) 
                         VALUES (:full_name, :email, :password_hash, :phone, NOW())");
                         
        // Bind các tham số
        $this->db->bind(':full_name', $data['fullname']);
        $this->db->bind(':email', $data['email']);
        $this->db->bind(':password_hash', $password_hash);
        $this->db->bind(':phone', $data['phone'] ?? null);
        
        // Thực thi truy vấn
        if($this->db->execute()) {
            return $this->db->lastInsertId();
        } else {
            return false;
        }
    }
    
    /**
     * Kiểm tra email đã tồn tại chưa
     * @param string $email Email cần kiểm tra
     * @return bool true nếu email đã tồn tại, false nếu chưa
     */
    public function findUserByEmail($email) {
        $this->db->query("SELECT * FROM users WHERE email = :email");
        $this->db->bind(':email', $email);
        
        $row = $this->db->single();
        
        // Nếu có kết quả, email đã tồn tại
        return ($row) ? true : false;
    }
    
    /**
     * Lấy thông tin người dùng theo ID
     * @param int $id ID người dùng
     * @return object|bool Thông tin người dùng hoặc false nếu không tìm thấy
     */
    public function getUserById($id) {
        $this->db->query("SELECT user_id, full_name, email, phone, created_at FROM users WHERE user_id = :id");
        $this->db->bind(':id', $id);
        
        return $this->db->single();
    }
    
    /**
     * Lấy thông tin người dùng theo email
     * @param string $email Email người dùng
     * @return object|bool Thông tin người dùng hoặc false nếu không tìm thấy
     */
    public function getUserByEmail($email) {
        $this->db->query("SELECT * FROM users WHERE email = :email");
        $this->db->bind(':email', $email);
        
        return $this->db->single();
    }
    
    /**
     * Đăng nhập người dùng
     * @param string $email Email người dùng
     * @param string $password Mật khẩu người dùng
     * @return object|bool Thông tin người dùng nếu đăng nhập thành công, false nếu thất bại
     */
    public function login($email, $password) {
        $user = $this->getUserByEmail($email);
        
        if(!$user) {
            return false;
        }
        
        // Kiểm tra mật khẩu
        if(password_verify($password, $user->password_hash)) {
            return $user;
        } else {
            return false;
        }
    }
    
    /**
     * Cập nhật thông tin người dùng
     * @param array $data Dữ liệu cần cập nhật
     * @return bool true nếu thành công, false nếu thất bại
     */
    public function updateUser($data) {
        $this->db->query("UPDATE users SET full_name = :full_name, phone = :phone WHERE user_id = :id");
        $this->db->bind(':full_name', $data['full_name']);
        $this->db->bind(':phone', $data['phone'] ?? null);
        $this->db->bind(':id', $data['user_id']);
        
        return $this->db->execute();
    }
    
    /**
     * Cập nhật mật khẩu người dùng
     * @param int $userId ID người dùng
     * @param string $newPassword Mật khẩu mới
     * @return bool true nếu thành công, false nếu thất bại
     */
    public function updatePassword($userId, $newPassword) {
        $password_hash = password_hash($newPassword, PASSWORD_BCRYPT);
        
        $this->db->query("UPDATE users SET password_hash = :password_hash WHERE user_id = :id");
        $this->db->bind(':password_hash', $password_hash);
        $this->db->bind(':id', $userId);
        
        return $this->db->execute();
    }
}