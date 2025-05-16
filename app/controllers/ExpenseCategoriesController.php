<?php
class ExpenseCategoriesController extends BaseController {
    private $categoryModel;
    
    public function __construct() {
        parent::__construct(); // Thêm gọi parent constructor
        
        // Kiểm tra đăng nhập
        if(!isset($_SESSION['user_id'])) {
            $this->redirect('auth/login');
            exit;
        }
        
        $this->categoryModel = $this->model('CategoryModel');
    }
    
    /**
     * Trang danh sách danh mục chi tiêu
     */
    public function index() {
        try {
            error_log("[ExpenseCategoriesController] Executing index method");
            
            // Lấy danh sách danh mục chi tiêu
            $categories = $this->categoryModel->getCategoriesByType($_SESSION['user_id'], 'expense');
            
            error_log("[ExpenseCategoriesController] Found " . count($categories) . " categories");
            
            $data = [
                'title' => 'Danh mục chi tiêu',
                'categories' => $categories
            ];
            
            $this->view('expense-categories/index', $data);
        } catch (Exception $e) {
            error_log("[ExpenseCategoriesController] Error in index: " . $e->getMessage());
            $_SESSION['error'] = 'Đã xảy ra lỗi: ' . $e->getMessage();
            var_dump($e);    
        }
    }
    
    /**
     * Trang thêm danh mục chi tiêu mới
     */
    public function create() {
        // Nếu là POST request, xử lý thêm danh mục
        if($_SERVER['REQUEST_METHOD'] === 'POST') {
            // Lấy dữ liệu từ form
            $data = [
                'name' => trim($_POST['name'] ?? ''),
                'description' => trim($_POST['description'] ?? ''),
                'user_id' => $_SESSION['user_id'],
                'type' => 'expense',
                'icon' => $_POST['icon'] ?? 'default.png',
                'color' => $_POST['color'] ?? '#4CAF50',
                'errors' => []
            ];
            
            // Xác thực dữ liệu
            if(empty($data['name'])) {
                $data['errors']['name'] = 'Vui lòng nhập tên danh mục';
            } elseif($this->categoryModel->categoryNameExists($data['name'], $data['user_id'], $data['type'])) {
                $data['errors']['name'] = 'Tên danh mục đã tồn tại';
            }
            
            // Nếu không có lỗi, thực hiện thêm danh mục
            if(empty($data['errors'])) {
                $categoryId = $this->categoryModel->addCategory($data);
                
                if($categoryId) {
                    // Thêm thông báo thành công vào session
                    $_SESSION['success'] = 'Thêm danh mục chi tiêu thành công!';
                    
                    // Chuyển hướng đến trang danh sách danh mục
                    header('Location: ' . BASEURL . '/expense-categories');
                    exit;
                } else {
                    $data['error'] = 'Đã xảy ra lỗi khi thêm danh mục. Vui lòng thử lại.';
                }
            }
            
            // Nếu có lỗi, hiển thị lại form với thông báo lỗi
            $data['title'] = 'Thêm danh mục chi tiêu mới';
            $data['icons'] = $this->getAvailableIcons();
            $this->view('expense-categories/create', $data);
            return;
        }
        
        // Hiển thị form thêm danh mục
        $data = [
            'title' => 'Thêm danh mục chi tiêu mới',
            'icons' => $this->getAvailableIcons()
        ];
        
        $this->view('expense-categories/create', $data);
    }
    
    /**
     * Trang sửa danh mục chi tiêu
     * @param int $id ID của danh mục
     */
    public function edit($id) {
        // Lấy chi tiết danh mục
        $category = $this->categoryModel->getCategoryById($id, $_SESSION['user_id']);
        
        if(!$category) {
            // Nếu không tìm thấy danh mục
            $_SESSION['error'] = 'Không tìm thấy danh mục này!';
            header('Location: ' . BASEURL . '/expense-categories');
            exit;
        }
        
        // Nếu là POST request, xử lý cập nhật danh mục
        if($_SERVER['REQUEST_METHOD'] === 'POST') {
            // Lấy dữ liệu từ form
            $data = [
                'category_id' => $id,
                'name' => trim($_POST['name'] ?? ''),
                'description' => trim($_POST['description'] ?? ''),
                'user_id' => $_SESSION['user_id'],
                'icon' => $_POST['icon'] ?? $category->icon,
                'color' => $_POST['color'] ?? $category->color,
                'errors' => []
            ];
            
            // Xác thực dữ liệu
            if(empty($data['name'])) {
                $data['errors']['name'] = 'Vui lòng nhập tên danh mục';
            } elseif($data['name'] !== $category->name && 
                    $this->categoryModel->categoryNameExists($data['name'], $data['user_id'], 'expense', $id)) {
                $data['errors']['name'] = 'Tên danh mục đã tồn tại';
            }
            
            // Nếu không có lỗi, thực hiện cập nhật danh mục
            if(empty($data['errors'])) {
                $result = $this->categoryModel->updateCategory($data);
                
                if($result) {
                    // Thêm thông báo thành công vào session
                    $_SESSION['success'] = 'Cập nhật danh mục thành công!';
                    
                    // Chuyển hướng đến trang danh sách danh mục
                    header('Location: ' . BASEURL . '/expense-categories');
                    exit;
                } else {
                    $data['error'] = 'Đã xảy ra lỗi khi cập nhật danh mục. Vui lòng thử lại.';
                }
            }
            
            // Nếu có lỗi, hiển thị lại form với thông báo lỗi
            $data['title'] = 'Sửa danh mục chi tiêu';
            $data['category'] = $category;
            $data['icons'] = $this->getAvailableIcons();
            
            $this->view('expense-categories/edit', $data);
            return;
        }
        
        // Hiển thị form sửa danh mục
        $data = [
            'title' => 'Sửa danh mục chi tiêu',
            'category' => $category,
            'icons' => $this->getAvailableIcons()
        ];
        
        $this->view('expense-categories/edit', $data);
    }
    
