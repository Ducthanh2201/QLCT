<?php
class CategoryModel {
    private $db;
    
    public function __construct() {
        $this->db = new Database();
    }
    
    /**
     * Lấy danh sách danh mục theo loại
     * @param int $userId ID người dùng
     * @param string $type Loại danh mục ('expense' hoặc 'income')
     * @return array Danh sách danh mục
     */
    public function getCategoriesByType($userId, $type) {
        try {
            $sql = '';
            
            // Kiểm tra xem có bảng expense_categories không
            $this->db->query("SHOW TABLES LIKE 'expense_categories'");
            $expenseCategoriesExists = count($this->db->resultSet()) > 0;
            error_log("[CategoryModel] expense_categories table exists: " . ($expenseCategoriesExists ? 'yes' : 'no'));
            
            if ($expenseCategoriesExists) {
                // Nếu có bảng expense_categories thì truy vấn từ đó
                $sql = '
                    SELECT 
                        category_id,
                        category_id AS id,
                        name,
                        type,
                        color,
                        icon,
                        description,
                        user_id
                    FROM expense_categories
                    WHERE (user_id = :user_id OR user_id IS NULL)';
                    
                // Chỉ thêm điều kiện type nếu được chỉ định 
                if ($type !== 'all') {
                    $sql .= ' AND type = :type';
                }
                
                $sql .= ' ORDER BY name';
                
                $this->db->query($sql);
                $this->db->bind(':user_id', $userId);
                
                if ($type !== 'all') {
                    $this->db->bind(':type', $type);
                }
            } else {
                // Nếu không có bảng expense_categories thì truy vấn từ bảng categories
                $sql = '
                    SELECT 
                        category_id,
                        category_id AS id,
                        name,
                        type,
                        color,
                        icon,
                        description,
                        user_id
                    FROM categories
                    WHERE (user_id = :user_id OR user_id IS NULL)';
                    
                // Chỉ thêm điều kiện type nếu được chỉ định 
                if ($type !== 'all') {
                    $sql .= ' AND type = :type';
                }
                
                $sql .= ' ORDER BY name';
                
                $this->db->query($sql);
                $this->db->bind(':user_id', $userId);
                
                if ($type !== 'all') {
                    $this->db->bind(':type', $type);
                }
            }
            
            $results = $this->db->resultSet();
            
            // Log debug info
            error_log("[CategoryModel] getCategoriesByType($userId, $type) returned " . count($results) . " records");
            if (!empty($results)) {
                error_log("[CategoryModel] First category: " . json_encode($results[0]));
            }
            
            return $results;
        } catch (Exception $e) {
            error_log("[CategoryModel] Error in getCategoriesByType: " . $e->getMessage());
            return [];
        }
    }
    
