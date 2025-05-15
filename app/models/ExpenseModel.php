<?php
class ExpenseModel {
    private $db;
    
    public function __construct() {
        $this->db = new Database();
        
        // Khởi tạo các bảng và dữ liệu cần thiết
        $this->initPaymentMethods();
        $this->initExpenseStatus();
    }
    
    /**
     * Lấy tất cả chi tiêu của người dùng
     * @param int $userId ID của người dùng
     * @param array $filters Bộ lọc (khoảng thời gian, danh mục)
     * @param string $orderBy Sắp xếp theo trường nào
     * @param string $direction Chiều sắp xếp (ASC/DESC)
     * @return array Danh sách chi tiêu
     */
    public function getExpenses($userId, $filters = []) {
        // Tạo câu truy vấn cơ bản
        $sql = "
            SELECT e.*, c.name as category_name, c.color as category_color, s.status_name,
                   pm.method_id, pm.method_name as payment_method, ep.payment_date
            FROM expenses e
            LEFT JOIN categories c ON e.category_id = c.category_id
            LEFT JOIN expense_status s ON e.status_id = s.status_id
            LEFT JOIN expense_payments ep ON e.expense_id = ep.expense_id
            LEFT JOIN payment_methods pm ON ep.method_id = pm.method_id
            WHERE e.user_id = :user_id
        ";
        
        // Thêm các điều kiện lọc
        if(!empty($filters['start_date']) && !empty($filters['end_date'])) {
            $sql .= " AND e.expense_date BETWEEN :start_date AND :end_date";
        }
        
        if(!empty($filters['category_id'])) {
            $sql .= " AND e.category_id = :category_id";
        }
        
        if(!empty($filters['status_id'])) {
            $sql .= " AND e.status_id = :status_id";
        }
        
        if(!empty($filters['search'])) {
            $sql .= " AND (e.description LIKE :search OR c.name LIKE :search)";
        }
        
        // Thêm sắp xếp và giới hạn
        $sql .= " ORDER BY e.expense_date DESC, e.expense_id DESC";
        
        if(isset($filters['limit']) && isset($filters['offset'])) {
            $sql .= " LIMIT :limit OFFSET :offset";
        }
        
        // Thực hiện truy vấn
        $this->db->query($sql);
        
        // Bind các tham số
        $this->db->bind(':user_id', $userId);
        
        if(!empty($filters['start_date']) && !empty($filters['end_date'])) {
            $this->db->bind(':start_date', $filters['start_date']);
            $this->db->bind(':end_date', $filters['end_date']);
        }
        
        if(!empty($filters['category_id'])) {
            $this->db->bind(':category_id', $filters['category_id']);
        }
        
        if(!empty($filters['status_id'])) {
            $this->db->bind(':status_id', $filters['status_id']);
        }
        
        if(!empty($filters['search'])) {
            $this->db->bind(':search', '%' . $filters['search'] . '%');
        }
        
        if(isset($filters['limit']) && isset($filters['offset'])) {
            $this->db->bind(':limit', $filters['limit']);
            $this->db->bind(':offset', $filters['offset']);
        }
        
        return $this->db->resultSet();
    }
    
