<?php
class PaymentMethodModel {
    private $db;
    
    public function __construct() {
        $this->db = new Database();
    }
    
    /**
     * Lấy tất cả phương thức thanh toán
     * @return array Danh sách phương thức thanh toán
     */
    public function getAllPaymentMethods() {
        $this->db->query("SELECT * FROM payment_methods ORDER BY method_id");
        return $this->db->resultSet();
    }
    
    /**
     * Lấy danh sách tài khoản ngân hàng của người dùng
     * @param int $userId ID của người dùng
     * @return array Danh sách tài khoản ngân hàng
     */
    public function getBankAccounts($userId) {
        $this->db->query("SELECT * FROM bank_accounts WHERE user_id = :user_id ORDER BY is_default DESC, bank_name ASC");
        $this->db->bind(':user_id', $userId);
        return $this->db->resultSet();
    }
    
    /**
     * Lấy danh sách thẻ thanh toán của người dùng
     * @param int $userId ID của người dùng
     * @return array Danh sách thẻ thanh toán
     */
    public function getPaymentCards($userId) {
        $this->db->query("SELECT * FROM payment_cards WHERE user_id = :user_id ORDER BY is_default DESC, bank_name ASC");
        $this->db->bind(':user_id', $userId);
        return $this->db->resultSet();
    }
    
    /**
     * Thêm tài khoản ngân hàng mới
     * @param array $data Dữ liệu tài khoản ngân hàng
     * @return int|bool ID của tài khoản mới hoặc false nếu có lỗi
     */
    public function addBankAccount($data) {
        // Nếu tài khoản này được đặt là mặc định, cập nhật các tài khoản khác không còn là mặc định
        if(isset($data['is_default']) && $data['is_default'] == 1) {
            $this->db->query("UPDATE bank_accounts SET is_default = 0 WHERE user_id = :user_id");
            $this->db->bind(':user_id', $data['user_id']);
            $this->db->execute();
        }
        
        $this->db->query("
            INSERT INTO bank_accounts (user_id, bank_name, account_number, account_holder, branch, is_default)
            VALUES (:user_id, :bank_name, :account_number, :account_holder, :branch, :is_default)
        ");
        
        $this->db->bind(':user_id', $data['user_id']);
        $this->db->bind(':bank_name', $data['bank_name']);
        $this->db->bind(':account_number', $data['account_number']);
        $this->db->bind(':account_holder', $data['account_holder']);
        $this->db->bind(':branch', $data['branch'] ?? '');
        $this->db->bind(':is_default', $data['is_default'] ?? 0);
        
        if($this->db->execute()) {
            return $this->db->lastInsertId();
        } else {
            return false;
        }
    }
    
    /**
     * Thêm thẻ thanh toán mới
     * @param array $data Dữ liệu thẻ thanh toán
     * @return int|bool ID của thẻ mới hoặc false nếu có lỗi
     */
    public function addPaymentCard($data) {
        // Nếu thẻ này được đặt là mặc định, cập nhật các thẻ khác không còn là mặc định
        if(isset($data['is_default']) && $data['is_default'] == 1) {
            $this->db->query("UPDATE payment_cards SET is_default = 0 WHERE user_id = :user_id");
            $this->db->bind(':user_id', $data['user_id']);
            $this->db->execute();
        }
        
        $this->db->query("
            INSERT INTO payment_cards (user_id, bank_name, card_number, card_holder, expiry_date, card_type, is_default)
            VALUES (:user_id, :bank_name, :card_number, :card_holder, :expiry_date, :card_type, :is_default)
        ");
        
        $this->db->bind(':user_id', $data['user_id']);
        $this->db->bind(':bank_name', $data['bank_name']);
        $this->db->bind(':card_number', $data['card_number']);
        $this->db->bind(':card_holder', $data['card_holder']);
        $this->db->bind(':expiry_date', $data['expiry_date']);
        $this->db->bind(':card_type', $data['card_type']);
        $this->db->bind(':is_default', $data['is_default'] ?? 0);
        
        if($this->db->execute()) {
            return $this->db->lastInsertId();
        } else {
            return false;
        }
    }

    /**
     * Lấy thông tin tài khoản ngân hàng theo ID
     * @param int $accountId ID của tài khoản ngân hàng
     * @param int $userId ID của người dùng (để kiểm tra quyền)
     * @return object|bool Thông tin tài khoản hoặc false nếu không tìm thấy
     */
    public function getBankAccountById($accountId, $userId) {
        $this->db->query("SELECT * FROM bank_accounts WHERE id = :id AND user_id = :user_id");
        $this->db->bind(':id', $accountId);
        $this->db->bind(':user_id', $userId);
        return $this->db->single();
    }

    /**
     * Lấy thông tin thẻ thanh toán theo ID
     * @param int $cardId ID của thẻ thanh toán
     * @param int $userId ID của người dùng (để kiểm tra quyền)
     * @return object|bool Thông tin thẻ hoặc false nếu không tìm thấy
     */
    public function getPaymentCardById($cardId, $userId) {
        $this->db->query("SELECT * FROM payment_cards WHERE id = :id AND user_id = :user_id");
        $this->db->bind(':id', $cardId);
        $this->db->bind(':user_id', $userId);
        return $this->db->single();
    }
}