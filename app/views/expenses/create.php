<?php
// filepath: c:\xampp\htdocs\ADM-QLCT\expense-management\app\views\expenses\create.php
require APPROOT . '/views/layouts/header.php';
require APPROOT . '/views/layouts/sidebar.php';
?>

<?php
// Thêm dòng này để định nghĩa ENVIRONMENT nếu chưa có
if (!defined('ENVIRONMENT')) {
    define('ENVIRONMENT', 'production'); // hoặc 'development' nếu đang phát triển
}

// Kiểm tra và khởi tạo mảng stats nếu chưa có hoặc thiếu key
if (!isset($data['stats']) || !is_array($data['stats'])) {
    $data['stats'] = [
        'this_month' => 0, // Thêm key this_month
        'budget_total' => 0,
        'budget_used' => 0,
        'budget_remaining' => 0,
        'budget_percentage' => 0,
        'budget_progress' => 0, // Thêm key budget_progress
        'budget_progress_class' => 'primary' // Thêm key budget_progress_class
    ];
}

// Đảm bảo tất cả các key cần thiết đều tồn tại
$defaultStats = [
    'this_month' => 0,
    'budget_total' => 0,
    'budget_used' => 0,
    'budget_remaining' => 0,
    'budget_percentage' => 0,
    'budget_progress' => 0,
    'budget_progress_class' => 'primary'
];

foreach ($defaultStats as $key => $defaultValue) {
    if (!isset($data['stats'][$key])) {
        $data['stats'][$key] = $defaultValue;
    }
}

// Khởi tạo mảng charts nếu không tồn tại
if (!isset($data['charts']) || !is_array($data['charts'])) {
    $data['charts'] = [
        'by_category' => [
            'labels' => [],
            'values' => [],
            'colors' => []
        ]
    ];
}

// Đảm bảo các mảng dữ liệu đầu vào tồn tại
if (!isset($data['bank_accounts']) || !is_array($data['bank_accounts'])) {
    $data['bank_accounts'] = [];
}

if (!isset($data['cards']) || !is_array($data['cards'])) {
    $data['cards'] = [];
}

// Đảm bảo mảng categories luôn tồn tại
if (!isset($data['categories']) || !is_array($data['categories'])) {
    $data['categories'] = [];
}
?>