    /**
 * Lấy chi tiết khoản chi tiêu theo ID
 * @param int $id ID của khoản chi tiêu
 * @param int $userId ID của người dùng
 * @return object|false Thông tin chi tiêu hoặc false nếu không tìm thấy
 */
public function getExpenseById($id, $userId) {
    try {
        error_log("Attempting to fetch expense with ID: {$id} for user: {$userId}");
        
        // Kiểm tra xem bảng expense_status có tồn tại không
        $this->db->query("SHOW TABLES LIKE 'expense_status'");
        $statusTableExists = count($this->db->resultSet()) > 0;
        
        // Xây dựng truy vấn
        $query = "SELECT e.*, c.name as category_name, c.color as category_color";
        
        // Chỉ thêm status_name nếu bảng status tồn tại
        if ($statusTableExists) {
            $query .= ", s.status_name as status_name";
        } else {
            $query .= ", CASE 
                        WHEN e.status_id = 1 THEN 'Đã thanh toán' 
                        WHEN e.status_id = 2 THEN 'Chờ thanh toán'
                        ELSE 'Không xác định' END as status_name";
        }
        
        $query .= " FROM expenses e 
                  LEFT JOIN categories c ON e.category_id = c.category_id";
        
        // Chỉ join bảng status nếu nó tồn tại
        if ($statusTableExists) {
            $query .= " LEFT JOIN expense_status s ON e.status_id = s.status_id";
        }
        
        // Sửa lại điều kiện WHERE để sử dụng expense_id thay vì id
        $query .= " WHERE e.expense_id = :id AND e.user_id = :user_id";
        
        $this->db->query($query);
        $this->db->bind(':id', $id);
        $this->db->bind(':user_id', $userId);
        
        error_log("Executing query: {$query}");
        $result = $this->db->single();
        
        // Debug log
        error_log("getExpenseById: Result for expense ID {$id} and user {$userId}: " . ($result ? json_encode($result) : 'not found'));
        
        return $result;
    } catch (Exception $e) {
        error_log("Error in getExpenseById: " . $e->getMessage());
        error_log("Error trace: " . $e->getTraceAsString());
        return false;
    }
}
    
