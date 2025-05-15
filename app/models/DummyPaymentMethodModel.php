<?php
class DummyPaymentMethodModel {
    public function getBankAccounts($userId) {
        return [];
    }
    
    public function getPaymentCards($userId) {
        return [];
    }
    
    // Thêm các phương thức khác mà PaymentMethodModel có thể có
    public function getAllPaymentMethods() {
        return [];
    }
    
    public function addBankAccount($data) {
        return false;
    }
    
    public function addPaymentCard($data) {
        return false;
    }
}