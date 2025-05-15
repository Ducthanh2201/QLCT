<?php
// Đảm bảo các helper files được load
require_once APPROOT . '/helpers/session_helper.php';
require_once APPROOT . '/helpers/helpers.php';

class ExpensesController extends BaseController {
    private $expenseModel;
    private $categoryModel;
    private $paymentMethodModel;
    protected $db;
    
    public function __construct() {
        $this->expenseModel = $this->model('ExpenseModel');
        $this->categoryModel = $this->model('CategoryModel');
        $this->paymentMethodModel = $this->model('PaymentMethodModel');
        $this->db = new Database();
    }
    
    /**
     * Tạo file DummyPaymentMethodModel.php
     * @param string $filePath Đường dẫn đến file
     */
    private function createDummyPaymentMethodModel($filePath) {
        $content = "<?php\nclass DummyPaymentMethodModel {\n";
        $content .= "    public function getBankAccounts(\$userId) {\n";
        $content .= "        return [];\n    }\n\n";
        $content .= "    public function getPaymentCards(\$userId) {\n";
        $content .= "        return [];\n    }\n\n";
        $content .= "    public function getAllPaymentMethods() {\n";
        $content .= "        return [];\n    }\n\n";
        $content .= "    public function addBankAccount(\$data) {\n";
        $content .= "        return false;\n    }\n\n";
        $content .= "    public function addPaymentCard(\$data) {\n";
        $content .= "        return false;\n    }\n";
        $content .= "}\n";
        
        file_put_contents($filePath, $content);
    }
    
    /**
     * Tạo file ExpenseModel.php
     * @param string $filePath Đường dẫn đến file
     */
    private function createExpenseModel($filePath) {
        $content = "<?php\nclass ExpenseModel {\n";
        $content .= "    private \$db;\n\n";
        $content .= "    public function __construct() {\n";
        $content .= "        \$this->db = new Database();\n    }\n\n";
        $content .= "    public function getExpenses(\$userId, \$filters = []) {\n";
        $content .= "        return [];\n    }\n\n";
        $content .= "    public function countExpenses(\$userId, \$filters = []) {\n";
        $content .= "        return 0;\n    }\n\n";
        $content .= "    public function getTotalExpense(\$userId, \$startDate, \$endDate) {\n";
        $content .= "        return 0;\n    }\n\n";
        $content .= "    public function getUserBudget(\$userId, \$month, \$year) {\n";
        $content .= "        return null;\n    }\n\n";
        $content .= "    public function getAllStatuses() {\n";
        $content .= "        return [];\n    }\n\n";
        $content .= "    public function getExpenseByCategory(\$userId, \$startDate, \$endDate) {\n";
        $content .= "        return [];\n    }\n\n";
        $content .= "    public function getDailyExpenses(\$userId, \$startDate, \$endDate) {\n";
        $content .= "        return [];\n    }\n\n";
        $content .= "    public function getAllPaymentMethods() {\n";
        $content .= "        return [];\n    }\n\n";
        $content .= "    public function addExpense(\$data) {\n";
        $content .= "        return false;\n    }\n\n";
        $content .= "    public function addPayment(\$expenseId, \$data) {\n";
        $content .= "        return false;\n    }\n\n";
        $content .= "    public function getExpenseById(\$id, \$userId) {\n";
        $content .= "        return null;\n    }\n\n";
        $content .= "    public function getPaymentByExpenseId(\$expenseId) {\n";
        $content .= "        return null;\n    }\n\n";
        $content .= "    public function updateExpense(\$data) {\n";
        $content .= "        return false;\n    }\n\n";
        $content .= "    public function deleteExpense(\$id, \$userId) {\n";
        $content .= "        return false;\n    }\n";
        $content .= "}\n";
        
        file_put_contents($filePath, $content);
    }
    
    /**
     * Tạo file CategoryModel.php
     * @param string $filePath Đường dẫn đến file
     */
    private function createCategoryModel($filePath) {
        $content = "<?php\nclass CategoryModel {\n";
        $content .= "    private \$db;\n\n";
        $content .= "    public function __construct() {\n";
        $content .= "        \$this->db = new Database();\n    }\n\n";
        $content .= "    public function getCategoriesByType(\$userId, \$type) {\n";
        $content .= "        return [];\n    }\n\n";
        $content .= "}\n";
        
        file_put_contents($filePath, $content);
    }
    