    /**
     * Thêm chi tiêu mới
     * @param array $data Dữ liệu chi tiêu
     * @return int|bool ID của chi tiêu mới hoặc false nếu có lỗi
     */
    public function addExpense($data) {
        $this->db->query("
            INSERT INTO expenses (user_id, category_id, amount, expense_date, description, receipt_url, status_id) 
            VALUES (:user_id, :category_id, :amount, :expense_date, :description, :receipt_url, :status_id)
        ");
        
        $this->db->bind(':user_id', $data['user_id']);
        $this->db->bind(':category_id', $data['category_id']);
        $this->db->bind(':amount', $data['amount']);
        $this->db->bind(':expense_date', $data['expense_date']);
        $this->db->bind(':description', $data['description']);
        $this->db->bind(':receipt_url', $data['receipt_url']);
        $this->db->bind(':status_id', $data['status_id']);
        
        if($this->db->execute()) {
            return $this->db->lastInsertId();
        } else {
            return false;
        }
    }
    
    /**
     * Lấy ID của phương thức thanh toán theo tên
     * @param string $methodName Tên phương thức thanh toán
     * @return int ID của phương thức thanh toán
     */
    public function getMethodIdByName($methodName) {
        // Ánh xạ tên phương thức với ID trong bảng payment_methods
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
     * Thêm thông tin thanh toán cho chi tiêu
     * @param int $expenseId ID của chi tiêu
     * @param array $data Dữ liệu thanh toán
     * @return bool Kết quả thêm thanh toán
     */
    public function addPayment($expenseId, $data) {
        try {
            // Đảm bảo method_id luôn có giá trị
            if (!isset($data['method_id']) || empty($data['method_id'])) {
                $data['method_id'] = 1; // Mặc định là tiền mặt
            }
            
            // Kiểm tra bảng expense_payments có tồn tại không
            $this->db->query("SHOW TABLES LIKE 'expense_payments'");
            $tableExists = (count($this->db->resultSet()) > 0);
            
            if(!$tableExists) {
                // Nếu bảng không tồn tại, tạo bảng expense_payments
                $this->createExpensePaymentTable();
            }
            
            // Kiểm tra cấu trúc bảng có các trường bank_account_id và payment_card_id không
            $this->db->query("DESCRIBE expense_payments");
            $columns = $this->db->resultSet();
            $columnNames = array_column($columns, 'Field');
            
            $hasBankAccountField = in_array('bank_account_id', $columnNames);
            $hasCardField = in_array('payment_card_id', $columnNames);
            
            if($hasBankAccountField && $hasCardField) {
                $this->db->query("
                    INSERT INTO expense_payments (expense_id, method_id, payment_date, amount_paid, bank_account_id, payment_card_id)
                    VALUES (:expense_id, :method_id, :payment_date, :amount_paid, :bank_account_id, :payment_card_id)
                ");
                
                $this->db->bind(':bank_account_id', $data['bank_account_id'] ?? null);
                $this->db->bind(':payment_card_id', $data['payment_card_id'] ?? null);
            } else {
                $this->db->query("
                    INSERT INTO expense_payments (expense_id, method_id, payment_date, amount_paid)
                    VALUES (:expense_id, :method_id, :payment_date, :amount_paid)
                ");
            }
            
            $this->db->bind(':expense_id', $expenseId);
            $this->db->bind(':method_id', $data['method_id']);
            $this->db->bind(':payment_date', $data['payment_date']);
            $this->db->bind(':amount_paid', $data['amount_paid']);
            
            return $this->db->execute();
        } catch (Exception $e) {
            error_log('Error in addPayment: ' . $e->getMessage());
            return false;
        }
    }
    
    /**
     * Tạo bảng expense_payment nếu chưa tồn tại
     */
    private function createExpensePaymentTable() {
        $sql = "CREATE TABLE IF NOT EXISTS expense_payments (
            payment_id INT AUTO_INCREMENT PRIMARY KEY,
            expense_id INT NOT NULL,
            method_id INT NOT NULL DEFAULT 1,
            payment_date DATE NOT NULL,
            amount_paid DECIMAL(15,2) NOT NULL,
            bank_account_id INT NULL,
            payment_card_id INT NULL,
            created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
            updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
            FOREIGN KEY (expense_id) REFERENCES expenses(expense_id) ON DELETE CASCADE
        ) ENGINE=InnoDB";
        
        $this->db->query($sql);
        $this->db->execute();
    }
    
    /**
     * Cập nhật một khoản chi tiêu
     * @param array $data Dữ liệu cập nhật
     * @return bool True nếu thành công, false nếu thất bại
     */    
    public function updateExpense($data) {
        try {
            // Debug data đầu vào
            error_log("[ExpenseModel] Updating expense with data: " . json_encode($data));
            
            // Kiểm tra cấu trúc bảng để xác định tên cột ID đúng
            $this->db->query("DESCRIBE expenses");
            $columns = $this->db->resultSet();
            $idColumn = 'expense_id'; // Mặc định
            
            foreach ($columns as $column) {
                if ($column->Field === 'id' && $column->Key === 'PRI') {
                    $idColumn = 'id';
                    break;
                }
            }
            
            // Log SQL query sẽ thực hiện
            $sql = "UPDATE expenses SET 
                     category_id = :category_id, 
                     amount = :amount, 
                     expense_date = :expense_date, 
                     description = :description, 
                     receipt_url = :receipt_url, 
                     status_id = :status_id 
                     WHERE {$idColumn} = :expense_id AND user_id = :user_id";
            
            error_log("[ExpenseModel] SQL: " . $sql);
            
            $this->db->query($sql);
            
            $this->db->bind(':category_id', $data['category_id']);
            $this->db->bind(':amount', $data['amount']);
            $this->db->bind(':expense_date', $data['expense_date']);
            $this->db->bind(':description', $data['description']);
            $this->db->bind(':receipt_url', $data['receipt_url']);
            $this->db->bind(':status_id', $data['status_id']);
            $this->db->bind(':expense_id', $data['expense_id']);
            $this->db->bind(':user_id', $data['user_id']);
            
            $result = $this->db->execute();
            error_log("[ExpenseModel] Update result: " . ($result ? "success" : "failed"));
            
            // Cập nhật thông tin thanh toán nếu có
            if($result && isset($data['payment_method']) && !empty($data['payment_method'])) {
                $this->updatePayment($data['expense_id'], $data);
            }
            
            return $result;
        } catch (Exception $e) {
            error_log("[ExpenseModel] Error in updateExpense: " . $e->getMessage());
            return false;
        }
    }
    
    /**
     * Cập nhật thông tin thanh toán cho một khoản chi tiêu
     * @param int $expenseId ID của khoản chi tiêu
     * @param array $data Dữ liệu thanh toán
     * @return bool True nếu thành công, false nếu thất bại
     */
    public function updatePayment($expenseId, $data) {
        // Kiểm tra xem đã có thanh toán cho khoản chi tiêu này chưa
        $this->db->query("SELECT * FROM expense_payments WHERE expense_id = :expense_id");
        $this->db->bind(':expense_id', $expenseId);
        $existingPayment = $this->db->single();
        
        if($existingPayment) {
            // Cập nhật thanh toán hiện có
            $this->db->query("UPDATE expense_payments SET method_id = :method_id, payment_date = :payment_date, 
                             amount_paid = :amount_paid WHERE payment_id = :payment_id");
            $this->db->bind(':method_id', $data['payment_method']);
            $this->db->bind(':payment_date', $data['payment_date'] ?? $data['expense_date']);
            $this->db->bind(':amount_paid', $data['amount']);
            $this->db->bind(':payment_id', $existingPayment->payment_id);
        } else {
            // Thêm thanh toán mới
            return $this->addPayment($expenseId, $data);
        }
        
        return $this->db->execute();
    }
      /**
     * Xóa một khoản chi tiêu và tất cả dữ liệu liên quan
     * @param int $id ID của khoản chi tiêu
     * @param int $userId ID của người dùng
     * @return bool Kết quả xóa
     */    public function deleteExpense($id, $userId) {
        try {
            // Xóa thông tin thanh toán trước
            $this->db->query('DELETE FROM expense_payments WHERE expense_id = :expense_id');
            $this->db->bind(':expense_id', $id);
            $this->db->execute();
              // Xóa khoản chi tiêu - sửa tên cột thành expense_id thay vì id
            $this->db->query('DELETE FROM expenses WHERE expense_id = :expense_id AND user_id = :user_id');
            $this->db->bind(':expense_id', $id);
            $this->db->bind(':user_id', $userId);
            $result = $this->db->execute();
            
            return $result;
        } catch (Exception $e) {
            error_log('Error in ExpenseModel::deleteExpense: ' . $e->getMessage());
            return false;
        }
    }
    
    /**
     * Xóa thông tin thanh toán cho một khoản chi tiêu
     * @param int $expenseId ID của khoản chi tiêu
     * @return bool Kết quả xóa
     */    public function deletePayment($expenseId) {
        try {
            $this->db->query('DELETE FROM expense_payments WHERE expense_id = :expense_id');
            $this->db->bind(':expense_id', $expenseId);
            
            return $this->db->execute();
        } catch (Exception $e) {
            error_log('Error in ExpenseModel::deletePayment: ' . $e->getMessage());
            return false;
        }
    }
    
    /**
     * Lấy tổng chi tiêu của người dùng trong một khoảng thời gian
     * @param int $userId ID của người dùng
     * @param string $startDate Ngày bắt đầu
     * @param string $endDate Ngày kết thúc
     * @return float Tổng chi tiêu
     */
    public function getTotalExpense($userId, $startDate = null, $endDate = null) {
        $sql = "SELECT SUM(amount) as total FROM expenses WHERE user_id = :user_id";
        
        if($startDate) {
            $sql .= " AND expense_date >= :start_date";
        }
        
        if($endDate) {
            $sql .= " AND expense_date <= :end_date";
        }
        
        $this->db->query($sql);
        $this->db->bind(':user_id', $userId);
        
        if($startDate) {
            $this->db->bind(':start_date', $startDate);
        }
        
        if($endDate) {
            $this->db->bind(':end_date', $endDate);
        }
        
        $result = $this->db->single();
        return $result->total ?? 0;
    }
    
    /**
     * Lấy chi tiêu theo danh mục trong một khoảng thời gian
     * @param int $userId ID của người dùng
     * @param string $startDate Ngày bắt đầu
     * @param string $endDate Ngày kết thúc
     * @return array Chi tiêu theo danh mục
     */
    public function getExpenseByCategory($userId, $startDate = null, $endDate = null) {
        $sql = "SELECT c.category_id, c.name, c.color, c.icon, SUM(e.amount) as total 
                FROM expenses e 
                JOIN categories c ON e.category_id = c.category_id 
                WHERE e.user_id = :user_id";
        
        if($startDate) {
            $sql .= " AND e.expense_date >= :start_date";
        }
        
        if($endDate) {
            $sql .= " AND e.expense_date <= :end_date";
        }
        
        $sql .= " GROUP BY c.category_id ORDER BY total DESC";
        
        $this->db->query($sql);
        $this->db->bind(':user_id', $userId);
        
        if($startDate) {
            $this->db->bind(':start_date', $startDate);
        }
        
        if($endDate) {
            $this->db->bind(':end_date', $endDate);
        }
        
        return $this->db->resultSet();
    }
    
    /**
     * Lấy tất cả các trạng thái chi tiêu
     * @return array Danh sách trạng thái
     */
    public function getAllStatuses() {
        try {
            // Kiểm tra bảng expense_status tồn tại không
            $this->db->query("SHOW TABLES LIKE 'expense_status'");
            $exists = count($this->db->resultSet()) > 0;
            
            if ($exists) {
                $this->db->query('SELECT status_id, status_name, description, color FROM expense_status ORDER BY status_id');
                $results = $this->db->resultSet();
                
                if (!empty($results)) {
                    error_log("[ExpenseModel] Found " . count($results) . " statuses from expense_status table");
                    return $results;
                }
            }
            
            // Nếu bảng không tồn tại hoặc không có dữ liệu, kiểm tra expense_statuses
            $this->db->query("SHOW TABLES LIKE 'expense_statuses'");
            $exists = count($this->db->resultSet()) > 0;
            
            if ($exists) {
                $this->db->query('SELECT status_id, name as status_name, color FROM expense_statuses ORDER BY status_id');
                $results = $this->db->resultSet();
                
                if (!empty($results)) {
                    error_log("[ExpenseModel] Found " . count($results) . " statuses from expense_statuses table");
                    return $results;
                }
            }
            
            // Nếu không có bảng hoặc dữ liệu nào, trả về mảng trạng thái mặc định
            error_log("[ExpenseModel] No status tables found, returning default statuses");
            return [
                (object)['status_id' => 1, 'status_name' => 'pending', 'description' => 'Chờ duyệt'],
                (object)['status_id' => 2, 'status_name' => 'approved', 'description' => 'Đã duyệt'],
                (object)['status_id' => 3, 'status_name' => 'rejected', 'description' => 'Bị từ chối']
            ];
        } catch (Exception $e) {
            error_log("[ExpenseModel] Error in getAllStatuses: " . $e->getMessage());
            return [];
        }
    }
    
    /**
     * Lấy tất cả các phương thức thanh toán
     * @return array Danh sách phương thức thanh toán
     */
    public function getAllPaymentMethods() {
        $this->db->query("SELECT * FROM payment_methods ORDER BY method_id");
        return $this->db->resultSet();
    }
    
    /**
     * Lấy thông tin thanh toán của một chi tiêu
     * @param int $expenseId ID của chi tiêu
     * @return object|bool Thông tin thanh toán hoặc false nếu không tìm thấy
     */
    public function getPaymentByExpenseId($expenseId) {
        try {
            // Kiểm tra bảng expense_payments tồn tại không
            $this->db->query("SHOW TABLES LIKE 'expense_payments'");
            $tableExists = (count($this->db->resultSet()) > 0);
            
            if(!$tableExists) {
                // Tạo bảng nếu chưa tồn tại
                $this->createExpensePaymentTable();
                return false; // Chưa có dữ liệu
            }
            
            // Sử dụng tên bảng chính xác là expense_payments
            $this->db->query("
                SELECT ep.*, pm.method_name
                FROM expense_payments ep
                LEFT JOIN payment_methods pm ON ep.method_id = pm.method_id
                WHERE ep.expense_id = :expense_id
            ");
            
            $this->db->bind(':expense_id', $expenseId);
            return $this->db->single();
        } catch (Exception $e) {
            error_log('Error in getPaymentByExpenseId: ' . $e->getMessage());
            return false;
        }
    }
    
    /**
     * Đếm số lượng chi tiêu của người dùng trong một khoảng thời gian
     * @param int $userId ID của người dùng
     * @param array $filters Bộ lọc
     * @return int Số lượng chi tiêu
     */
    public function countExpenses($userId, $filters = []) {
        $sql = "SELECT COUNT(*) as total FROM expenses WHERE user_id = :user_id";
        
        // Thêm điều kiện lọc theo thời gian nếu có
        if(isset($filters['start_date']) && !empty($filters['start_date'])) {
            $sql .= " AND expense_date >= :start_date";
        }
        
        if(isset($filters['end_date']) && !empty($filters['end_date'])) {
            $sql .= " AND expense_date <= :end_date";
        }
        
        // Lọc theo danh mục
        if(isset($filters['category_id']) && !empty($filters['category_id'])) {
            $sql .= " AND category_id = :category_id";
        }
        
        // Lọc theo trạng thái
        if(isset($filters['status_id']) && !empty($filters['status_id'])) {
            $sql .= " AND status_id = :status_id";
        }
        
        // Tìm kiếm theo mô tả
        if(isset($filters['search']) && !empty($filters['search'])) {
            $sql .= " AND description LIKE :search";
        }
        
        $this->db->query($sql);
        $this->db->bind(':user_id', $userId);
        
        // Bind các tham số lọc
        if(isset($filters['start_date']) && !empty($filters['start_date'])) {
            $this->db->bind(':start_date', $filters['start_date']);
        }
        
        if(isset($filters['end_date']) && !empty($filters['end_date'])) {
            $this->db->bind(':end_date', $filters['end_date']);
        }
        
        if(isset($filters['category_id']) && !empty($filters['category_id'])) {
            $this->db->bind(':category_id', $filters['category_id']);
        }
        
        if(isset($filters['status_id']) && !empty($filters['status_id'])) {
            $this->db->bind(':status_id', $filters['status_id']);
        }
        
        if(isset($filters['search']) && !empty($filters['search'])) {
            $this->db->bind(':search', '%' . $filters['search'] . '%');
        }
        
        $result = $this->db->single();
        return $result->total ?? 0;
    }
    
    /**
     * Lấy chi tiêu hàng ngày trong một khoảng thời gian
     * @param int $userId ID của người dùng
     * @param string $startDate Ngày bắt đầu
     * @param string $endDate Ngày kết thúc
     * @return array Chi tiêu hàng ngày
     */
    public function getDailyExpenses($userId, $startDate, $endDate) {
        $this->db->query("
            SELECT DATE(expense_date) as expense_date, SUM(amount) as total 
            FROM expenses 
            WHERE user_id = :user_id 
            AND expense_date BETWEEN :start_date AND :end_date 
            GROUP BY DATE(expense_date)
        ");
        
        $this->db->bind(':user_id', $userId);
        $this->db->bind(':start_date', $startDate);
        $this->db->bind(':end_date', $endDate);
        
        return $this->db->resultSet();
    }

    /**
     * Lấy ngân sách của người dùng cho tháng cụ thể
     * @param int $userId ID của người dùng
     * @param int $month Tháng (1-12)
     * @param int $year Năm
     * @return object|bool Thông tin ngân sách hoặc false nếu không có
     */
    public function getUserBudget($userId, $month, $year) {
        $startDate = "$year-$month-01";
        $endDate = date('Y-m-t', strtotime($startDate));
        
        $this->db->query("
            SELECT * FROM budgets 
            WHERE user_id = :user_id 
            AND start_date <= :end_date 
            AND end_date >= :start_date
            ORDER BY budget_id DESC 
            LIMIT 1
        ");
        
        $this->db->bind(':user_id', $userId);
        $this->db->bind(':start_date', $startDate);
        $this->db->bind(':end_date', $endDate);
        
        return $this->db->single();
    }

    /**
     * Đảm bảo tồn tại bảng payment_methods và có đầy đủ dữ liệu
     */
    public function initPaymentMethods() {
        // Tạo bảng payment_methods nếu chưa tồn tại
        $this->db->query("
            CREATE TABLE IF NOT EXISTS payment_methods (
                method_id INT AUTO_INCREMENT PRIMARY KEY,
                method_name VARCHAR(100) NOT NULL,
                icon VARCHAR(50) NULL,
                is_active TINYINT(1) DEFAULT 1,
                created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
                updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
            ) ENGINE=InnoDB
        ");
        $this->db->execute();
        
        // Kiểm tra xem bảng đã có dữ liệu chưa
        $this->db->query("SELECT COUNT(*) as count FROM payment_methods");
        $result = $this->db->single();
        
        // Nếu bảng rỗng, thêm dữ liệu mặc định
        if($result->count == 0) {
            $this->db->query("
                INSERT INTO payment_methods (method_id, method_name, icon) VALUES
                (1, 'Tiền mặt', 'mdi-cash'),
                (2, 'Chuyển khoản', 'mdi-bank'),
                (3, 'Thẻ tín dụng/ghi nợ', 'mdi-credit-card'),
                (4, 'Khác', 'mdi-dots-horizontal')
            ");
            $this->db->execute();
        }
    }

    /**
     * Đảm bảo tồn tại bảng expense_status và có đầy đủ dữ liệu
     */
    public function initExpenseStatus() {
        try {
            // Tạo bảng expense_status nếu chưa tồn tại với trường color
            $this->db->query("
                CREATE TABLE IF NOT EXISTS expense_status (
                    status_id INT AUTO_INCREMENT PRIMARY KEY,
                    status_name VARCHAR(50) NOT NULL,
                    description VARCHAR(255) NULL,
                    color VARCHAR(20) NULL DEFAULT '#cccccc',
                    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
                    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
                ) ENGINE=InnoDB
            ");
            $this->db->execute();
            
            // Kiểm tra xem có trường color trong bảng chưa
            $this->db->query("SHOW COLUMNS FROM expense_status LIKE 'color'");
            $hasColorField = (count($this->db->resultSet()) > 0);
            
            // Nếu chưa có trường color, thêm vào
            if (!$hasColorField) {
                $this->db->query("ALTER TABLE expense_status ADD COLUMN color VARCHAR(20) NULL DEFAULT '#cccccc' AFTER description");
                $this->db->execute();
                error_log("Added color column to expense_status table");
            }
            
            // Kiểm tra xem bảng đã có dữ liệu chưa
            $this->db->query("SELECT COUNT(*) as count FROM expense_status");
            $result = $this->db->single();
            
            // Nếu bảng rỗng, thêm dữ liệu mặc định
            if($result->count == 0) {
                $this->db->query("
                    INSERT INTO expense_status (status_id, status_name, description, color) VALUES
                    (1, 'pending', 'Chờ duyệt', '#ffc107'),
                    (2, 'approved', 'Đã duyệt', '#28a745'),
                    (3, 'rejected', 'Bị từ chối', '#dc3545')
                ");
                $this->db->execute();
            } else {
                // Cập nhật trạng thái một cách an toàn, chỉ cập nhật các trường chắc chắn tồn tại
                $this->db->query("UPDATE expense_status SET status_name = 'pending', description = 'Chờ duyệt' WHERE status_id = 1");
                $this->db->execute();
                
                $this->db->query("UPDATE expense_status SET status_name = 'approved', description = 'Đã duyệt' WHERE status_id = 2");
                $this->db->execute();
                
                $this->db->query("UPDATE expense_status SET status_name = 'rejected', description = 'Bị từ chối' WHERE status_id = 3");
                $this->db->execute();
                
                // Cập nhật màu riêng biệt nếu trường color tồn tại
                if ($hasColorField) {
                    $this->db->query("UPDATE expense_status SET color = '#ffc107' WHERE status_id = 1");
                    $this->db->execute();
                    
                    $this->db->query("UPDATE expense_status SET color = '#28a745' WHERE status_id = 2");
                    $this->db->execute();
                    
                    $this->db->query("UPDATE expense_status SET color = '#dc3545' WHERE status_id = 3");
                    $this->db->execute();
                }
            }
            
            return true;
        } catch (Exception $e) {
            error_log("Error in initExpenseStatus: " . $e->getMessage());
            return false;
        }
    }

    /**
     * Lấy tất cả danh mục chi tiêu với định dạng chuẩn
     * @param int $userId ID người dùng
     * @return array Danh sách danh mục
     */
    public function getAllFormattedCategories($userId) {
        try {
            $this->db->query('
                SELECT 
                    category_id as id, 
                    name, 
                    type, 
                    color 
                FROM expense_categories
                WHERE user_id = :user_id OR user_id IS NULL
                ORDER BY name
            ');
            
            $this->db->bind(':user_id', $userId);
            return $this->db->resultSet();
        } catch (Exception $e) {
            error_log("[ExpenseModel] Error in getAllFormattedCategories: " . $e->getMessage());
            return [];
        }
    }
}
