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
        <h3 class="page-title">Thêm tài khoản ngân hàng</h3>
        <nav aria-label="breadcrumb">
          <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="<?php echo BASEURL; ?>/dashboard">Dashboard</a></li>
            <li class="breadcrumb-item"><a href="<?php echo BASEURL; ?>/payment-methods">Phương thức thanh toán</a></li>
            <li class="breadcrumb-item active" aria-current="page">Thêm tài khoản</li>
          </ol>
        </nav>
      </div>
      
      <div class="row">
        <div class="col-md-8 grid-margin stretch-card">
          <div class="card">
            <div class="card-body">
              <h4 class="card-title">Thông tin tài khoản ngân hàng</h4>
              <p class="card-description">Nhập đầy đủ thông tin tài khoản ngân hàng</p>
              
              <form class="forms-sample" method="post" action="<?php echo BASEURL; ?>/payment-methods/add-bank-account">
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
                  <label for="account_number">Số tài khoản <span class="text-danger">*</span></label>
                  <input type="text" class="form-control" id="account_number" name="account_number" 
                         value="<?php echo $data['account_number']; ?>" 
                         placeholder="Nhập số tài khoản ngân hàng" required>
                  <?php if(isset($data['errors']['account_number'])): ?>
                    <small class="text-danger"><?php echo $data['errors']['account_number']; ?></small>
                  <?php endif; ?>
                </div>
                
                <div class="form-group">
                  <label for="account_holder">Chủ tài khoản <span class="text-danger">*</span></label>
                  <input type="text" class="form-control" id="account_holder" name="account_holder" 
                         value="<?php echo $data['account_holder']; ?>" 
                         placeholder="Nhập tên chủ tài khoản" required>
                  <?php if(isset($data['errors']['account_holder'])): ?>
                    <small class="text-danger"><?php echo $data['errors']['account_holder']; ?></small>
                  <?php endif; ?>
                </div>
                
                <div class="form-group">
                  <label for="branch">Chi nhánh</label>
                  <input type="text" class="form-control" id="branch" name="branch" 
                         value="<?php echo $data['branch']; ?>" 
                         placeholder="Nhập chi nhánh ngân hàng (không bắt buộc)">
                </div>
                
                <div class="form-group">
                  <div class="form-check form-check-flat form-check-primary">
                    <label class="form-check-label">
                      <input type="checkbox" class="form-check-input" id="is_default" name="is_default" 
                             <?php echo $data['is_default'] ? 'checked' : ''; ?>>
                      Đặt làm tài khoản mặc định
                    </label>
                  </div>
                </div>
                
                <div class="form-group mt-4">
                  <button type="submit" class="btn btn-primary mr-2">
                    <i class="mdi mdi-content-save"></i> Lưu tài khoản
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

<?php require APPROOT . '/views/layouts/footer_scripts.php'; ?>