    /**
     * Trang danh sách chi tiêu
     */
    public function index() {
        // Xử lý các tham số lọc và phân trang
        $page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
        $limit = isset($_GET['limit']) ? (int)$_GET['limit'] : 10;
        $offset = ($page - 1) * $limit;
        
        // Lấy thời gian hiện tại và đầu tháng
        $currentMonth = date('m');
        $currentYear = date('Y');
        $firstDayOfMonth = date('Y-m-01');
        $lastDayOfMonth = date('Y-m-t');
        
        $filters = [
            'start_date' => $_GET['start_date'] ?? $firstDayOfMonth,
            'end_date' => $_GET['end_date'] ?? $lastDayOfMonth,
            'category_id' => $_GET['category'] ?? '',
            'status_id' => $_GET['status'] ?? '',
            'search' => $_GET['search'] ?? '',
            'limit' => $limit,
            'offset' => $offset
        ];
        
        // Lấy danh sách chi tiêu
        $expenses = $this->expenseModel->getExpenses($_SESSION['user_id'], $filters);
        
        // Lấy tổng số chi tiêu để phân trang
        $totalExpenses = $this->expenseModel->countExpenses($_SESSION['user_id'], $filters);
        $totalPages = ceil($totalExpenses / $limit);
        
        // Lấy danh sách danh mục chi tiêu
        $categories = $this->categoryModel->getCategoriesByType($_SESSION['user_id'], 'expense');
        
        // Lấy danh sách trạng thái chi tiêu
        $statuses = $this->expenseModel->getAllStatuses();
        
        // Tính tổng chi tiêu trong khoảng thời gian hiện tại
        $totalAmount = $this->expenseModel->getTotalExpense(
            $_SESSION['user_id'], 
            $filters['start_date'], 
            $filters['end_date']
        );
        
        // Tính chi tiêu tháng trước để so sánh
        $lastMonth = date('Y-m', strtotime('-1 month'));
        $lastMonthStart = date('Y-m-01', strtotime('-1 month'));
        $lastMonthEnd = date('Y-m-t', strtotime('-1 month'));
        $lastMonthTotal = $this->expenseModel->getTotalExpense(
            $_SESSION['user_id'], 
            $lastMonthStart, 
            $lastMonthEnd
        );
        
        // Tính phần trăm thay đổi
        $monthCompare = 0;
        if ($lastMonthTotal > 0) {
            $monthCompare = round(($totalAmount - $lastMonthTotal) / $lastMonthTotal * 100);
        }
        
        // Lấy chi tiêu theo danh mục
        $expensesByCategory = $this->expenseModel->getExpenseByCategory(
            $_SESSION['user_id'], 
            $filters['start_date'], 
            $filters['end_date']
        );
        
        // Chuẩn bị dữ liệu cho biểu đồ
        $chartCategories = [];
        $chartValues = [];
        $chartColors = [];
        
        foreach ($expensesByCategory as $cat) {
            $chartCategories[] = $cat->name;
            $chartValues[] = $cat->total;
            $chartColors[] = $cat->color ?? $this->getRandomColor();
        }
        
        // Chuẩn bị dữ liệu cho biểu đồ theo thời gian
        $timeLabels = [];
        $timeValues = [];
        
        // Lấy chi tiêu trong 7 ngày gần đây
        $startDate = date('Y-m-d', strtotime('-6 days'));
        $endDate = date('Y-m-d');
        $dailyExpenses = $this->expenseModel->getDailyExpenses($_SESSION['user_id'], $startDate, $endDate);
        
        // Tạo mảng với tất cả các ngày
        for ($i = 0; $i <= 6; $i++) {
            $date = date('Y-m-d', strtotime("-$i days"));
            $displayDate = date('d/m', strtotime($date));
            $timeLabels[6-$i] = $displayDate;
            $timeValues[6-$i] = 0; // Giá trị mặc định là 0
            
            // Tìm chi tiêu cho ngày này
            foreach ($dailyExpenses as $daily) {
                if ($daily->expense_date == $date) {
                    $timeValues[6-$i] = $daily->total;
                    break;
                }
            }
        }
        
        // Tính toán thêm một số thống kê
        $today = date('Y-m-d');
        $todayExpense = $this->expenseModel->getTotalExpense($_SESSION['user_id'], $today, $today);
        
        // Dự báo chi tiêu tháng này dựa trên xu hướng hiện tại
        $dayOfMonth = (int)date('d');
        $daysInMonth = (int)date('t');
        $forecastExpense = 0;
        
        if ($dayOfMonth > 0) {
            $forecastExpense = round($totalAmount / $dayOfMonth * $daysInMonth);
        }
        
        $data = [
            'title' => 'Danh sách chi tiêu',
            'expenses' => $expenses,
            'categories' => $categories,
            'statuses' => $statuses,
            'filters' => $filters,
            'pagination' => [
                'currentPage' => $page,
                'totalPages' => $totalPages,
                'limit' => $limit,
                'total' => $totalExpenses,
                'from' => $offset + 1,
                'to' => min($offset + $limit, $totalExpenses)
            ],
            'charts' => [
                'by_category' => [
                    'labels' => $chartCategories,
                    'values' => $chartValues,
                    'colors' => $chartColors
                ],
                'by_time' => [
                    'labels' => $timeLabels,
                    'values' => $timeValues
                ]
            ],
            'stats' => [
                'total_this_month' => $totalAmount,
                'month_compare' => $monthCompare,
                'today' => $todayExpense,
                'forecast' => $forecastExpense
            ]
        ];
        
        $this->view('expenses/index', $data);
    }
    
