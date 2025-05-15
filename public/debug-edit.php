<?php
// Hiển thị lỗi
ini_set('display_errors', 1);
error_reporting(E_ALL);

// Định nghĩa hằng số
define('DS', DIRECTORY_SEPARATOR);
define('ROOT', dirname(dirname(__FILE__)));
define('APPROOT', ROOT . DS . 'app');
define('BASEURL', 'http://localhost/ADM-QLCT/expense-management');

// Kiểm tra session
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

// Load các file cần thiết
require_once APPROOT . DS . 'config' . DS . 'config.php';
require_once APPROOT . DS . 'core' . DS . 'Database.php';
require_once APPROOT . DS . 'models' . DS . 'CategoryModel.php';
require_once APPROOT . DS . 'models' . DS . 'ExpenseModel.php';

echo "<h1>Debug Danh mục chi tiêu</h1>";

// Thiết lập hoặc lấy user_id từ session
$userId = $_SESSION['user_id'] ?? 1;
echo "<p>User ID: $userId</p>";

// Thử lấy danh mục từ cả hai bảng
$db = new Database();

echo "<h2>1. KIỂM TRA BẢNG CATEGORIES</h2>";
// Kiểm tra bảng categories
$db->query("SHOW TABLES LIKE 'categories'");
$hasCategoriesTable = count($db->resultSet()) > 0;

if ($hasCategoriesTable) {
    echo "<p>Bảng 'categories' tồn tại ✓</p>";
    
    // Lấy dữ liệu từ bảng categories
    $db->query("SELECT * FROM categories WHERE (user_id = :user_id OR user_id IS NULL)");
    $db->bind(':user_id', $userId);
    $categories = $db->resultSet();
    
    if (!empty($categories)) {
        echo "<p>Số lượng danh mục từ bảng categories: " . count($categories) . "</p>";
        echo "<h3>Cấu trúc dữ liệu danh mục đầu tiên:</h3>";
        echo "<pre>";
        print_r($categories[0]);
        echo "</pre>";
        
        // Hiển thị danh sách
        echo "<table border='1' cellpadding='5'>";
        echo "<tr><th>ID</th><th>Tên</th><th>Loại</th><th>User ID</th></tr>";
        foreach ($categories as $cat) {
            echo "<tr>";
            echo "<td>" . ($cat->category_id ?? ($cat->id ?? 'N/A')) . "</td>";
            echo "<td>" . ($cat->name ?? ($cat->category_name ?? 'N/A')) . "</td>";
            echo "<td>" . ($cat->type ?? 'N/A') . "</td>";
            echo "<td>" . ($cat->user_id ?? 'N/A') . "</td>";
            echo "</tr>";
        }
        echo "</table>";
    } else {
        echo "<p>Không tìm thấy danh mục nào trong bảng categories</p>";
    }
} else {
    echo "<p>Bảng 'categories' không tồn tại ✗</p>";
}

echo "<h2>2. KIỂM TRA BẢNG EXPENSE_CATEGORIES</h2>";
// Kiểm tra bảng expense_categories
$db->query("SHOW TABLES LIKE 'expense_categories'");
$hasExpenseCategoriesTable = count($db->resultSet()) > 0;

if ($hasExpenseCategoriesTable) {
    echo "<p>Bảng 'expense_categories' tồn tại ✓</p>";
    
    // Lấy dữ liệu từ bảng expense_categories
    $db->query("SELECT * FROM expense_categories WHERE (user_id = :user_id OR user_id IS NULL) AND type = 'expense'");
    $db->bind(':user_id', $userId);
    $expenseCategories = $db->resultSet();
    
    if (!empty($expenseCategories)) {
        echo "<p>Số lượng danh mục từ bảng expense_categories: " . count($expenseCategories) . "</p>";
        echo "<h3>Cấu trúc dữ liệu danh mục đầu tiên:</h3>";
        echo "<pre>";
        print_r($expenseCategories[0]);
        echo "</pre>";
        
        // Hiển thị danh sách
        echo "<table border='1' cellpadding='5'>";
        echo "<tr><th>ID</th><th>Tên</th><th>Loại</th><th>User ID</th></tr>";
        foreach ($expenseCategories as $cat) {
            echo "<tr>";
            echo "<td>" . ($cat->category_id ?? ($cat->id ?? 'N/A')) . "</td>";
            echo "<td>" . ($cat->name ?? ($cat->category_name ?? 'N/A')) . "</td>";
            echo "<td>" . ($cat->type ?? 'N/A') . "</td>";
            echo "<td>" . ($cat->user_id ?? 'N/A') . "</td>";
            echo "</tr>";
        }
        echo "</table>";
    } else {
        echo "<p>Không tìm thấy danh mục nào trong bảng expense_categories</p>";
    }
} else {
    echo "<p>Bảng 'expense_categories' không tồn tại ✗</p>";
}

