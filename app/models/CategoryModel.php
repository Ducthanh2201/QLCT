<?php
class CategoryModel {
    private $db;
    
    public function __construct() {
        $this->db = new Database();
        $this->ensureCategoryTable();
    }
    
    /**
     * Đảm bảo bảng categories tồn tại và có đúng cấu trúc
     */
    private function ensureCategoryTable() {
        try {
            // Tạo bảng categories nếu chưa tồn tại
            $this->db->query("
                CREATE TABLE IF NOT EXISTS categories (
                    category_id INT AUTO_INCREMENT PRIMARY KEY,
                    name VARCHAR(100) NOT NULL,
                    description TEXT NULL,
                    user_id INT NOT NULL,
                    type ENUM('income', 'expense') NOT NULL,
                    icon VARCHAR(50) NOT NULL,
                    color VARCHAR(20) NOT NULL,
                    created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
                    updated_at DATETIME DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
                    INDEX (name),
                    INDEX (user_id),
                    INDEX (type)
                ) ENGINE=InnoDB
            ");
            $this->db->execute();
            
            // Kiểm tra các trường cụ thể nếu cần
            $this->db->query("SHOW COLUMNS FROM categories LIKE 'icon'");
            $iconExists = count($this->db->resultSet()) > 0;
            
            if (!$iconExists) {
                $this->db->query("ALTER TABLE categories ADD COLUMN icon VARCHAR(50) NOT NULL AFTER type");
                $this->db->execute();
            }
            
            $this->db->query("SHOW COLUMNS FROM categories LIKE 'color'");
            $colorExists = count($this->db->resultSet()) > 0;
            
            if (!$colorExists) {
                $this->db->query("ALTER TABLE categories ADD COLUMN color VARCHAR(20) NOT NULL AFTER icon");
                $this->db->execute();
            }
            
        } catch (Exception $e) {
            error_log("[CategoryModel] Error in ensureCategoryTable: " . $e->getMessage());
        }
    }
    
    /**
     * Lấy danh sách danh mục theo loại
     * @param int $userId ID người dùng
     * @param string $type Loại danh mục ('expense' hoặc 'income')
     * @return array Danh sách danh mục
     */
    public function getCategoriesByType($userId, $type) {
        try {
            // Truy vấn danh mục từ bảng categories
            $sql = '
                SELECT 
                    category_id,
                    category_id AS id,
                    name,
                    description,
                    type,
                    user_id,
                    icon,
                    color,
                    created_at,
                    updated_at
                FROM categories
                WHERE (user_id = :user_id OR user_id = 0)';
                
            // Chỉ thêm điều kiện type nếu khác 'all'
            if ($type !== 'all') {
                $sql .= ' AND type = :type';
            }
            
            $sql .= ' ORDER BY name';
            
            $this->db->query($sql);
            $this->db->bind(':user_id', $userId);
            
            if ($type !== 'all') {
                $this->db->bind(':type', $type);
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
        try {
            $this->db->query("
                SELECT 
                    category_id,
                    name,
                    description,
                    user_id,
                    type,
                    icon,
                    color,
                    created_at,
                    updated_at
                FROM categories 
                WHERE category_id = :category_id AND (user_id = :user_id OR user_id = 0)
            ");
            
            $this->db->bind(':category_id', $categoryId);
            $this->db->bind(':user_id', $userId);
            
            $result = $this->db->single();
            
            if ($result) {
                error_log("[CategoryModel] getCategoryById: Found category with ID " . $categoryId);
            } else {
                error_log("[CategoryModel] getCategoryById: No category found with ID " . $categoryId . " for user " . $userId);
            }
            
            return $result;
        } catch (Exception $e) {
            error_log("[CategoryModel] Error in getCategoryById: " . $e->getMessage());
            return false;
        }
    }
    
    /**
     * Thêm một danh mục mới
     * @param array $data Dữ liệu danh mục
     * @return bool|int ID của danh mục nếu thành công, false nếu thất bại
     */
    public function addCategory($data) {
        try {
            $this->db->query("
                INSERT INTO categories (
                    name,
                    description,
                    user_id,
                    type,
                    icon,
                    color
                ) VALUES (
                    :name,
                    :description,
                    :user_id,
                    :type,
                    :icon,
                    :color
                )
            ");
            
            $this->db->bind(':name', $data['name']);
            $this->db->bind(':description', $data['description']);
            $this->db->bind(':user_id', $data['user_id']);
            $this->db->bind(':type', $data['type']);
            $this->db->bind(':icon', $data['icon']);
            $this->db->bind(':color', $data['color']);
            
            if($this->db->execute()) {
                $categoryId = $this->db->lastInsertId();
                error_log("[CategoryModel] Added new category with ID: " . $categoryId);
                return $categoryId;
            } else {
                error_log("[CategoryModel] Failed to add new category");
                return false;
            }
        } catch (Exception $e) {
            error_log("[CategoryModel] Error in addCategory: " . $e->getMessage());
            return false;
        }
    }
    
    /**
     * Cập nhật một danh mục
     * @param array $data Dữ liệu cập nhật
     * @return bool True nếu thành công, false nếu thất bại
     */
    public function updateCategory($data) {
        try {
            $this->db->query("
                UPDATE categories 
                SET 
                    name = :name, 
                    description = :description, 
                    icon = :icon, 
                    color = :color
                WHERE 
                    category_id = :category_id AND 
                    (user_id = :user_id OR user_id = 0)
            ");
            
            $this->db->bind(':name', $data['name']);
            $this->db->bind(':description', $data['description']);
            $this->db->bind(':icon', $data['icon']);
            $this->db->bind(':color', $data['color']);
            $this->db->bind(':category_id', $data['category_id']);
            $this->db->bind(':user_id', $data['user_id']);
            
            $result = $this->db->execute();
            
            if ($result) {
                error_log("[CategoryModel] Updated category with ID: " . $data['category_id']);
            } else {
                error_log("[CategoryModel] Failed to update category with ID: " . $data['category_id']);
            }
            
            return $result;
        } catch (Exception $e) {
            error_log("[CategoryModel] Error in updateCategory: " . $e->getMessage());
            return false;
        }
    }
    
    /**
     * Xóa một danh mục
     * @param int $categoryId ID của danh mục
     * @param int $userId ID của người dùng (để kiểm tra quyền)
     * @return bool True nếu thành công, false nếu thất bại
     */
    public function deleteCategory($categoryId, $userId) {
        try {
            // Kiểm tra xem danh mục có thuộc về người dùng không
            $this->db->query("SELECT * FROM categories WHERE category_id = :category_id AND user_id = :user_id");
            $this->db->bind(':category_id', $categoryId);
            $this->db->bind(':user_id', $userId);
            
            if(!$this->db->single()) {
                error_log("[CategoryModel] User $userId tried to delete category $categoryId but does not own it");
                return false; // Người dùng không có quyền xóa danh mục này
            }
            
            // Kiểm tra xem có chi tiêu nào thuộc danh mục này không
            $this->db->query("SELECT COUNT(*) as count FROM expenses WHERE category_id = :category_id");
            $this->db->bind(':category_id', $categoryId);
            $result = $this->db->single();
            
            if($result && $result->count > 0) {
                error_log("[CategoryModel] Cannot delete category $categoryId as it has " . $result->count . " expenses");
                return false; // Không thể xóa danh mục đã có chi tiêu
            }
            
            // Xóa danh mục
            $this->db->query("DELETE FROM categories WHERE category_id = :category_id AND user_id = :user_id");
            $this->db->bind(':category_id', $categoryId);
            $this->db->bind(':user_id', $userId);
            
            $result = $this->db->execute();
            
            if ($result) {
                error_log("[CategoryModel] Deleted category with ID: " . $categoryId);
            } else {
                error_log("[CategoryModel] Failed to delete category with ID: " . $categoryId);
            }
            
            return $result;
        } catch (Exception $e) {
            error_log("[CategoryModel] Error in deleteCategory: " . $e->getMessage());
            return false;
        }
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
        try {
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
        } catch (Exception $e) {
            error_log("[CategoryModel] Error in categoryNameExists: " . $e->getMessage());
            return false;
        }
    }

    /**
     * Thêm dữ liệu mẫu cho danh mục
     * @param int $userId ID người dùng
     * @return bool true nếu thành công
     */
    public function insertSampleCategories($userId) {
        try {
            // Kiểm tra đã có danh mục nào của user chưa
            $this->db->query("SELECT COUNT(*) as count FROM categories WHERE user_id = :user_id AND type = 'expense'");
            $this->db->bind(':user_id', $userId);
            $result = $this->db->single();
            
            // Nếu chưa có danh mục nào, thêm danh mục mẫu
            if ($result && $result->count == 0) {
                $defaultCategories = [
                    ['name' => 'Ăn uống', 'type' => 'expense', 'color' => '#FF5722', 'icon' => 'food.png', 'description' => 'Chi tiêu cho thực phẩm và ăn uống hàng ngày'],
                    ['name' => 'Di chuyển', 'type' => 'expense', 'color' => '#2196F3', 'icon' => 'transport.png', 'description' => 'Chi phí đi lại như xăng xe, vé tàu, taxi'],
                    ['name' => 'Mua sắm', 'type' => 'expense', 'color' => '#9C27B0', 'icon' => 'shopping.png', 'description' => 'Quần áo, giày dép, đồ dùng cá nhân'],
                    ['name' => 'Giải trí', 'type' => 'expense', 'color' => '#FF9800', 'icon' => 'entertainment.png', 'description' => 'Xem phim, tiệc tùng, sở thích cá nhân'],
                    ['name' => 'Hóa đơn', 'type' => 'expense', 'color' => '#F44336', 'icon' => 'bill.png', 'description' => 'Điện, nước, internet, điện thoại'],
                    ['name' => 'Sức khỏe', 'type' => 'expense', 'color' => '#4CAF50', 'icon' => 'health.png', 'description' => 'Khám bệnh, thuốc men, bảo hiểm'],
                    ['name' => 'Khác', 'type' => 'expense', 'color' => '#607D8B', 'icon' => 'other.png', 'description' => 'Các chi tiêu khác']
                ];
                
                $successCount = 0;
                foreach ($defaultCategories as $category) {
                    $this->db->query("
                        INSERT INTO categories (name, type, color, icon, description, user_id) 
                        VALUES (:name, :type, :color, :icon, :description, :user_id)
                    ");
                    
                    $this->db->bind(':name', $category['name']);
                    $this->db->bind(':type', $category['type']);
                    $this->db->bind(':color', $category['color']);
                    $this->db->bind(':icon', $category['icon']);
                    $this->db->bind(':description', $category['description']);
                    $this->db->bind(':user_id', $userId);
                    
                    if ($this->db->execute()) {
                        $successCount++;
                    }
                }
                
                error_log("[CategoryModel] Inserted $successCount sample categories for user $userId");
                return ($successCount > 0);
            }
            
            error_log("[CategoryModel] User $userId already has categories, not inserting samples");
            return false;
        } catch (Exception $e) {
            error_log("[CategoryModel] Error in insertSampleCategories: " . $e->getMessage());
            return false;
        }
    }
    
    /**
     * Lấy tổng số danh mục của người dùng
     * @param int $userId ID người dùng
     * @param string $type Loại danh mục ('expense', 'income', hoặc 'all')
     * @return int Tổng số danh mục
     */
    public function countCategories($userId, $type = 'all') {
        try {
            $sql = "SELECT COUNT(*) as total FROM categories WHERE user_id = :user_id";
            
            if ($type !== 'all') {
                $sql .= " AND type = :type";
            }
            
            $this->db->query($sql);
            $this->db->bind(':user_id', $userId);
            
            if ($type !== 'all') {
                $this->db->bind(':type', $type);
            }
            
            $result = $this->db->single();
            
            return ($result) ? $result->total : 0;
        } catch (Exception $e) {
            error_log("[CategoryModel] Error in countCategories: " . $e->getMessage());
            return 0;
        }
    }
    
    /**
     * Kiểm tra xem danh mục có được sử dụng trong chi tiêu không
     * @param int $categoryId ID của danh mục
     * @return bool True nếu đang được sử dụng, false nếu không
     */
    public function isUsedInExpenses($categoryId) {
        try {
            $this->db->query("SELECT COUNT(*) as count FROM expenses WHERE category_id = :category_id");
            $this->db->bind(':category_id', $categoryId);
            $result = $this->db->single();
            
            return ($result && $result->count > 0);
        } catch (Exception $e) {
            error_log("[CategoryModel] Error in isUsedInExpenses: " . $e->getMessage());
            return false;
        }
    }
}