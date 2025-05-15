<?php
require_once APPROOT . '/views/layouts/header.php';
require_once APPROOT . '/views/layouts/sidebar.php';
?>

<div class="container-fluid page-body-wrapper">
  <?php require_once APPROOT . '/views/partials/_settings-panel.php'; ?>
  <?php require_once APPROOT . '/views/layouts/navbar.php'; ?>
  
  <div class="main-panel">
    <div class="content-wrapper">
      <?php require_once APPROOT . '/views/partials/_message.php'; ?>
      
      <div class="page-header">
        <h3 class="page-title">Chỉnh sửa khoản chi tiêu</h3>
        <nav aria-label="breadcrumb">
          <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="<?php echo BASEURL; ?>/dashboard">Dashboard</a></li>
            <li class="breadcrumb-item"><a href="<?php echo BASEURL; ?>/expenses">Danh sách chi tiêu</a></li>
            <li class="breadcrumb-item active" aria-current="page">Chỉnh sửa</li>
          </ol>
        </nav>
      </div>
      
      <div class="row">
        <div class="col-md-8 grid-margin stretch-card">
          <div class="card">
            <div class="card-body">
              <h4 class="card-title">Form chỉnh sửa chi tiêu</h4>
              
              <?php if(isset($expense) && !empty($expense)): ?>
                <form class="forms-sample" method="post" action="<?php echo BASEURL; ?>/expenses/update/<?php echo $expense->expense_id ?? ($expense->id ?? ''); ?>" enctype="multipart/form-data">
                  <input type="hidden" name="expense_id" value="<?php echo $expense->expense_id ?? ($expense->id ?? ''); ?>">
                  
                  <div class="form-group">
                    <label for="category_id">Loại chi tiêu <span class="text-danger">*</span></label>
                    <select class="form-control" id="category_id" name="category_id" required>
                      <option value="">-- Chọn danh mục --</option>
                      
                      <?php
                      // Kiểm tra biến $categories
                      if(isset($categories) && !empty($categories)): 
                        // Hiển thị danh mục từ database
                        foreach($categories as $category):
                          // Xác định ID của danh mục
                          $cat_id = null;
                          if (isset($category->id)) {
                            $cat_id = $category->id;
                          } elseif (isset($category->category_id)) {
                            $cat_id = $category->category_id;
                          }
                          
                          // Xác định tên danh mục
                          $cat_name = isset($category->name) ? $category->name : 
                                    (isset($category->category_name) ? $category->category_name : 'Không xác định');
                          
                          // Xác định ID danh mục hiện tại của chi tiêu
                          $current_cat_id = isset($expense->category_id) ? $expense->category_id : 0;
                          
                          // Xác định xem danh mục hiện tại có được chọn không
                          $selected = ($cat_id == $current_cat_id) ? 'selected' : '';
                          
                          // Chỉ hiển thị nếu có ID danh mục hợp lệ
                          if (!empty($cat_id)):
                      ?>
                        <option value="<?php echo $cat_id; ?>" <?php echo $selected; ?>>
                          <?php echo $cat_name; ?>
                        </option>
                      <?php 
                          endif;
                        endforeach;
                      else:
                        // Hiển thị một số danh mục cứng nếu không có từ database
                        $defaultCategories = [
                          ['id' => 1, 'name' => 'Ăn uống'],
                          ['id' => 2, 'name' => 'Di chuyển'],
                          ['id' => 3, 'name' => 'Mua sắm'],
                          ['id' => 4, 'name' => 'Giải trí'],
                          ['id' => 5, 'name' => 'Hóa đơn'],
                          ['id' => 6, 'name' => 'Sức khỏe'],
                          ['id' => 7, 'name' => 'Khác']
                        ];
                        
                        $current_cat_id = isset($expense->category_id) ? $expense->category_id : 0;
                        
                        foreach($defaultCategories as $cat):
                          $selected = ($cat['id'] == $current_cat_id) ? 'selected' : '';
                      ?>
                        <option value="<?php echo $cat['id']; ?>" <?php echo $selected; ?>>
                          <?php echo $cat['name']; ?>
                        </option>
                      <?php
                        endforeach;
                      endif;
                      ?>
                    </select>
                  </div>
                  
                  <div class="form-group">
                    <label for="amount">Số tiền <span class="text-danger">*</span></label>
                    <input type="number" class="form-control" id="amount" name="amount" value="<?php echo $expense->amount ?? ''; ?>" required>
                  </div>
                  
                  <div class="form-group">
                    <label for="expense_date">Ngày chi tiêu <span class="text-danger">*</span></label>
                    <input type="date" class="form-control" id="expense_date" name="expense_date" value="<?php echo isset($expense->expense_date) ? date('Y-m-d', strtotime($expense->expense_date)) : ''; ?>" required>
                  </div>
                  
                  <div class="form-group">
                    <label for="description">Mô tả</label>
                    <textarea class="form-control" id="description" name="description" rows="4"><?php echo $expense->description ?? ''; ?></textarea>
                  </div>
                  
                  <div class="form-group">
                    <label for="status_id">Trạng thái</label>
                    <select class="form-control" id="status_id" name="status_id">
                      <?php if(isset($statuses) && !empty($statuses)): ?>
                        <?php foreach($statuses as $status): ?>
                          <?php 
                          // Đảm bảo các thuộc tính tồn tại để tránh lỗi undefined property
                          $statusId = isset($status->status_id) ? $status->status_id : (isset($status->id) ? $status->id : 0);
                          $statusName = isset($status->status_name) ? $status->status_name : (isset($status->name) ? $status->name : 'Không xác định');
                          $description = isset($status->description) ? ' - ' . $status->description : '';
                          
                          // Debug để kiểm tra giá trị
                          error_log("Status ID: $statusId, Expense status_id: " . ($expense->status_id ?? 'null'));
                          
                          // So sánh với expense->status_id
                          $selected = ($statusId == ($expense->status_id ?? 0)) ? 'selected' : '';
                          ?>
                          <option value="<?php echo $statusId; ?>" <?php echo $selected; ?>>
                            <?php echo $statusName . $description; ?>
                          </option>
                        <?php endforeach; ?>
                      <?php else: ?>
                        <option value="1" <?php echo ($expense->status_id == 1) ? 'selected' : ''; ?>>Chờ duyệt</option>
                        <option value="2" <?php echo ($expense->status_id == 2) ? 'selected' : ''; ?>>Đã duyệt</option>
                        <option value="3" <?php echo ($expense->status_id == 3) ? 'selected' : ''; ?>>Bị từ chối</option>
                      <?php endif; ?>
                    </select>
                  </div>
                  
                  <div class="form-group">
                    <label for="receipt_image">Hình ảnh hóa đơn</label>
                    
                    <?php if(isset($expense->receipt_url) && !empty($expense->receipt_url)): ?>
                      <div class="mb-3">
                        <img src="<?php echo BASEURL . '/' . $expense->receipt_url; ?>" class="img-thumbnail" style="max-width: 200px;">
                        <div class="small text-muted mt-1">Hình ảnh hiện tại</div>
                      </div>
                    <?php endif; ?>
                    
                    <input type="file" class="form-control-file" id="receipt_image" name="receipt_image">
                    <small class="form-text text-muted">Để trống nếu không muốn thay đổi hình ảnh</small>
                  </div>
                  
                  <div class="mt-4">
                    <button type="submit" class="btn btn-primary mr-2">
                      <i class="mdi mdi-content-save"></i> Lưu thay đổi
                    </button>
                    <a href="<?php echo BASEURL; ?>/expenses/detail/<?php echo $expense->expense_id ?? ($expense->id ?? ''); ?>" class="btn btn-light">
                      <i class="mdi mdi-close"></i> Hủy bỏ
                    </a>
                  </div>
                </form>
              <?php else: ?>
                <div class="alert alert-danger">
                  <i class="mdi mdi-alert-circle-outline mr-2"></i>
                  Không tìm thấy thông tin chi tiêu
                </div>
                <a href="<?php echo BASEURL; ?>/expenses" class="btn btn-primary">
                  <i class="mdi mdi-arrow-left"></i> Quay lại danh sách
                </a>
              <?php endif; ?>
            </div>
          </div>
        </div>
        
        <div class="col-md-4 grid-margin stretch-card">
          <div class="card">
            <div class="card-body">
              <h4 class="card-title">Thông tin bổ sung</h4>
              
              <div class="alert alert-info">
                <i class="mdi mdi-information-outline mr-2"></i>
                Chỉnh sửa thông tin chi tiêu của bạn. Các trường được đánh dấu <span class="text-danger">*</span> là bắt buộc.
              </div>
              
              <div class="tips mt-4">
                <h5>Các gợi ý:</h5>
                <ul class="pl-3">
                  <li>Hãy chọn đúng danh mục để dễ dàng theo dõi chi tiêu</li>
                  <li>Mô tả chi tiết để dễ nhớ khoản chi tiêu này</li>
                  <li>Đính kèm hình ảnh hóa đơn để lưu lại bằng chứng</li>
                </ul>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
    
    <?php require_once APPROOT . '/views/layouts/footer.php'; ?>
  </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
  console.log("edit form loaded successfully");
  
  // Format số tiền khi nhập
  const amountInput = document.getElementById('amount');
  if (amountInput) {
    amountInput.addEventListener('blur', function() {
      const value = this.value.replace(/,/g, '');
      if (!isNaN(value) && value.trim() !== '') {
        this.value = parseFloat(value).toFixed(0);
      }
    });
  }
});
</script>

<?php require_once APPROOT . '/views/layouts/footer_scripts.php'; ?>