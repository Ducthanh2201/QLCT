<?php
class DashboardController extends BaseController {
    public function __construct() {
        // Kiểm tra đăng nhập trước khi khởi tạo controller
        if (!isset($_SESSION['user_id'])) {
            header('Location: ' . BASEURL . '/auth/login');
            exit;
        }
        
        // Khởi tạo model nếu cần
    }
    
    public function index() {
        // Khởi tạo dữ liệu mặc định để tránh lỗi undefined index
        $data = [
            'title' => 'Dashboard - Quản Lý Chi Tiêu',
            'total_balance' => 0,
            'total_income' => 0,
            'total_expense' => 0,
            'budget_spent_percent' => 0,
            'budget_progress_class' => 'bg-success',
            'budget_message' => 'Bạn đang chi tiêu trong phạm vi ngân sách.',
            'date_range' => date('01/m/Y') . ' - ' . date('d/m/Y'),
            'monthly_income' => 0,
            'income_trend' => 0,
            'monthly_expense' => 0,
            'expense_trend' => 0,
            'recent_expenses' => [],
            'this_month' => 0,
            'month_compare' => 0,
            'fixed_expenses' => 0,
            'fixed_income' => 0,
            'balance_after_fixed' => 0,
            'category_expenses' => [],
            'income_chart_data' => [],
            'expense_chart_data' => [],
            'category_chart_data' => [],
            'time_chart_data' => [],
            'method_chart_data' => [],
            'upcoming_expenses' => [],
            'total_budget' => 0,
            'total_expense_month' => 0,
            'total_income_month' => 0,
            'savings' => 0,
            'savings_percentage' => 0,
            'financial_goals' => []
        ];
        
        // TODO: Nạp dữ liệu thực tế từ model
        // Ví dụ:
        // $expenseModel = $this->model('ExpenseModel');
        // $data['total_expense'] = $expenseModel->getTotalExpense();
        
        // Load view
        $this->view('dashboard/index', $data);
    }
}