    /**
     * Lấy chi tiết một danh mục
     * @param int $categoryId ID của danh mục
     * @param int $userId ID của người dùng (để kiểm tra quyền)
     * @return object|bool Chi tiết danh mục hoặc false nếu không tìm thấy
     */
    public function getCategoryById($categoryId, $userId) {
        $this->db->query("SELECT * FROM categories 
                         WHERE category_id = :category_id AND user_id = :user_id");
        $this->db->bind(':category_id', $categoryId);
        $this->db->bind(':user_id', $userId);
        
        return $this->db->single();
    }
    
    /**
     * Thêm một danh mục mới
     * @param array $data Dữ liệu danh mục
     * @return bool|int ID của danh mục nếu thành công, false nếu thất bại
     */
    public function addCategory($data) {
        $this->db->query("INSERT INTO categories (name, description, user_id, type, icon, color) 
                         VALUES (:name, :description, :user_id, :type, :icon, :color)");
        
        $this->db->bind(':name', $data['name']);
        $this->db->bind(':description', $data['description']);
        $this->db->bind(':user_id', $data['user_id']);
        $this->db->bind(':type', $data['type']);
        $this->db->bind(':icon', $data['icon']);
        $this->db->bind(':color', $data['color']);
        
        if($this->db->execute()) {
            return $this->db->lastInsertId();
        } else {
            return false;
        }
    }
    
    /**
     * Cập nhật một danh mục
     * @param array $data Dữ liệu cập nhật
     * @return bool True nếu thành công, false nếu thất bại
     */
    public function updateCategory($data) {
        $this->db->query("UPDATE categories SET name = :name, description = :description, 
                         icon = :icon, color = :color 
                         WHERE category_id = :category_id AND user_id = :user_id");
        
        $this->db->bind(':name', $data['name']);
        $this->db->bind(':description', $data['description']);
        $this->db->bind(':icon', $data['icon']);
        $this->db->bind(':color', $data['color']);
        $this->db->bind(':category_id', $data['category_id']);
        $this->db->bind(':user_id', $data['user_id']);
        
        return $this->db->execute();
    }
    
    /**
     * Xóa một danh mục
     * @param int $categoryId ID của danh mục
     * @param int $userId ID của người dùng (để kiểm tra quyền)
     * @return bool True nếu thành công, false nếu thất bại
     */
    public function deleteCategory($categoryId, $userId) {
        // Kiểm tra xem danh mục có thuộc về người dùng không
        $this->db->query("SELECT * FROM categories WHERE category_id = :category_id AND user_id = :user_id");
        $this->db->bind(':category_id', $categoryId);
        $this->db->bind(':user_id', $userId);
        
        if(!$this->db->single()) {
            return false; // Người dùng không có quyền xóa danh mục này
        }
        
        // Kiểm tra xem có chi tiêu nào thuộc danh mục này không
        $this->db->query("SELECT COUNT(*) as count FROM expenses WHERE category_id = :category_id");
        $this->db->bind(':category_id', $categoryId);
        $result = $this->db->single();
        
        if($result && $result->count > 0) {
            return false; // Không thể xóa danh mục đã có chi tiêu
        }
        
        // Xóa danh mục
        $this->db->query("DELETE FROM categories WHERE category_id = :category_id AND user_id = :user_id");
        $this->db->bind(':category_id', $categoryId);
        $this->db->bind(':user_id', $userId);
        
        return $this->db->execute();
    }
    
    /**
     * Kiểm tra xem tên danh mục đã tồn tại chưa
     * @param string $name Tên danh mục
     * @param int $userId ID của người dùng
     * @param string $type Loại danh mục
     * @param int $excludeId ID của danh mục cần loại trừ (dùng khi cập nhật)
     * @return bool True nếu tên đã tồn tại, false nếu chưa
     */
    public function categoryNameExists($name, $userId, $type, $excludeId = 0) {
        $sql = "SELECT * FROM categories WHERE name = :name AND user_id = :user_id AND type = :type";
        
        if($excludeId > 0) {
            $sql .= " AND category_id != :exclude_id";
        }
        
        $this->db->query($sql);
        $this->db->bind(':name', $name);
        $this->db->bind(':user_id', $userId);
        $this->db->bind(':type', $type);
        
        if($excludeId > 0) {
            $this->db->bind(':exclude_id', $excludeId);
        }
        
        $row = $this->db->single();
        
        // Nếu có kết quả, tên danh mục đã tồn tại
        return ($row) ? true : false;
    }

    /**
     * Thêm dữ liệu mẫu cho danh mục
     * @param int $userId ID người dùng
     * @return bool true nếu thành công
     */
    public function insertSampleCategories($userId) {
        try {
            // Kiểm tra xem có bảng expense_categories không
            $this->db->query("SHOW TABLES LIKE 'expense_categories'");
            $expenseCategoriesExists = count($this->db->resultSet()) > 0;
            
            $tableName = $expenseCategoriesExists ? 'expense_categories' : 'categories';
            
            // Kiểm tra đã có danh mục nào của user chưa
            $this->db->query("SELECT COUNT(*) as count FROM $tableName WHERE user_id = :user_id AND type = 'expense'");
            $this->db->bind(':user_id', $userId);
            $result = $this->db->single();
            
            // Nếu chưa có danh mục nào, thêm danh mục mẫu
            if ($result && $result->count == 0) {
                $defaultCategories = [
                    ['name' => 'Ăn uống', 'type' => 'expense', 'color' => '#FF5722', 'icon' => 'food.png'],
                    ['name' => 'Di chuyển', 'type' => 'expense', 'color' => '#2196F3', 'icon' => 'transport.png'],
                    ['name' => 'Mua sắm', 'type' => 'expense', 'color' => '#9C27B0', 'icon' => 'shopping.png'],
                    ['name' => 'Giải trí', 'type' => 'expense', 'color' => '#FF9800', 'icon' => 'entertainment.png'],
                    ['name' => 'Hóa đơn', 'type' => 'expense', 'color' => '#F44336', 'icon' => 'bills.png'],
                    ['name' => 'Sức khỏe', 'type' => 'expense', 'color' => '#4CAF50', 'icon' => 'health.png'],
                    ['name' => 'Khác', 'type' => 'expense', 'color' => '#607D8B', 'icon' => 'other.png']
                ];
                
                foreach ($defaultCategories as $category) {
                    $this->db->query("INSERT INTO $tableName (name, type, color, icon, user_id) VALUES (:name, :type, :color, :icon, :user_id)");
                    $this->db->bind(':name', $category['name']);
                    $this->db->bind(':type', $category['type']);
                    $this->db->bind(':color', $category['color']);
                    $this->db->bind(':icon', $category['icon']);
                    $this->db->bind(':user_id', $userId);
                    $this->db->execute();
                }
                
                return true;
            }
            
            return false;
        } catch (Exception $e) {
            error_log("[CategoryModel] Error in insertSampleCategories: " . $e->getMessage());
            return false;
        }
    }
}