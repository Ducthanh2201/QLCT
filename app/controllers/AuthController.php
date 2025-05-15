<?php
class AuthController extends BaseController {
    private $userModel;
    
    public function __construct() {
        $this->userModel = $this->model('UserModel');
    }
    
    public function index() {
        // Chuyển hướng đến trang đăng nhập
        $this->login();
    }
    
    public function login() {
        // Nếu đã đăng nhập, chuyển hướng đến dashboard
        if(isset($_SESSION['user_id'])) {
            header('Location: ' . BASEURL . '/dashboard');
            exit;
        }
        
        // Xử lý form đăng nhập nếu là POST request
        if($_SERVER['REQUEST_METHOD'] === 'POST') {
            // Lấy dữ liệu từ form
            $email = trim($_POST['email'] ?? '');
            $password = $_POST['password'] ?? '';
            $remember = isset($_POST['remember']);
            
            // Kiểm tra dữ liệu nhập
            $errors = [];
            
            if(empty($email)) {
                $errors['email'] = 'Vui lòng nhập địa chỉ email';
            } elseif(!filter_var($email, FILTER_VALIDATE_EMAIL)) {
                $errors['email'] = 'Email không hợp lệ';
            }
            
            if(empty($password)) {
                $errors['password'] = 'Vui lòng nhập mật khẩu';
            }
            
            // Nếu không có lỗi validation, tiến hành đăng nhập
            if(empty($errors)) {
                // Xác thực người dùng
                $user = $this->userModel->login($email, $password);
                
                if($user) {
                    // Đăng nhập thành công
                    $this->createUserSession($user);
                    
                    // Xử lý remember me
                    if($remember) {
                        // Set cookie cho remember me (30 ngày)
                        $token = bin2hex(random_bytes(32));
                        setcookie('remember_token', $token, time() + 30*24*60*60, '/');
                        
                        // TODO: Lưu token vào database để xác thực sau này
                    }
                    
                    // Thêm thông báo thành công vào session
                    $_SESSION['success'] = 'Đăng nhập thành công!';
                    
                    // Nếu có URL chuyển hướng từ trước
                    if(isset($_SESSION['redirect_url'])) {
                        $redirectUrl = $_SESSION['redirect_url'];
                        unset($_SESSION['redirect_url']);
                        header('Location: ' . BASEURL . $redirectUrl);
                        exit;
                    }
                    
                    // Mặc định chuyển đến dashboard
                    header('Location: ' . BASEURL . '/dashboard');
                    exit;
                } else {
                    // Đăng nhập thất bại
                    $data = [
                        'email' => $email,
                        'error' => 'Email hoặc mật khẩu không đúng'
                    ];
                    
                    $this->view('auth/login', $data);
                    return;
                }
            } else {
                // Có lỗi validation
                $data = [
                    'email' => $email,
                    'errors' => $errors
                ];
                
                $this->view('auth/login', $data);
                return;
            }
        }
        
        // Hiển thị form đăng nhập
        $this->view('auth/login', []);
    }
    
