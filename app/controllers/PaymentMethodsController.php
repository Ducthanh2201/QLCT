<?php
// Đảm bảo các helper files được load
require_once APPROOT . '/helpers/session_helper.php';
require_once APPROOT . '/helpers/helpers.php';

class PaymentMethodsController extends BaseController {
    private $paymentMethodModel;
    
    public function __construct() {
        parent::__construct();
        
        // Kiểm tra đăng nhập
        if(!isLoggedIn()) {
            redirect('/auth/login');
        }
        
        // Kiểm tra file PaymentMethodModel tồn tại
        $paymentMethodFile = APPROOT . '/models/PaymentMethodModel.php';
        
        if(file_exists($paymentMethodFile)) {
            require_once $paymentMethodFile;
            $this->paymentMethodModel = new PaymentMethodModel();
        } else {
            // Tạo file PaymentMethodModel.php
            $this->createPaymentMethodModel($paymentMethodFile);
            require_once $paymentMethodFile;
            $this->paymentMethodModel = new PaymentMethodModel();
        }
    }
    
    /**
     * Tạo file PaymentMethodModel.php
     * @param string $filePath Đường dẫn đến file
     */
    private function createPaymentMethodModel($filePath) {
        $content = "<?php\nclass PaymentMethodModel {\n";
        $content .= "    private \$db;\n\n";
        $content .= "    public function __construct() {\n";
        $content .= "        \$this->db = new Database();\n    }\n\n";
        $content .= "    public function getAllPaymentMethods() {\n";
        $content .= "        \$this->db->query(\"SELECT * FROM payment_methods ORDER BY method_id\");\n";
        $content .= "        return \$this->db->resultSet();\n    }\n\n";
        $content .= "    public function getBankAccounts(\$userId) {\n";
        $content .= "        \$this->db->query(\"SELECT * FROM bank_accounts WHERE user_id = :user_id ORDER BY is_default DESC, bank_name ASC\");\n";
        $content .= "        \$this->db->bind(':user_id', \$userId);\n";
        $content .= "        return \$this->db->resultSet();\n    }\n\n";
        $content .= "    public function getPaymentCards(\$userId) {\n";
        $content .= "        \$this->db->query(\"SELECT * FROM payment_cards WHERE user_id = :user_id ORDER BY is_default DESC, bank_name ASC\");\n";
        $content .= "        \$this->db->bind(':user_id', \$userId);\n";
        $content .= "        return \$this->db->resultSet();\n    }\n\n";
        $content .= "    public function addBankAccount(\$data) {\n";
        $content .= "        // Nếu tài khoản này được đặt là mặc định, cập nhật các tài khoản khác không còn là mặc định\n";
        $content .= "        if(isset(\$data['is_default']) && \$data['is_default'] == 1) {\n";
        $content .= "            \$this->db->query(\"UPDATE bank_accounts SET is_default = 0 WHERE user_id = :user_id\");\n";
        $content .= "            \$this->db->bind(':user_id', \$data['user_id']);\n";
        $content .= "            \$this->db->execute();\n";
        $content .= "        }\n\n";
        $content .= "        \$this->db->query(\"INSERT INTO bank_accounts (user_id, bank_name, account_number, account_holder, branch, is_default)
            VALUES (:user_id, :bank_name, :account_number, :account_holder, :branch, :is_default)\");\n\n";
        $content .= "        \$this->db->bind(':user_id', \$data['user_id']);\n";
        $content .= "        \$this->db->bind(':bank_name', \$data['bank_name']);\n";
        $content .= "        \$this->db->bind(':account_number', \$data['account_number']);\n";
        $content .= "        \$this->db->bind(':account_holder', \$data['account_holder']);\n";
        $content .= "        \$this->db->bind(':branch', \$data['branch'] ?? '');\n";
        $content .= "        \$this->db->bind(':is_default', \$data['is_default'] ?? 0);\n\n";
        $content .= "        if(\$this->db->execute()) {\n";
        $content .= "            return \$this->db->lastInsertId();\n";
        $content .= "        } else {\n";
        $content .= "            return false;\n";
        $content .= "        }\n    }\n\n";
        $content .= "    public function addPaymentCard(\$data) {\n";
        $content .= "        // Nếu thẻ này được đặt là mặc định, cập nhật các thẻ khác không còn là mặc định\n";
        $content .= "        if(isset(\$data['is_default']) && \$data['is_default'] == 1) {\n";
        $content .= "            \$this->db->query(\"UPDATE payment_cards SET is_default = 0 WHERE user_id = :user_id\");\n";
        $content .= "            \$this->db->bind(':user_id', \$data['user_id']);\n";
        $content .= "            \$this->db->execute();\n";
        $content .= "        }\n\n";
        $content .= "        \$this->db->query(\"INSERT INTO payment_cards (user_id, bank_name, card_number, card_holder, expiry_date, card_type, is_default)
            VALUES (:user_id, :bank_name, :card_number, :card_holder, :expiry_date, :card_type, :is_default)\");\n\n";
        $content .= "        \$this->db->bind(':user_id', \$data['user_id']);\n";
        $content .= "        \$this->db->bind(':bank_name', \$data['bank_name']);\n";
        $content .= "        \$this->db->bind(':card_number', \$data['card_number']);\n";
        $content .= "        \$this->db->bind(':card_holder', \$data['card_holder']);\n";
        $content .= "        \$this->db->bind(':expiry_date', \$data['expiry_date']);\n";
        $content .= "        \$this->db->bind(':card_type', \$data['card_type']);\n";
        $content .= "        \$this->db->bind(':is_default', \$data['is_default'] ?? 0);\n\n";
        $content .= "        if(\$this->db->execute()) {\n";
        $content .= "            return \$this->db->lastInsertId();\n";
        $content .= "        } else {\n";
        $content .= "            return false;\n";
        $content .= "        }\n    }\n";
        $content .= "}\n";
        
        file_put_contents($filePath, $content);
    }
    