<div class="container-fluid page-body-wrapper">
  <?php require APPROOT . '/views/partials/_settings-panel.php'; ?>
  <?php require APPROOT . '/views/layouts/navbar.php'; ?>
  
  <div class="main-panel">
    <div class="content-wrapper">
      <?php require APPROOT . '/views/partials/_message.php'; ?>
      
      <div class="page-header">
        <h3 class="page-title">Thêm mới khoản chi tiêu</h3>
        <nav aria-label="breadcrumb">
          <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="<?php echo BASEURL; ?>/dashboard">Dashboard</a></li>
            <li class="breadcrumb-item"><a href="<?php echo BASEURL; ?>/expenses">Danh sách chi tiêu</a></li>
            <li class="breadcrumb-item active" aria-current="page">Thêm mới</li>
          </ol>
        </nav>
      </div>
      
      <div class="row">
        <div class="col-md-8 grid-margin stretch-card">
          <div class="card">
            <div class="card-body">
              <h4 class="card-title">Thông tin chi tiêu</h4>
              <p class="card-description">Vui lòng nhập đầy đủ thông tin</p>
              
              <form class="forms-sample" method="post" action="<?php echo BASEURL; ?>/expenses/create" enctype="multipart/form-data">
                <div class="form-group">
                  <label for="expense_date">Ngày chi tiêu <span class="text-danger">*</span></label>
                  <input type="date" class="form-control" id="expense_date" name="expense_date" 
                         value="<?php echo isset($data['expense_date']) ? $data['expense_date'] : date('Y-m-d'); ?>" required>
                </div>
                
                <div class="form-group">
                  <label for="category_id">Danh mục <span class="text-danger">*</span></label>
                  <select class="form-control" id="category_id" name="category_id" required>
                    <option value="">-- Chọn danh mục --</option>
                    <?php if(isset($data['categories']) && !empty($data['categories'])): ?>
                      <?php foreach($data['categories'] as $categoryItem): ?>
                        <?php if(is_object($categoryItem) && isset($categoryItem->category_id) && isset($categoryItem->name)): ?>
                          <option value="<?php echo get_property_safe($categoryItem, 'category_id', ''); ?>" 
                                  <?php echo (isset($data['category_id']) && $data['category_id'] == get_property_safe($categoryItem, 'category_id', '')) ? 'selected' : ''; ?>>
                            <?php echo get_property_safe($categoryItem, 'name', 'Không xác định'); ?>
                          </option>
                        <?php endif; ?>
                      <?php endforeach; ?>
                    <?php endif; ?>
                  </select>
                  <?php if(isset($data['errors']['category_id'])): ?>
                    <small class="text-danger"><?php echo htmlspecialchars($data['errors']['category_id']); ?></small>
                  <?php endif; ?>
                  <div class="mt-2">
                    <a href="<?php echo BASEURL; ?>/expense-categories/create" class="text-primary">
                      <i class="mdi mdi-plus-circle"></i> Thêm danh mục mới
                    </a>
                  </div>
                </div>
                
                <div class="form-group">
                  <label for="description">Mô tả <span class="text-danger">*</span></label>
                  <input type="text" class="form-control" id="description" name="description" 
                         value="<?php echo isset($data['description']) ? $data['description'] : ''; ?>" 
                         placeholder="Nhập mô tả chi tiết về khoản chi tiêu" required>
                </div>
                
                <div class="form-group">
                  <label for="amount">Số tiền <span class="text-danger">*</span></label>
                  <div class="input-group">
                    <input type="number" class="form-control" id="amount" name="amount" 
                           value="<?php echo isset($data['amount']) ? $data['amount'] : ''; ?>" 
                           placeholder="Nhập số tiền" min="1000" step="1000" required>
                    <div class="input-group-append">
                      <span class="input-group-text">VNĐ</span>
                    </div>
                  </div>
                </div>
                
                <div class="form-group">
                  <label for="payment_method">Phương thức thanh toán <span class="text-danger">*</span></label>
                  <select class="form-control" id="payment_method" name="payment_method" required>
                    <option value="cash" <?php echo (isset($data['payment_method']) && $data['payment_method'] == 'cash') ? 'selected' : ''; ?>>Tiền mặt</option>
                    <option value="bank" <?php echo (isset($data['payment_method']) && $data['payment_method'] == 'bank') ? 'selected' : ''; ?>>Chuyển khoản</option>
                    <option value="card" <?php echo (isset($data['payment_method']) && $data['payment_method'] == 'card') ? 'selected' : ''; ?>>Thẻ tín dụng/ghi nợ</option>
                    <option value="other" <?php echo (isset($data['payment_method']) && $data['payment_method'] == 'other') ? 'selected' : ''; ?>>Khác</option>
                  </select>
                </div>
                
                <!-- Phần hiển thị tài khoản ngân hàng -->
                <div class="form-group" id="bank_info" style="display: <?php echo (isset($data['payment_method']) && $data['payment_method'] == 'bank') ? 'block' : 'none'; ?>;">
                  <label for="bank_details">Thông tin ngân hàng</label>
                  <?php if(isset($data['bank_accounts']) && !empty($data['bank_accounts'])): ?>
                    <select class="form-control" id="bank_account" name="bank_account">
                      <option value="">-- Chọn tài khoản ngân hàng --</option>
                      <?php foreach($data['bank_accounts'] as $bankItem): ?>
                        <?php if(is_object($bankItem) && isset($bankItem->id)): ?>
                          <option value="<?php echo $bankItem->id; ?>" <?php echo (isset($data['bank_account']) && $data['bank_account'] == $bankItem->id) ? 'selected' : ''; ?>>
                            <?php echo (isset($bankItem->bank_name) ? $bankItem->bank_name : 'Không xác định') . ' - ' . (isset($bankItem->account_number) ? $bankItem->account_number : '******'); ?>
                          </option>
                        <?php endif; ?>
                      <?php endforeach; ?>
                    </select>
                    <div class="mt-2">
                      <a href="<?php echo BASEURL; ?>/payment-methods/add-bank-account" class="text-primary">
                        <i class="mdi mdi-plus-circle"></i> Thêm tài khoản ngân hàng mới
                      </a>
                    </div>
                  <?php else: ?>
                    <div class="alert alert-info">
                      Bạn chưa có tài khoản ngân hàng nào. <a href="<?php echo BASEURL; ?>/payment-methods/add-bank-account" class="alert-link">Thêm tài khoản ngân hàng</a>
                    </div>
                  <?php endif; ?>
                </div>

                <!-- Phần hiển thị thẻ thanh toán -->
                <div class="form-group" id="card_info" style="display: <?php echo (isset($data['payment_method']) && $data['payment_method'] == 'card') ? 'block' : 'none'; ?>;">
                  <label for="card_number">Thẻ thanh toán</label>
                  <?php if(isset($data['cards']) && !empty($data['cards'])): ?>
                    <select class="form-control" id="card_number" name="card_number">
                      <option value="">-- Chọn thẻ --</option>
                      <?php foreach($data['cards'] as $cardItem): ?>
                        <?php if(is_object($cardItem) && isset($cardItem->id)): ?>
                          <option value="<?php echo $cardItem->id; ?>" <?php echo (isset($data['card_number']) && $data['card_number'] == $cardItem->id) ? 'selected' : ''; ?>>
                            <?php echo (isset($cardItem->bank_name) ? $cardItem->bank_name : 'Không xác định') . ' - ' . (isset($cardItem->card_number) ? substr($cardItem->card_number, -4) : '****'); ?>
                          </option>
                        <?php endif; ?>
                      <?php endforeach; ?>
                    </select>
                    <div class="mt-2">
                      <a href="<?php echo BASEURL; ?>/payment-methods/add-payment-card" class="text-primary">
                        <i class="mdi mdi-plus-circle"></i> Thêm thẻ thanh toán mới
                      </a>
                    </div>
                  <?php else: ?>
                    <div class="alert alert-info">
                      Bạn chưa có thẻ thanh toán nào. <a href="<?php echo BASEURL; ?>/payment-methods/add-payment-card" class="alert-link">Thêm thẻ thanh toán</a>
                    </div>
                  <?php endif; ?>
                </div>
                
                <div class="form-group">
                  <label for="status">Trạng thái <span class="text-danger">*</span></label>
                  <select class="form-control" id="status" name="status" required>
                    <option value="paid" <?php echo (isset($data['status']) && $data['status'] == 'paid') ? 'selected' : ''; ?>>Đã thanh toán</option>
                    <option value="pending" <?php echo (isset($data['status']) && $data['status'] == 'pending') ? 'selected' : ''; ?>>Chờ thanh toán</option>
                  </select>
                </div>
                
                <div class="form-group">
                  <label for="receipt_image">Hình ảnh hóa đơn</label>
                  <input type="file" class="form-control-file" id="receipt_image" name="receipt_image" accept="image/*">
                  <small class="form-text text-muted">Chấp nhận file .jpg, .png, .gif có kích thước tối đa 2MB</small>
                </div>
                
                <div class="form-group">
                  <label for="notes">Ghi chú</label>
                  <textarea class="form-control" id="notes" name="notes" rows="4"><?php echo isset($data['notes']) ? $data['notes'] : ''; ?></textarea>
                </div>
                
                <div class="form-group">
                  <div class="form-check form-check-flat form-check-primary">
                    <label class="form-check-label">
                      <input type="checkbox" class="form-check-input" id="is_recurring" name="is_recurring" value="1" 
                             <?php echo (isset($data['is_recurring']) && $data['is_recurring'] == 1) ? 'checked' : ''; ?>>
                      Đây là khoản chi tiêu định kỳ
                    </label>
                  </div>
                </div>
                
                <div id="recurring_options" style="display: <?php echo (isset($data['is_recurring']) && $data['is_recurring'] == 1) ? 'block' : 'none'; ?>;">
                  <div class="form-group">
                    <label for="recurring_frequency">Tần suất</label>
                    <select class="form-control" id="recurring_frequency" name="recurring_frequency">
                      <option value="weekly" <?php echo (isset($data['recurring_frequency']) && $data['recurring_frequency'] == 'weekly') ? 'selected' : ''; ?>>Hàng tuần</option>
                      <option value="monthly" <?php echo (isset($data['recurring_frequency']) && $data['recurring_frequency'] == 'monthly') ? 'selected' : ''; ?>>Hàng tháng</option>
                      <option value="yearly" <?php echo (isset($data['recurring_frequency']) && $data['recurring_frequency'] == 'yearly') ? 'selected' : ''; ?>>Hàng năm</option>
                    </select>
                  </div>
                  
                  <div class="form-group">
                    <label for="recurring_end_date">Ngày kết thúc</label>
                    <input type="date" class="form-control" id="recurring_end_date" name="recurring_end_date" 
                           value="<?php echo isset($data['recurring_end_date']) ? $data['recurring_end_date'] : ''; ?>">
                    <small class="form-text text-muted">Để trống nếu khoản chi tiêu lặp lại vô thời hạn</small>
                  </div>
                </div>
                
                <div class="form-group mt-4">
                  <button type="submit" class="btn btn-primary mr-2">
                    <i class="mdi mdi-content-save"></i> Lưu chi tiêu
                  </button>
                  <a href="<?php echo BASEURL; ?>/expenses" class="btn btn-light">
                    <i class="mdi mdi-cancel"></i> Hủy bỏ
                  </a>
                </div>
              </form>
            </div>
          </div>
        </div>
        
        <div class="col-md-4 grid-margin">
          <div class="card">
            <div class="card-body">
              <h4 class="card-title">Thống kê chi tiêu</h4>
              <div class="d-flex flex-wrap mb-4">
                <div class="mr-4">
                  <p class="text-muted mb-1">Chi tiêu tháng này</p>
                  <h4 class="text-danger font-weight-bold"><?php echo number_format($data['stats']['budget_used'] ?? 0, 0, ',', '.'); ?>đ</h4>
                </div>
                <div>
                  <p class="text-muted mb-1">Ngân sách còn lại</p>
                  <h4 class="text-primary font-weight-bold"><?php echo number_format($data['stats']['budget_remaining'] ?? 0, 0, ',', '.'); ?>đ</h4>
                </div>
              </div>
              
              <div class="mt-4">
                <p class="text-muted mb-2">Tiến độ sử dụng ngân sách</p>
                <div class="progress mb-2" style="height: 8px;">
                  <div class="progress-bar bg-<?php echo $data['stats']['budget_percentage'] > 80 ? 'danger' : 'primary'; ?>" 
                      role="progressbar" 
                      style="width: <?php echo $data['stats']['budget_percentage'] ?? 0; ?>%;" 
                      aria-valuenow="<?php echo $data['stats']['budget_percentage'] ?? 0; ?>" 
                      aria-valuemin="0" 
                      aria-valuemax="100">
                  </div>
                </div>
                <p class="text-muted font-13"><?php echo $data['stats']['budget_percentage'] ?? 0; ?>% ngân sách đã sử dụng</p>
              </div>
            </div>
          </div>
          
          <div class="card mt-4">
            <div class="card-body">
              <h4 class="card-title">Mẹo tiết kiệm</h4>
              <div class="d-flex align-items-center mt-4">
                <i class="mdi mdi-lightbulb-on-outline text-warning icon-md"></i>
                <div class="ml-3">
                  <p class="mb-0">Nên phân loại chi tiêu để dễ dàng theo dõi và cân đối ngân sách.</p>
                </div>
              </div>
              <div class="d-flex align-items-center mt-4">
                <i class="mdi mdi-lightbulb-on-outline text-warning icon-md"></i>
                <div class="ml-3">
                  <p class="mb-0">Tuân thủ nguyên tắc 50/30/20: 50% cho nhu cầu thiết yếu, 30% cho mong muốn, 20% để tiết kiệm.</p>
                </div>
              </div>
              <div class="d-flex align-items-center mt-4">
                <i class="mdi mdi-lightbulb-on-outline text-warning icon-md"></i>
                <div class="ml-3">
                  <p class="mb-0">Theo dõi các khoản chi tiêu định kỳ để không bỏ sót khoản thanh toán quan trọng.</p>
                </div>
              </div>
            </div>
          </div>

          <div class="card bg-light mb-4">
            <div class="card-body">
                <h5 class="card-title text-primary">Thông tin ngân sách</h5>
                <div class="row">
                    <div class="col-md-6">
                        <p class="mb-1">Ngân sách còn lại</p>
                        <h4 class="font-weight-bold"><?php echo number_format($data['stats']['budget_remaining'] ?? 0, 0, ',', '.'); ?>đ</h4>
                    </div>
                    <div class="col-md-6">
                        <p class="mb-1">Tiến độ sử dụng ngân sách</p>
                        <h4 class="font-weight-bold"><?php echo number_format($data['stats']['budget_used'] ?? 0, 0, ',', '.'); ?>đ</h4>
                    </div>
                </div>
                <div class="progress mt-3" style="height: 10px;">
                    <div class="progress-bar bg-primary" role="progressbar" 
                         style="width: <?php echo $data['stats']['budget_percentage'] ?? 0; ?>%;" 
                         aria-valuenow="<?php echo $data['stats']['budget_percentage'] ?? 0; ?>" 
                         aria-valuemin="0" aria-valuemax="100">
                    </div>
                </div>
                <small class="text-muted mt-2 d-block"><?php echo $data['stats']['budget_percentage'] ?? 0; ?>% ngân sách đã sử dụng</small>
            </div>
          </div>

          <?php if (ENVIRONMENT === 'development'): ?>
          <div class="alert alert-info">
            <h5>Debug: $data['stats']</h5>
            <pre><?php print_r($data['stats'] ?? 'Not set'); ?></pre>
          </div>
          <?php endif; ?>

          <!-- Debug thông tin để tìm lỗi - chỉ hiển thị trong môi trường development -->
          <?php if (defined('ENVIRONMENT') && ENVIRONMENT === 'development'): ?>
          <div class="alert alert-info mt-4">
            <h5>Debug: Bank Accounts</h5>
            <pre><?php 
              if(isset($data['bank_accounts'])) {
                echo "Count: " . count($data['bank_accounts']) . "\n";
                if(!empty($data['bank_accounts'])) {
                  $firstItem = $data['bank_accounts'][0];
                  echo "First item properties: \n";
                  if(is_object($firstItem)) {
                    print_r(get_object_vars($firstItem));
                  } else {
                    echo "First item is not an object: ";
                    var_export($firstItem);
                  }
                }
              } else {
                echo "bank_accounts not set";
              }
            ?></pre>
            
            <h5>Debug: Cards</h5>
            <pre><?php 
              if(isset($data['cards'])) {
                echo "Count: " . count($data['cards']) . "\n";
                if(!empty($data['cards'])) {
                  $firstItem = $data['cards'][0];
                  echo "First item properties: \n";
                  if(is_object($firstItem)) {
                    print_r(get_object_vars($firstItem));
                  } else {
                    echo "First item is not an object: ";
                    var_export($firstItem);
                  }
                }
              } else {
                echo "cards not set";
              }
            ?></pre>
          </div>
          <?php endif; ?>
        </div>
      </div>
    </div>
    
    <?php require APPROOT . '/views/layouts/footer.php'; ?>
  </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
  // Xử lý hiển thị phương thức thanh toán
  const paymentMethod = document.getElementById('payment_method');
  const bankInfo = document.getElementById('bank_info');
  const cardInfo = document.getElementById('card_info');
  
  paymentMethod.addEventListener('change', function() {
    bankInfo.style.display = this.value === 'bank' ? 'block' : 'none';
    cardInfo.style.display = this.value === 'card' ? 'block' : 'none';
  });
  
  // Xử lý hiển thị chi tiêu định kỳ
  const isRecurring = document.getElementById('is_recurring');
  const recurringOptions = document.getElementById('recurring_options');
  
  isRecurring.addEventListener('change', function() {
    recurringOptions.style.display = this.checked ? 'block' : 'none';
  });
  
  // Hiển thị biểu đồ chi tiêu theo danh mục
  if (document.getElementById('categoryExpensesChart')) {
    const categoryData = <?php echo json_encode($data['charts']['by_category'] ?? []); ?>;
    
    if (categoryData && categoryData.labels && categoryData.labels.length > 0) {
      const categoryCtx = document.getElementById('categoryExpensesChart').getContext('2d');
      new Chart(categoryCtx, {
        type: 'doughnut',
        data: {
          labels: categoryData.labels,
          datasets: [{
            data: categoryData.values,
            backgroundColor: categoryData.colors,
            borderWidth: 1
          }]
        },
        options: {
          responsive: true,
          maintainAspectRatio: false,
          legend: {
            position: 'bottom',
            labels: {
              boxWidth: 12
            }
          }
        }
      });
    }
  }
  
  // Định dạng số tiền theo chuẩn VNĐ khi nhập
  const amountInput = document.getElementById('amount');
  amountInput.addEventListener('change', function() {
    if (this.value < 0) {
      this.value = Math.abs(this.value);
    }
  });
});
</script>

<?php require APPROOT . '/views/layouts/footer_scripts.php'; ?>