    public function register() {
        // Nếu đã đăng nhập, chuyển hướng đến dashboard
        if(isset($_SESSION['user_id'])) {
            header('Location: ' . BASEURL . '/dashboard');
            exit;
        }
        
        // Xử lý form đăng ký nếu là POST request
        if($_SERVER['REQUEST_METHOD'] === 'POST') {
            // Lấy dữ liệu từ form
            $data = [
                'fullname' => trim($_POST['fullname'] ?? ''),
                'email' => trim($_POST['email'] ?? ''),
                'password' => $_POST['password'] ?? '',
                'confirm_password' => $_POST['confirm_password'] ?? '',
                'phone' => !empty($_POST['phone']) ? trim($_POST['phone']) : null,
                'currency' => $_POST['currency'] ?? 'VND',
                'terms' => isset($_POST['terms']),
                'errors' => []
            ];
            
            // Xác thực dữ liệu
            if(empty($data['fullname'])) {
                $data['errors']['fullname'] = 'Vui lòng nhập họ và tên';
            }
            
            if(empty($data['email'])) {
                $data['errors']['email'] = 'Vui lòng nhập email';
            } elseif(!filter_var($data['email'], FILTER_VALIDATE_EMAIL)) {
                $data['errors']['email'] = 'Email không hợp lệ';
            } elseif($this->userModel->findUserByEmail($data['email'])) {
                $data['errors']['email'] = 'Email đã được sử dụng';
            }
            
            if(empty($data['password'])) {
                $data['errors']['password'] = 'Vui lòng nhập mật khẩu';
            } elseif(strlen($data['password']) < 8) {
                $data['errors']['password'] = 'Mật khẩu phải có ít nhất 8 ký tự';
            } elseif(!preg_match('/[A-Z]/', $data['password'])) {
                $data['errors']['password'] = 'Mật khẩu phải chứa ít nhất 1 chữ hoa';
            } elseif(!preg_match('/[a-z]/', $data['password'])) {
                $data['errors']['password'] = 'Mật khẩu phải chứa ít nhất 1 chữ thường';
            } elseif(!preg_match('/[0-9]/', $data['password'])) {
                $data['errors']['password'] = 'Mật khẩu phải chứa ít nhất 1 số';
            }
            
            if($data['password'] !== $data['confirm_password']) {
                $data['errors']['confirm_password'] = 'Mật khẩu xác nhận không khớp';
            }
            
            if(!$data['terms']) {
                $data['errors']['terms'] = 'Bạn phải đồng ý với điều khoản và điều kiện';
            }
            
            // Nếu không có lỗi, thực hiện đăng ký
            if(empty($data['errors'])) {
                // Đăng ký người dùng
                $userId = $this->userModel->register($data);
                
                if($userId) {
                    // Tạo tài khoản mặc định cho người dùng
                    $this->createDefaultSettings($userId, $data['currency']);
                    
                    // Thêm thông báo thành công vào session
                    $_SESSION['success'] = 'Đăng ký thành công! Vui lòng đăng nhập.';
                    
                    // Chuyển hướng đến trang đăng nhập với email đã đăng ký
                    header('Location: ' . BASEURL . '/auth/login?registered_email=' . urlencode($data['email']));
                    exit;
                } else {
                    $data['error'] = 'Đã xảy ra lỗi khi đăng ký. Vui lòng thử lại.';
                }
            }
            
            // Nếu có lỗi, hiển thị lại form đăng ký với thông báo lỗi
            $this->view('auth/register', $data);
            return;
        }
        
        // Hiển thị form đăng ký
        $this->view('auth/register', []);
    }
    
    public function logout() {
        // Xóa dữ liệu session
        unset($_SESSION['user_id']);
        unset($_SESSION['user_email']);
        unset($_SESSION['user_name']);
        session_destroy();
        
        // Chuyển hướng đến trang đăng nhập
        header('Location: ' . BASEURL . '/auth/login');
        exit;
    }
    
    public function forgotPassword() {
        // Xử lý quên mật khẩu
        if($_SERVER['REQUEST_METHOD'] === 'POST') {
            // TODO: Xử lý gửi email đặt lại mật khẩu
            $email = $_POST['email'] ?? '';
            
            if(empty($email)) {
                $data['error'] = 'Vui lòng nhập email';
                $this->view('auth/forgot-password', $data);
                return;
            }
            
            // Kiểm tra email có tồn tại không
            if($this->userModel->findUserByEmail($email)) {
                // TODO: Gửi email với liên kết đặt lại mật khẩu
                
                // Thông báo thành công
                $data['success'] = 'Liên kết đặt lại mật khẩu đã được gửi đến email của bạn.';
            } else {
                $data['error'] = 'Email không tồn tại trong hệ thống.';
            }
            
            $this->view('auth/forgot-password', $data);
            return;
        }
        
        $this->view('auth/forgot-password', []);
    }
    
    public function resetPassword($token = null) {
        // TODO: Xử lý đặt lại mật khẩu
        $this->view('auth/reset-password', ['token' => $token]);
    }
    