    /**
     * Trang quản lý tài khoản ngân hàng và thẻ thanh toán
     */
    public function index() {
        $bankAccounts = $this->paymentMethodModel->getBankAccounts($_SESSION['user_id']);
        $cards = $this->paymentMethodModel->getPaymentCards($_SESSION['user_id']);
        
        $data = [
            'title' => 'Quản lý phương thức thanh toán',
            'bank_accounts' => $bankAccounts,
            'cards' => $cards
        ];
        
        $this->view('payment-methods/index', $data);
    }
    
    /**
     * Trang thêm tài khoản ngân hàng
     */
    public function addBankAccount() {
        if($_SERVER['REQUEST_METHOD'] === 'POST') {
            $data = [
                'user_id' => $_SESSION['user_id'],
                'bank_name' => $_POST['bank_name'] ?? '',
                'account_number' => $_POST['account_number'] ?? '',
                'account_holder' => $_POST['account_holder'] ?? '',
                'branch' => $_POST['branch'] ?? '',
                'is_default' => isset($_POST['is_default']) ? 1 : 0,
                'errors' => []
            ];
            
            // Xác thực dữ liệu
            if(empty($data['bank_name'])) {
                $data['errors']['bank_name'] = 'Vui lòng nhập tên ngân hàng';
            }
            
            if(empty($data['account_number'])) {
                $data['errors']['account_number'] = 'Vui lòng nhập số tài khoản';
            }
            
            if(empty($data['account_holder'])) {
                $data['errors']['account_holder'] = 'Vui lòng nhập tên chủ tài khoản';
            }
            
            // Nếu không có lỗi, thêm tài khoản mới
            if(empty($data['errors'])) {
                $account_id = $this->paymentMethodModel->addBankAccount($data);
                
                if($account_id) {
                    $_SESSION['success'] = 'Thêm tài khoản ngân hàng thành công!';
                    header('Location: ' . BASEURL . '/payment-methods');
                    exit;
                } else {
                    $data['errors']['general'] = 'Có lỗi xảy ra khi thêm tài khoản. Vui lòng thử lại!';
                }
            }
            
            $this->view('payment-methods/add-bank-account', $data);
        } else {
            $data = [
                'title' => 'Thêm tài khoản ngân hàng',
                'bank_name' => '',
                'account_number' => '',
                'account_holder' => '',
                'branch' => '',
                'is_default' => 0,
                'errors' => []
            ];
            
            $this->view('payment-methods/add-bank-account', $data);
        }
    }
    
    /**
     * Trang thêm thẻ thanh toán
     */
    public function addPaymentCard() {
        if($_SERVER['REQUEST_METHOD'] === 'POST') {
            $data = [
                'user_id' => $_SESSION['user_id'],
                'bank_name' => $_POST['bank_name'] ?? '',
                'card_number' => $_POST['card_number'] ?? '',
                'card_holder' => $_POST['card_holder'] ?? '',
                'expiry_date' => $_POST['expiry_date'] ?? '',
                'card_type' => $_POST['card_type'] ?? '',
                'is_default' => isset($_POST['is_default']) ? 1 : 0,
                'errors' => []
            ];
            
            // Xác thực dữ liệu
            if(empty($data['bank_name'])) {
                $data['errors']['bank_name'] = 'Vui lòng nhập tên ngân hàng';
            }
            
            if(empty($data['card_number'])) {
                $data['errors']['card_number'] = 'Vui lòng nhập số thẻ';
            }
            
            if(empty($data['card_holder'])) {
                $data['errors']['card_holder'] = 'Vui lòng nhập tên chủ thẻ';
            }
            
            if(empty($data['expiry_date'])) {
                $data['errors']['expiry_date'] = 'Vui lòng nhập ngày hết hạn';
            }
            
            if(empty($data['card_type'])) {
                $data['errors']['card_type'] = 'Vui lòng chọn loại thẻ';
            }
            
            // Nếu không có lỗi, thêm thẻ mới
            if(empty($data['errors'])) {
                $card_id = $this->paymentMethodModel->addPaymentCard($data);
                
                if($card_id) {
                    $_SESSION['success'] = 'Thêm thẻ thanh toán thành công!';
                    header('Location: ' . BASEURL . '/payment-methods');
                    exit;
                } else {
                    $data['errors']['general'] = 'Có lỗi xảy ra khi thêm thẻ. Vui lòng thử lại!';
                }
            }
            
            $this->view('payment-methods/add-payment-card', $data);
        } else {
            $data = [
                'title' => 'Thêm thẻ thanh toán',
                'bank_name' => '',
                'card_number' => '',
                'card_holder' => '',
                'expiry_date' => '',
                'card_type' => '',
                'is_default' => 0,
                'errors' => []
            ];
            
            $this->view('payment-methods/add-payment-card', $data);
        }
    }
    
    /**
     * Chỉnh sửa tài khoản ngân hàng
     */
    public function editBankAccount($id) {
        // Code của phương thức editBankAccount
    }
    
    /**
     * Chỉnh sửa thẻ thanh toán
     */
    public function editPaymentCard($id) {
        // Code của phương thức editPaymentCard
    }
    
    /**
     * Xóa tài khoản ngân hàng
     */
    public function deleteBankAccount($id) {
        // Code của phương thức deleteBankAccount
    }
    
    /**
     * Xóa thẻ thanh toán
     */
    public function deletePaymentCard($id) {
        // Code của phương thức deletePaymentCard
    }
}