    /**
     * Tạo màu ngẫu nhiên cho biểu đồ
     * @return string Mã màu HEX
     */
    private function getRandomColor() {
        $colors = [
            '#4CAF50', '#F44336', '#2196F3', '#FF9800', '#9C27B0', 
            '#3F51B5', '#00BCD4', '#FFEB3B', '#795548', '#607D8B',
            '#E91E63', '#009688', '#673AB7', '#FFC107', '#03A9F4'
        ];
        
        return $colors[array_rand($colors)];
    }
    
    /**
     * Thêm phương thức để lấy chi tiêu hàng ngày cho biểu đồ
     */
    public function getDailyExpenses($userId, $startDate, $endDate) {
        // Sửa: Không truy cập trực tiếp vào $this->db mà sử dụng model thay thế
        return $this->expenseModel->getDailyExpenses($userId, $startDate, $endDate);
    }
    
    /**
     * Trang thêm chi tiêu mới
     */
    public function create() {
        try {
            // Nếu là POST request, xử lý thêm chi tiêu mới
            if($_SERVER['REQUEST_METHOD'] === 'POST') {
                // Lấy dữ liệu từ form
                $data = [
                    'user_id' => $_SESSION['user_id'],
                    'category_id' => $_POST['category_id'] ?? '',
                    'amount' => $_POST['amount'] ?? '',
                    'expense_date' => $_POST['expense_date'] ?? date('Y-m-d'),
                    'description' => $_POST['description'] ?? '',
                    'receipt_url' => null,
                    'status_id' => $_POST['status'] == 'paid' ? 1 : 2,
                    // Lưu method_id trực tiếp thay vì lưu tên method
                    'method_id' => $this->getMethodIdByName($_POST['payment_method'] ?? 'cash'),
                    'payment_method' => $_POST['payment_method'] ?? 'cash',
                    'payment_date' => $_POST['expense_date'] ?? date('Y-m-d'),
                    'bank_account_id' => $_POST['bank_account'] ?? null,
                    'payment_card_id' => $_POST['card_number'] ?? null,
                    'errors' => []
                ];
                
                // Xử lý upload file nếu có
                if(isset($_FILES['receipt_image']) && $_FILES['receipt_image']['error'] === UPLOAD_ERR_OK) {
                    $uploadDir = 'uploads/receipts/';
                    
                    // Đảm bảo thư mục tồn tại
                    if(!file_exists($uploadDir)) {
                        mkdir($uploadDir, 0755, true);
                    }
                    
                    $fileName = time() . '_' . basename($_FILES['receipt_image']['name']);
                    $uploadFile = $uploadDir . $fileName;
                    
                    if(move_uploaded_file($_FILES['receipt_image']['tmp_name'], $uploadFile)) {
                        $data['receipt_url'] = $uploadFile;
                    }
                }
                
                // Xác thực dữ liệu
                if(empty($data['category_id'])) {
                    $data['errors']['category_id'] = 'Vui lòng chọn danh mục chi tiêu';
                }
                
                if(empty($data['amount']) || !is_numeric($data['amount']) || $data['amount'] <= 0) {
                    $data['errors']['amount'] = 'Vui lòng nhập số tiền hợp lệ';
                }
                
                if(empty($data['description'])) {
                    $data['errors']['description'] = 'Vui lòng nhập mô tả chi tiêu';
                }
                
                // Nếu không có lỗi, thêm chi tiêu mới
                if(empty($data['errors'])) {
                    $expense_id = $this->expenseModel->addExpense($data);
                    
                    if($expense_id) {
                        // Thêm thông tin thanh toán
                        $paymentData = [
                            'method_id' => $data['method_id'], // Sử dụng method_id đã lấy ở trên
                            'payment_date' => $data['payment_date'],
                            'amount_paid' => $data['amount'],
                            'bank_account_id' => $data['bank_account_id'],
                            'payment_card_id' => $data['payment_card_id']
                        ];
                        
                        $this->expenseModel->addPayment($expense_id, $paymentData);
                        
                        $_SESSION['success'] = 'Thêm chi tiêu thành công!';
                        header('Location: ' . BASEURL . '/expenses');
                        exit;
                    } else {
                        $data['errors']['general'] = 'Có lỗi xảy ra khi thêm chi tiêu. Vui lòng thử lại!';
                    }
                }
            }
            
            // Tính toán thông tin ngân sách
            $currentMonth = date('m');
            $currentYear = date('Y');
            $firstDayOfMonth = date('Y-m-01');
            $lastDayOfMonth = date('Y-m-t');
            
            // Lấy tổng chi tiêu trong tháng
            $totalExpense = $this->expenseModel->getTotalExpense(
                $_SESSION['user_id'], 
                $firstDayOfMonth,
                $lastDayOfMonth
            );
            
            // Lấy ngân sách của người dùng cho tháng hiện tại
            $budget = $this->expenseModel->getUserBudget($_SESSION['user_id'], $currentMonth, $currentYear);
            $budgetAmount = $budget ? $budget->amount_limit : 0;
            
            // Tính toán các thông số ngân sách
            $budgetRemaining = max(0, $budgetAmount - $totalExpense);
            $budgetPercentage = $budgetAmount > 0 ? round(($totalExpense / $budgetAmount) * 100) : 0;
            
            // Hiển thị form thêm chi tiêu
            $categories = $this->categoryModel->getCategoriesByType($_SESSION['user_id'], 'expense');
            $statuses = $this->expenseModel->getAllStatuses();
            $paymentMethods = $this->expenseModel->getAllPaymentMethods();
            
            // Lấy danh sách tài khoản ngân hàng và thẻ thanh toán
            $bankAccounts = [];
            $cards = [];
            
            // Đảm bảo PaymentMethodModel đã được khởi tạo
            if(class_exists('PaymentMethodModel')) {
                try {
                    // Kiểm tra kỹ bảng trong cơ sở dữ liệu
                    try {
                        $this->db = new Database();
                        $this->db->query("SHOW TABLES LIKE 'bank_accounts'");
                        $bankTableExists = (count($this->db->resultSet()) > 0);
                        
                        $this->db->query("SHOW TABLES LIKE 'payment_cards'");
                        $cardTableExists = (count($this->db->resultSet()) > 0);
                        
                        if($bankTableExists) {
                            $bankAccounts = $this->paymentMethodModel->getBankAccounts($_SESSION['user_id']);
                        }
                        
                        if($cardTableExists) {
                            $cards = $this->paymentMethodModel->getPaymentCards($_SESSION['user_id']);
                        }
                    } catch(Exception $e) {
                        error_log('Error checking database tables: ' . $e->getMessage());
                    }
                } catch(Exception $e) {
                    error_log('Error in PaymentMethodModel: ' . $e->getMessage());
                }
            }
            
            // Thêm dữ liệu mẫu nếu cần (không sử dụng ENVIRONMENT vì có thể chưa được định nghĩa)
            if(empty($bankAccounts)) {
                $bankAccounts = [];
            }
            
            if(empty($cards)) {
                $cards = [];
            }
            
            $data = [
                'title' => 'Thêm chi tiêu mới',
                'categories' => $categories,
                'statuses' => $statuses,
                'payment_methods' => $paymentMethods,
                'bank_accounts' => $bankAccounts,
                'cards' => $cards,
                'stats' => [
                    'budget_total' => $budgetAmount,
                    'budget_used' => $totalExpense,
                    'budget_remaining' => $budgetRemaining,
                    'budget_percentage' => $budgetPercentage,
                    'this_month' => $totalExpense, 
                    'budget_progress' => $budgetPercentage,
                    'budget_progress_class' => $budgetPercentage > 80 ? 'danger' : 'primary'
                ]
            ];
            
            $this->view('expenses/create', $data);
        } catch (Exception $e) {
            error_log('Error in ExpensesController::create: ' . $e->getMessage());
            
            $_SESSION['error'] = 'Đã xảy ra lỗi. Vui lòng thử lại sau.';
            header('Location: ' . BASEURL . '/expenses');
            exit;
        }
    }
    