    /**
     * Xóa danh mục chi tiêu
     * @param int $id ID của danh mục
     */
    public function delete($id) {
        // Kiểm tra xem có phải POST request không
        if($_SERVER['REQUEST_METHOD'] !== 'POST') {
            header('Location: ' . BASEURL . '/expense-categories');
            exit;
        }
        
        // Kiểm tra xem danh mục có thuộc về người dùng không
        $category = $this->categoryModel->getCategoryById($id, $_SESSION['user_id']);
        
        if(!$category) {
            // Nếu không tìm thấy danh mục
            $_SESSION['error'] = 'Không tìm thấy danh mục này!';
            header('Location: ' . BASEURL . '/expense-categories');
            exit;
        }
        
        // Xóa danh mục
        $result = $this->categoryModel->deleteCategory($id, $_SESSION['user_id']);
        
        if($result) {
            // Thêm thông báo thành công vào session
            $_SESSION['success'] = 'Xóa danh mục thành công!';
        } else {
            $_SESSION['error'] = 'Không thể xóa danh mục đã có chi tiêu.';
        }
        
        // Chuyển hướng đến trang danh sách danh mục
        header('Location: ' . BASEURL . '/expense-categories');
        exit;
    }
    
    /**
     * Sao chép danh mục chi tiêu
     * @param int $id ID của danh mục cần sao chép
     */
    public function duplicate($id) {
        // Lấy chi tiết danh mục
        $category = $this->categoryModel->getCategoryById($id, $_SESSION['user_id']);
        
        if(!$category) {
            // Nếu không tìm thấy danh mục
            $_SESSION['error'] = 'Không tìm thấy danh mục này!';
            header('Location: ' . BASEURL . '/expense-categories');
            exit;
        }
        
        // Tạo dữ liệu sao chép
        $data = [
            'name' => $category->name . ' (Bản sao)',
            'description' => $category->description,
            'user_id' => $_SESSION['user_id'],
            'type' => 'expense',
            'icon' => $category->icon,
            'color' => $category->color
        ];
        
        // Kiểm tra xem tên mới có bị trùng không
        if($this->categoryModel->categoryNameExists($data['name'], $data['user_id'], $data['type'])) {
            $data['name'] = $category->name . ' (Bản sao ' . date('Y-m-d H:i:s') . ')';
        }
        
        // Thêm danh mục mới
        $result = $this->categoryModel->addCategory($data);
        
        if($result) {
            $_SESSION['success'] = 'Sao chép danh mục thành công!';
        } else {
            $_SESSION['error'] = 'Đã xảy ra lỗi khi sao chép danh mục.';
        }
        
        // Chuyển hướng về trang danh sách
        header('Location: ' . BASEURL . '/expense-categories');
        exit;
    }
    
    /**
     * Lấy danh sách icon có sẵn
     * @return array Danh sách icon
     */
    private function getAvailableIcons() {
        // Đây là danh sách mẫu, bạn có thể thay thế bằng cách quét thư mục icon thực tế
        return [
            'default.png' => 'Mặc định',
            'food.png' => 'Ăn uống',
            'transport.png' => 'Di chuyển',
            'shopping.png' => 'Mua sắm',
            'bill.png' => 'Hóa đơn',
            'entertainment.png' => 'Giải trí',
            'education.png' => 'Giáo dục',
            'health.png' => 'Sức khỏe',
            'travel.png' => 'Du lịch',
            'gift.png' => 'Quà tặng',
            'family.png' => 'Gia đình',
            'housing.png' => 'Nhà ở',
            'personal.png' => 'Cá nhân',
            'pets.png' => 'Thú cưng',
            'business.png' => 'Kinh doanh',
            'charity.png' => 'Từ thiện',
            'investment.png' => 'Đầu tư',
            'other.png' => 'Khác'
        ];
    }
    
    /**
     * Nhập danh mục mẫu cho người dùng
     */
    public function importSamples() {
        if($_SERVER['REQUEST_METHOD'] !== 'POST') {
            header('Location: ' . BASEURL . '/expense-categories');
            exit;
        }
        
        $result = $this->categoryModel->insertSampleCategories($_SESSION['user_id']);
        
        if($result) {
            $_SESSION['success'] = 'Đã nhập danh mục mẫu thành công!';
        } else {
            $_SESSION['error'] = 'Bạn đã có danh mục hoặc đã xảy ra lỗi khi nhập danh mục mẫu.';
        }
        
        header('Location: ' . BASEURL . '/expense-categories');
        exit;
    }
}