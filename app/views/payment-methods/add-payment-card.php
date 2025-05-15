<?php
require APPROOT . '/views/layouts/header.php';
require APPROOT . '/views/layouts/sidebar.php';
?>

<div class="container-fluid page-body-wrapper">
  <?php require APPROOT . '/views/partials/_settings-panel.php'; ?>
  <?php require APPROOT . '/views/layouts/navbar.php'; ?>
  
  <div class="main-panel">
    <div class="content-wrapper">
      <?php require APPROOT . '/views/partials/_message.php'; ?>
      
      <div class="page-header">
        <h3 class="page-title">Thêm thẻ thanh toán</h3>
        <nav aria-label="breadcrumb">
          <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="<?php echo BASEURL; ?>/dashboard">Dashboard</a></li>
            <li class="breadcrumb-item"><a href="<?php echo BASEURL; ?>/payment-methods">Phương thức thanh toán</a></li>
            <li class="breadcrumb-item active" aria-current="page">Thêm thẻ</li>
          </ol>
        </nav>
      </div>
      
      <div class="row">
        <div class="col-md-8 grid-margin stretch-card">
          <div class="card">
            <div class="card-body">
              <h4 class="card-title">Thông tin thẻ thanh toán</h4>
              <p class="card-description">Nhập đầy đủ thông tin thẻ thanh toán</p>
              
              <form class="forms-sample" method="post" action="<?php echo BASEURL; ?>/payment-methods/add-payment-card">
                <?php if(isset($data['errors']['general'])): ?>
                  <div class="alert alert-danger"><?php echo $data['errors']['general']; ?></div>
                <?php endif; ?>
                
                <div class="form-group">
                  <label for="bank_name">Tên ngân hàng <span class="text-danger">*</span></label>
                  <input type="text" class="form-control" id="bank_name" name="bank_name" 
                         value="<?php echo $data['bank_name']; ?>" 
                         placeholder="Nhập tên ngân hàng (VD: Vietcombank, BIDV)" required>
                  <?php if(isset($data['errors']['bank_name'])): ?>
                    <small class="text-danger"><?php echo $data['errors']['bank_name']; ?></small>
                  <?php endif; ?>
                </div>
                
                <div class="form-group">
                  <label for="card_number">Số thẻ <span class="text-danger">*</span></label>
                  <input type="text" class="form-control" id="card_number" name="card_number" 
                         value="<?php echo $data['card_number']; ?>" 
                         placeholder="Nhập số thẻ" required>
                  <?php if(isset($data['errors']['card_number'])): ?>
                    <small class="text-danger"><?php echo $data['errors']['card_number']; ?></small>
                  <?php endif; ?>
                </div>
                
                <div class="form-group">
                  <label for="card_holder">Chủ thẻ <span class="text-danger">*</span></label>
                  <input type="text" class="form-control" id="card_holder" name="card_holder" 
                         value="<?php echo $data['card_holder']; ?>" 
                         placeholder="Nhập tên chủ thẻ" required>
                  <?php if(isset($data['errors']['card_holder'])): ?>
                    <small class="text-danger"><?php echo $data['errors']['card_holder']; ?></small>
                  <?php endif; ?>
                </div>
                
                <div class="form-group">
                  <label for="expiry_date">Ngày hết hạn <span class="text-danger">*</span></label>
                  <input type="text" class="form-control" id="expiry_date" name="expiry_date" 
                         value="<?php echo $data['expiry_date']; ?>" 
                         placeholder="MM/YY (VD: 12/25)" required>
                  <?php if(isset($data['errors']['expiry_date'])): ?>
                    <small class="text-danger"><?php echo $data['errors']['expiry_date']; ?></small>
                  <?php endif; ?>
                </div>
                
                <div class="form-group">
                  <label for="card_type">Loại thẻ <span class="text-danger">*</span></label>
                  <select class="form-control" id="card_type" name="card_type" required>
                    <option value="">-- Chọn loại thẻ --</option>
                    <option value="Visa" <?php echo $data['card_type'] == 'Visa' ? 'selected' : ''; ?>>Visa</option>
                    <option value="MasterCard" <?php echo $data['card_type'] == 'MasterCard' ? 'selected' : ''; ?>>MasterCard</option>
                    <option value="JCB" <?php echo $data['card_type'] == 'JCB' ? 'selected' : ''; ?>>JCB</option>
                    <option value="ATM" <?php echo $data['card_type'] == 'ATM' ? 'selected' : ''; ?>>Thẻ ATM nội địa</option>
                    <option value="Other" <?php echo $data['card_type'] == 'Other' ? 'selected' : ''; ?>>Khác</option>
                  </select>
                  <?php if(isset($data['errors']['card_type'])): ?>
                    <small class="text-danger"><?php echo $data['errors']['card_type']; ?></small>
                  <?php endif; ?>
                </div>
                
                <div class="form-group">
                  <div class="form-check form-check-flat form-check-primary">
                    <label class="form-check-label">
                      <input type="checkbox" class="form-check-input" id="is_default" name="is_default" 
                             <?php echo $data['is_default'] ? 'checked' : ''; ?>>
                      Đặt làm thẻ mặc định
                    </label>
                  </div>
                </div>
                
                <div class="form-group mt-4">
                  <button type="submit" class="btn btn-primary mr-2">
                    <i class="mdi mdi-content-save"></i> Lưu thẻ
                  </button>
                  <a href="<?php echo BASEURL; ?>/payment-methods" class="btn btn-light">
                    <i class="mdi mdi-cancel"></i> Hủy bỏ
                  </a>
                </div>
              </form>
            </div>
          </div>
        </div>
      </div>
    </div>
    
    <?php require APPROOT . '/views/layouts/footer.php'; ?>
  </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
  // Định dạng ngày hết hạn thẻ
  const expiryDateInput = document.getElementById('expiry_date');
  
  expiryDateInput.addEventListener('input', function(e) {
    let value = e.target.value.replace(/\D/g, '');
    
    if (value.length > 2) {
      value = value.substring(0, 2) + '/' + value.substring(2, 4);
    }
    
    e.target.value = value;
  });
});
</script>

<?php require APPROOT . '/views/layouts/footer_scripts.php'; ?>