    /**
     * Lấy ID của phương thức thanh toán theo tên
     * @param string $methodName Tên phương thức thanh toán
     * @return int ID của phương thức thanh toán
     */
    private function getMethodIdByName($methodName) {
    // Nếu đã là số, trả về luôn
    if(is_numeric($methodName)) {
        return (int)$methodName;
    }
    
    // Ánh xạ tên phương thức với ID
    switch ($methodName) {
        case 'cash':
            return 1; // ID cho phương thức tiền mặt
        case 'bank':
            return 2; // ID cho phương thức chuyển khoản
        case 'card':
            return 3; // ID cho phương thức thẻ tín dụng/ghi nợ
        default:
            return 4; // ID cho phương thức khác
    }
}
    /**
     * Lấy thống kê cho một khoản chi tiêu
     * @param object $expense Thông tin chi tiêu
     * @return array Dữ liệu thống kê
     */
    private function getExpenseStats($expense) {
        if (!$expense) {
            return [
                'monthly_avg' => 0,
                'category_avg' => 0
            ];
        }
        
        // Lấy tháng và năm của khoản chi tiêu
        $month = date('m', strtotime($expense->expense_date));
        $year = date('Y', strtotime($expense->expense_date));
        
        // Lấy tổng chi tiêu trong tháng
        $firstDayOfMonth = date('Y-m-01', strtotime($expense->expense_date));
        $lastDayOfMonth = date('Y-m-t', strtotime($expense->expense_date));
        
        $totalExpense = $this->expenseModel->getTotalExpense(
            $_SESSION['user_id'], 
            $firstDayOfMonth,
            $lastDayOfMonth
        );
        
        // Đếm số khoản chi tiêu trong tháng
        $filters = [
            'start_date' => $firstDayOfMonth,
            'end_date' => $lastDayOfMonth
        ];
        $countExpenses = $this->expenseModel->countExpenses($_SESSION['user_id'], $filters);
        
        // Tính chi tiêu trung bình hàng tháng
        $monthlyAvg = $countExpenses > 0 ? $totalExpense / $countExpenses : 0;
        
        // Tính chi tiêu trung bình theo danh mục
        $categoryId = $expense->category_id;
        $filters['category_id'] = $categoryId;
        
        $categoryExpenses = $this->expenseModel->getExpenses($_SESSION['user_id'], $filters);
        $categoryTotal = 0;
        $categoryCount = is_array($categoryExpenses) ? count($categoryExpenses) : 0;
        
        if ($categoryCount > 0) {
            foreach ($categoryExpenses as $catExp) {
                $categoryTotal += $catExp->amount;
            }
        }
        
        $categoryAvg = $categoryCount > 0 ? $categoryTotal / $categoryCount : 0;
        
        return [
            'monthly_avg' => $monthlyAvg,
            'category_avg' => $categoryAvg,
            'budget_total' => 0, // Placeholder
            'budget_used' => $totalExpense,
            'budget_remaining' => 0, // Placeholder
            'budget_percentage' => 0 // Placeholder
        ];
    }
    /**
     * Trang chi tiết chi tiêu
     * @param int $id ID của khoản chi tiêu
     */
    public function viewExpense($id = null) {
    // Debug để kiểm tra luồng thực thi
    error_log("[DEBUG] viewExpense được gọi với ID = " . ($id ?? 'null'));
    
    // Kiểm tra có ID không
    if ($id === null) {
        error_log("[DEBUG] ID là null, chuyển hướng về trang danh sách");
        $_SESSION['error'] = 'Không tìm thấy ID chi tiêu!';
        $this->redirect('expenses');
        return;
    }
    
    try {
        // Lấy chi tiết khoản chi tiêu
        $expense = $this->expenseModel->getExpenseById($id, $_SESSION['user_id']);
        
        error_log("[DEBUG] Query executed, expense found: " . ($expense ? "yes" : "no"));
        
        if(!$expense) {
            $_SESSION['error'] = 'Không tìm thấy khoản chi tiêu này!';
            $this->redirect('expenses');
            return;
        }
        
        // Lấy thông tin thanh toán
        $payment = $this->expenseModel->getPaymentByExpenseId($id);
        
        // Lấy thống kê chi tiêu
        $stats = $this->getExpenseStats($expense);
        
        // Chuẩn bị dữ liệu để truyền vào view
        $data = [
            'title' => 'Chi tiết chi tiêu',
            'expense' => $expense,
            'payment' => $payment,
            'stats' => $stats
        ];
        
        // Gọi view một cách rõ ràng
        $this->view('expenses/expense_detail', $data);
        
    } catch (Exception $e) {
        error_log('Error in viewExpense: ' . $e->getMessage());
        $_SESSION['error'] = 'Đã xảy ra lỗi khi hiển thị chi tiết chi tiêu: ' . $e->getMessage();
        $this->redirect('expenses');
    }
}