echo "<h2>3. KIỂM TRA CategoryModel</h2>";

try {
    $categoryModel = new CategoryModel();
    
    // Test getCategoriesByType
    $categoriesByType = $categoryModel->getCategoriesByType($userId, 'expense');
    
    if (!empty($categoriesByType)) {
        echo "<p>Số lượng danh mục từ getCategoriesByType: " . count($categoriesByType) . "</p>";
        
        // Hiển thị cấu trúc
        echo "<h3>Cấu trúc dữ liệu danh mục đầu tiên:</h3>";
        echo "<pre>";
        print_r($categoriesByType[0]);
        echo "</pre>";
        
        // Hiển thị danh sách
        echo "<table border='1' cellpadding='5'>";
        echo "<tr><th>ID</th><th>Category ID</th><th>Tên</th><th>Loại</th></tr>";
        foreach ($categoriesByType as $cat) {
            echo "<tr>";
            echo "<td>" . (isset($cat->id) ? $cat->id : 'undefined') . "</td>";
            echo "<td>" . (isset($cat->category_id) ? $cat->category_id : 'undefined') . "</td>";
            echo "<td>" . (isset($cat->name) ? $cat->name : 'undefined') . "</td>";
            echo "<td>" . (isset($cat->type) ? $cat->type : 'undefined') . "</td>";
            echo "</tr>";
        }
        echo "</table>";
    } else {
        echo "<p>Không tìm thấy danh mục nào từ getCategoriesByType</p>";
    }
} catch (Exception $e) {
    echo "<p>Lỗi: " . $e->getMessage() . "</p>";
}

echo "<h2>4. KIỂM TRA ExpenseModel</h2>";

try {
    $expenseModel = new ExpenseModel();
    
    if (method_exists($expenseModel, 'getAllFormattedCategories')) {
        // Test getAllFormattedCategories
        $formattedCategories = $expenseModel->getAllFormattedCategories($userId);
        
        if (!empty($formattedCategories)) {
            echo "<p>Số lượng danh mục từ getAllFormattedCategories: " . count($formattedCategories) . "</p>";
            
            // Hiển thị cấu trúc
            echo "<h3>Cấu trúc dữ liệu danh mục đầu tiên:</h3>";
            echo "<pre>";
            print_r($formattedCategories[0]);
            echo "</pre>";
            
            // Hiển thị danh sách
            echo "<table border='1' cellpadding='5'>";
            echo "<tr><th>ID</th><th>Tên</th><th>Loại</th></tr>";
            foreach ($formattedCategories as $cat) {
                echo "<tr>";
                echo "<td>" . (isset($cat->id) ? $cat->id : 'undefined') . "</td>";
                echo "<td>" . (isset($cat->name) ? $cat->name : 'undefined') . "</td>";
                echo "<td>" . (isset($cat->type) ? $cat->type : 'undefined') . "</td>";
                echo "</tr>";
            }
            echo "</table>";
        } else {
            echo "<p>Không tìm thấy danh mục nào từ getAllFormattedCategories</p>";
        }
    } else {
        echo "<p>Phương thức getAllFormattedCategories không tồn tại trong ExpenseModel</p>";
    }
} catch (Exception $e) {
    echo "<p>Lỗi: " . $e->getMessage() . "</p>";
}

// Thêm liên kết tới các trang liên quan
echo "<h2>Các liên kết hữu ích</h2>";
echo "<ul>";
echo "<li><a href='" . BASEURL . "/expenses'>Danh sách chi tiêu</a></li>";
echo "<li><a href='" . BASEURL . "/expenses/create'>Thêm chi tiêu mới</a></li>";
echo "</ul>";