    /**
     * Tạo session cho người dùng đăng nhập
     * @param object $user Thông tin người dùng
     */
    private function createUserSession($user) {
        // Thông tin cơ bản người dùng
        $_SESSION['user_id'] = $user->user_id;
        $_SESSION['user_email'] = $user->email;
        $_SESSION['user_name'] = $user->full_name;
        
        // Thêm thời gian đăng nhập
        $_SESSION['login_time'] = time();
        
        // Tải các cài đặt người dùng từ database
        $db = new Database();
        $db->query("SELECT * FROM user_settings WHERE user_id = :user_id");
        $db->bind(':user_id', $user->user_id);
        $settings = $db->single();
        
        if($settings) {
            $_SESSION['currency'] = $settings->currency ?? 'VND';
            $_SESSION['theme'] = $settings->theme ?? 'light';
            $_SESSION['language'] = $settings->language ?? 'vi';
            $_SESSION['date_format'] = $settings->date_format ?? 'd/m/Y';
            $_SESSION['notifications_enabled'] = $settings->notifications_enabled ?? true;
            $_SESSION['budget_alerts'] = $settings->budget_alerts ?? true;
        }
    }
    
    /**
     * Tạo cài đặt mặc định cho người dùng mới
     * @param int $userId ID người dùng
     * @param string $currency Đơn vị tiền tệ mặc định
     */
    private function createDefaultSettings($userId, $currency) {
        // Tạo bảng cài đặt cho người dùng
        $db = new Database();
        $db->query("INSERT INTO user_settings (user_id, currency, theme, language, created_at) 
                   VALUES (:user_id, :currency, 'light', 'vi', NOW())");
        $db->bind(':user_id', $userId);
        $db->bind(':currency', $currency);
        $db->execute();
        
        // Tạo các danh mục chi tiêu mặc định
        $defaultCategories = [
            ['name' => 'Ăn uống', 'type' => 'expense', 'icon' => 'food.png', 'color' => '#FF5722'],
            ['name' => 'Di chuyển', 'type' => 'expense', 'icon' => 'transport.png', 'color' => '#2196F3'],
            ['name' => 'Mua sắm', 'type' => 'expense', 'icon' => 'shopping.png', 'color' => '#9C27B0'],
            ['name' => 'Hóa đơn', 'type' => 'expense', 'icon' => 'bill.png', 'color' => '#F44336'],
            ['name' => 'Giải trí', 'type' => 'expense', 'icon' => 'entertainment.png', 'color' => '#4CAF50'],
            ['name' => 'Lương', 'type' => 'income', 'icon' => 'salary.png', 'color' => '#4CAF50'],
            ['name' => 'Thưởng', 'type' => 'income', 'icon' => 'bonus.png', 'color' => '#FF9800'],
            ['name' => 'Quà tặng', 'type' => 'income', 'icon' => 'gift.png', 'color' => '#E91E63'],
            ['name' => 'Đầu tư', 'type' => 'income', 'icon' => 'investment.png', 'color' => '#2196F3']
        ];
        
        // Kiểm tra xem có danh mục nào đã tồn tại trước khi thêm
        foreach($defaultCategories as $category) {
            // Kiểm tra xem danh mục đã tồn tại cho người dùng này chưa
            $db->query("SELECT * FROM categories WHERE user_id = :user_id AND name = :name AND type = :type");
            $db->bind(':user_id', $userId);
            $db->bind(':name', $category['name']);
            $db->bind(':type', $category['type']);
            $db->execute();
            
            // Nếu chưa có, thêm mới
            if($db->rowCount() == 0) {
                $db->query("INSERT INTO categories (user_id, name, type, icon, color, created_at) 
                           VALUES (:user_id, :name, :type, :icon, :color, NOW())");
                $db->bind(':user_id', $userId);
                $db->bind(':name', $category['name']);
                $db->bind(':type', $category['type']);
                $db->bind(':icon', $category['icon']);
                $db->bind(':color', $category['color']);
                $db->execute();
            }
        }
    }
}