      /**
     * Trang sửa chi tiêu
     * @param int $id ID của khoản chi tiêu
     */
    public function edit($id = null) {
    error_log("[ExpensesController] edit() called with ID: " . ($id ?? 'null'));
    
    // Kiểm tra có ID không
    if ($id === null) {
        $_SESSION['error'] = 'Không tìm thấy ID chi tiêu!';
        $this->redirect('expenses');
        return;
    }
    
    try {
        // Lấy chi tiết khoản chi tiêu
        $expense = $this->expenseModel->getExpenseById($id, $_SESSION['user_id']);
        
        error_log("[ExpensesController] Expense found: " . ($expense ? 'yes' : 'no'));
        
        if(!$expense) {
            $_SESSION['error'] = 'Không tìm thấy khoản chi tiêu này!';
            $this->redirect('expenses');
            return;
        }
        
        // Debug thông tin chi tiêu
        error_log("[ExpensesController] Expense: " . json_encode($expense));
        
        // Lấy thông tin thanh toán
        $payment = $this->expenseModel->getPaymentByExpenseId($id);
        
        // Lấy danh sách danh mục chi tiêu
        $categories = $this->categoryModel->getCategoriesByType($_SESSION['user_id'], 'expense');
        
        // Nếu không có danh mục, thử lấy theo cách khác hoặc in thông báo lỗi
        if (empty($categories)) {
            error_log("[ExpensesController] No categories found via categoryModel->getCategoriesByType!");
            
            // Thử lấy từ cả expense_categories và categories
            $db = new Database();
            
            // Thử lấy từ expense_categories
            $db->query("SELECT category_id, category_id as id, name, type FROM expense_categories 
                       WHERE (user_id = :user_id OR user_id IS NULL) AND type = 'expense'");
            $db->bind(':user_id', $_SESSION['user_id']);
            $categories = $db->resultSet();
            
            // Nếu vẫn không có, thử lấy từ categories
            if (empty($categories)) {
                $db->query("SELECT category_id, category_id as id, name, type FROM categories 
                           WHERE (user_id = :user_id OR user_id IS NULL) AND type = 'expense'");
                $db->bind(':user_id', $_SESSION['user_id']);
                $categories = $db->resultSet();
            }
            
            error_log("[ExpensesController] Found " . count($categories) . " categories via direct DB query");
        }
        
        // Biến này để debug
        $firstCategory = !empty($categories) ? json_encode($categories[0]) : 'None';
        error_log("[ExpensesController] First category: " . $firstCategory);
        
        // Đảm bảo danh mục đầy đủ
        $formattedCategories = [];
        foreach ($categories as $category) {
            $formattedCategory = new stdClass();
            $formattedCategory->id = $category->category_id ?? $category->id ?? 0;
            $formattedCategory->name = $category->name ?? $category->category_name ?? 'Không xác định';
            $formattedCategories[] = $formattedCategory;
            
            // Debug từng danh mục
            error_log("[ExpensesController] Formatted category: " . json_encode($formattedCategory));
        }
        
        // Lấy trạng thái từ bảng expense_status
        $this->db->query("SELECT status_id, status_name FROM expense_status");
        $statuses = $this->db->resultSet();
        
        // Format statuses
        $formattedStatuses = [];
        foreach ($statuses as $status) {
            $formattedStatus = new stdClass();
            $formattedStatus->id = $status->status_id;
            $formattedStatus->name = $status->status_name;
            $formattedStatuses[] = $formattedStatus;
        }
        
        // Chuẩn bị dữ liệu để truyền vào view
        $data = [
            'title' => 'Sửa chi tiêu',
            'expense' => $expense,
            'payment' => $payment,
            'categories' => $formattedCategories,
            'statuses' => $formattedStatuses
        ];
        
        // Debug danh sách cuối cùng
        error_log("[ExpensesController] Final categories count: " . count($formattedCategories));
        
        $this->view('expenses/edit', $data);
        
    } catch (Exception $e) {
        error_log("[ExpensesController] Error in edit: " . $e->getMessage());
        $_SESSION['error'] = 'Đã xảy ra lỗi khi hiển thị form chỉnh sửa: ' . $e->getMessage();
        $this->redirect('expenses');
    }
}
    public function update($id = null) {
    // Kiểm tra xem có phải POST request không
    if($_SERVER['REQUEST_METHOD'] !== 'POST') {
        $_SESSION['error'] = 'Phương thức không hợp lệ!';
        header('Location: ' . BASEURL . '/expenses');
        exit;
    }
    
    // Kiểm tra ID hợp lệ
    if($id === null) {
        $id = $_POST['expense_id'] ?? null;
    }
    
    if(!$id) {
        $_SESSION['error'] = 'ID không hợp lệ!';
        header('Location: ' . BASEURL . '/expenses');
        exit;
    }
    
    // Lấy chi tiết khoản chi tiêu hiện tại
    $expense = $this->expenseModel->getExpenseById($id, $_SESSION['user_id']);
    
    if(!$expense) {
        $_SESSION['error'] = 'Không tìm thấy khoản chi tiêu này!';
        header('Location: ' . BASEURL . '/expenses');
        exit;
    }
    
    try {
        // Debug thông tin POST
        error_log('POST data for update: ' . json_encode($_POST));
        
        // Lấy dữ liệu từ form
        $data = [
            'expense_id' => $id,
            'user_id' => $_SESSION['user_id'],
            'category_id' => $_POST['category_id'] ?? $expense->category_id,
            'amount' => $_POST['amount'] ?? $expense->amount,
            'expense_date' => $_POST['expense_date'] ?? $expense->expense_date,
            'description' => $_POST['description'] ?? $expense->description,
            'receipt_url' => $expense->receipt_url, // Giữ nguyên URL hình ảnh trừ khi có upload mới
            'status_id' => $_POST['status_id'] ?? $expense->status_id, // Lấy đúng status_id từ form
            'payment_method' => $this->getMethodIdByName($_POST['payment_method'] ?? 'cash'),
            'payment_date' => $_POST['expense_date'] ?? date('Y-m-d'),
            'bank_account_id' => $_POST['bank_account'] ?? null,
            'payment_card_id' => $_POST['card_number'] ?? null
        ];
        
        // Debug dữ liệu cập nhật
        error_log('Update data prepared: ' . json_encode($data));
        
        // Xử lý upload file nếu có
        if(isset($_FILES['receipt_image']) && $_FILES['receipt_image']['error'] === UPLOAD_ERR_OK) {
            $uploadDir = 'uploads/receipts/';
            
            // Đảm bảo thư mục tồn tại
            if(!file_exists($uploadDir)) {
                mkdir($uploadDir, 0755, true);
            }
            
            $fileName = time() . '_' . basename($_FILES['receipt_image']['name']);
            $uploadFile = $uploadDir . $fileName;
            
            if(move_uploaded_file($_FILES['receipt_image']['tmp_name'], $uploadFile)) {
                // Nếu upload thành công, xóa file cũ (nếu có)
                if($expense->receipt_url && file_exists($expense->receipt_url)) {
                    @unlink($expense->receipt_url);
                }
                $data['receipt_url'] = $uploadFile;
            }
        }
        
        // Cập nhật chi tiêu
        $result = $this->expenseModel->updateExpense($data);
        
        if($result) {
            $_SESSION['success'] = 'Cập nhật chi tiêu thành công!';
        } else {
            $_SESSION['error'] = 'Có lỗi xảy ra khi cập nhật chi tiêu!';
        }
    } catch (Exception $e) {
        error_log('Error: ' . $e->getMessage());
        $_SESSION['error'] = 'Đã xảy ra lỗi: ' . $e->getMessage();
    }
    
    // Chuyển hướng về trang danh sách chi tiêu
    header('Location: ' . BASEURL . '/expenses');
    exit;
}
    /**
     * Xóa khoản chi tiêu
     */
    public function delete() {
        error_log("[DEBUG] Phương thức delete được gọi");
        
        // Kiểm tra xem có phải POST request không
        if($_SERVER['REQUEST_METHOD'] !== 'POST') {
            $_SESSION['error'] = 'Phương thức không hợp lệ!';
            header('Location: ' . BASEURL . '/expenses');
            exit;
        }
        
        try {
            // Lấy ID từ form POST
            $id = isset($_POST['expense_id']) ? $_POST['expense_id'] : null;
            
            error_log("[DEBUG] expense_id: " . ($id ?? 'null'));
            
            if(!$id) {
                $_SESSION['error'] = 'ID khoản chi tiêu không hợp lệ!';
                header('Location: ' . BASEURL . '/expenses');
                exit;
            }
            
            // Lấy chi tiết khoản chi tiêu để kiểm tra quyền
            $expense = $this->expenseModel->getExpenseById($id, $_SESSION['user_id']);
            
            if(!$expense) {
                // Nếu không tìm thấy khoản chi tiêu
                $_SESSION['error'] = 'Không tìm thấy khoản chi tiêu này hoặc bạn không có quyền xóa!';
                header('Location: ' . BASEURL . '/expenses');
                exit;
            }
            
            // Xóa khoản chi tiêu (phương thức này đã tích hợp việc xóa thông tin thanh toán)
            $result = $this->expenseModel->deleteExpense($id, $_SESSION['user_id']);
            
            if($result) {
                // Xóa hình ảnh hóa đơn nếu có
                if(isset($expense->receipt_url) && $expense->receipt_url && file_exists($expense->receipt_url)) {
                    error_log("[DEBUG] Đang xóa file hình ảnh: " . $expense->receipt_url);
                    @unlink($expense->receipt_url);
                }
                
                // Thêm thông báo thành công vào session
                $_SESSION['success'] = 'Xóa chi tiêu thành công!';
                error_log("[DEBUG] Xóa chi tiêu thành công: ID = " . $id);
            } else {
                $_SESSION['error'] = 'Đã xảy ra lỗi khi xóa chi tiêu. Vui lòng thử lại.';
                error_log("[ERROR] Không thể xóa chi tiêu: ID = " . $id);
            }
        } catch (Exception $e) {
            $_SESSION['error'] = 'Đã xảy ra lỗi hệ thống: ' . $e->getMessage();
            error_log("[ERROR] Exception trong delete: " . $e->getMessage());
        }
        
        // Chuyển hướng đến trang danh sách chi tiêu
        header('Location: ' . BASEURL . '/expenses');
        exit;
    }
    
    /**
     * API lấy danh sách chi tiêu theo định dạng JSON
     */
    public function api() {
        // Kiểm tra xem có phải AJAX request không
        if(!isset($_SERVER['HTTP_X_REQUESTED_WITH']) || 
           strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) !== 'xmlhttprequest') {
            header('HTTP/1.0 403 Forbidden');
            echo json_encode(['error' => 'Forbidden']);
            exit;
        }
        
        // Xử lý các tham số lọc
        $filters = [
            'start_date' => $_GET['start_date'] ?? date('Y-m-01'),
            'end_date' => $_GET['end_date'] ?? date('Y-m-t'),
            'category_id' => $_GET['category_id'] ?? '',
            'status_id' => $_GET['status_id'] ?? '',
            'search' => $_GET['search'] ?? ''
        ];
        
        // Lấy danh sách chi tiêu
        $expenses = $this->expenseModel->getExpenses($_SESSION['user_id'], $filters);
        
        // Lấy tổng chi tiêu
        $totalAmount = $this->expenseModel->getTotalExpense(
            $_SESSION['user_id'], 
            $filters['start_date'], 
            $filters['end_date']
        );
        
        // Lấy chi tiêu theo danh mục
        $expensesByCategory = $this->expenseModel->getExpenseByCategory(
            $_SESSION['user_id'], 
            $filters['start_date'], 
            $filters['end_date']
        );
        
        // Trả về dữ liệu dưới dạng JSON
        header('Content-Type: application/json');
        echo json_encode([
            'expenses' => $expenses,
            'total_amount' => $totalAmount,
            'expenses_by_category' => $expensesByCategory,
            'currency' => $_SESSION['currency'] ?? 'VND'
        ]);
        exit;
    }    // Ghi chú: Phương thức viewExpenseDetail đã được loại bỏ và thay thế bằng viewExpense
}

