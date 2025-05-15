<?php
// filepath: c:\xampp\htdocs\ADM-QLCT\expense-management\app\views\auth\register.php
?>
<!DOCTYPE html>
<html lang="vi">
<head>
  <!-- Required meta tags -->
  <meta charset="utf-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no" />
  <title><?php echo SITENAME; ?> - Đăng ký</title>
  <!-- plugins:css -->
  <link rel="stylesheet" href="<?php echo BASEURL; ?>/assets/vendors/mdi/css/materialdesignicons.min.css">
  <link rel="stylesheet" href="<?php echo BASEURL; ?>/assets/vendors/flag-icon-css/css/flag-icon.min.css">
  <link rel="stylesheet" href="<?php echo BASEURL; ?>/assets/vendors/css/vendor.bundle.base.css">
  <!-- endinject -->
  <!-- Layout styles -->
  <link rel="stylesheet" href="<?php echo BASEURL; ?>/assets/css/demo_1/style.css" />
  <!-- End layout styles -->
  <link rel="shortcut icon" href="<?php echo BASEURL; ?>/assets/images/favicon.png" />
  
  <!-- Thêm vào phần <head> của file -->
  <style>
    #password-strength-indicator .badge {
      font-size: 80%;
      padding: 0.25em 0.5em;
      min-width: 70px;
      text-align: center;
    }
    
    .form-group .d-flex {
      min-height: 24px; /* Đảm bảo chiều cao cố định */
    }
    
    .badge.text-danger {
      background-color: rgba(220, 53, 69, 0.1);
      color: #dc3545;
      border: 1px solid rgba(220, 53, 69, 0.2);
    }
    
    .badge.text-warning {
      background-color: rgba(255, 193, 7, 0.1);
      color: #ffc107;
      border: 1px solid rgba(255, 193, 7, 0.2);
    }
    
    .badge.text-success {
      background-color: rgba(40, 167, 69, 0.1);
      color: #28a745;
      border: 1px solid rgba(40, 167, 69, 0.2);
    }
  </style>
</head>
<body>
  <div class="container-scroller">
    <div class="container-fluid page-body-wrapper full-page-wrapper">
      <div class="content-wrapper d-flex align-items-center auth">
        <div class="row flex-grow">
          <div class="col-lg-5 mx-auto">
            <div class="auth-form-light text-left p-5">
              <div class="brand-logo">
                <img src="<?php echo BASEURL; ?>/assets/images/logo.png" alt="logo">
              </div>
              <h4>Lần đầu đến với ứng dụng?</h4>
              <h6 class="font-weight-light">Đăng ký thật đơn giản. Chỉ mất vài giây</h6>
              
              <?php require APPROOT . '/views/partials/_message.php'; ?>
              
              <form class="pt-3" action="<?php echo BASEURL; ?>/auth/register" method="POST">
                <div class="form-group">
                  <input type="text" class="form-control form-control-lg" id="fullname" name="fullname" placeholder="Họ và tên"
                         value="<?php echo isset($data['fullname']) ? $data['fullname'] : ''; ?>" required>
                  <?php if(isset($data['errors']['fullname'])): ?>
                    <small class="text-danger"><?php echo $data['errors']['fullname']; ?></small>
                  <?php endif; ?>
                </div>
                
                <div class="form-group">
                  <input type="email" class="form-control form-control-lg" id="email" name="email" placeholder="Email"
                         value="<?php echo isset($data['email']) ? $data['email'] : ''; ?>" required>
                  <?php if(isset($data['errors']['email'])): ?>
                    <small class="text-danger"><?php echo $data['errors']['email']; ?></small>
                  <?php endif; ?>
                </div>
                
                <!-- Thay thế đoạn code form-group cho password như sau -->
                <div class="form-group">
                    <div class="input-group">
                        <input type="password" class="form-control form-control-lg" id="password" name="password" 
                            placeholder="Mật khẩu" required>
                        <div class="input-group-append">
                        <button class="btn btn-outline-secondary toggle-password" type="button">
                            <i class="mdi mdi-eye-outline"></i>
                        </button>
                        </div>
                    </div>
                    <div class="d-flex justify-content-between align-items-center mt-1">
                        <small class="form-text text-muted">Mật khẩu phải có ít nhất 8 ký tự, chữ hoa, thường và số</small>
                        <div id="password-strength-indicator" class="ml-2"></div>
                    </div>
                    <?php if(isset($data['errors']['password'])): ?>
                        <small class="text-danger"><?php echo $data['errors']['password']; ?></small>
                    <?php endif; ?>
                </div>
                
                <div class="form-group">
                  <div class="input-group">
                    <input type="password" class="form-control form-control-lg" id="confirm_password" name="confirm_password" 
                           placeholder="Xác nhận mật khẩu" required>
                    <div class="input-group-append">
                      <button class="btn btn-outline-secondary toggle-password" type="button">
                        <i class="mdi mdi-eye-outline"></i>
                      </button>
                    </div>
                  </div>
                  <?php if(isset($data['errors']['confirm_password'])): ?>
                    <small class="text-danger"><?php echo $data['errors']['confirm_password']; ?></small>
                  <?php endif; ?>
                </div>
                
                <div class="form-group">
                  <input type="tel" class="form-control form-control-lg" id="phone" name="phone" placeholder="Số điện thoại (không bắt buộc)"
                         value="<?php echo isset($data['phone']) ? $data['phone'] : ''; ?>">
                </div>
                
                <div class="form-group">
                  <label for="currency">Đơn vị tiền tệ</label>
                  <select class="form-control form-control-lg" id="currency" name="currency">
                    <option value="VND" <?php echo (!isset($data['currency']) || $data['currency'] == 'VND') ? 'selected' : ''; ?>>VND (Việt Nam Đồng)</option>
                    <option value="USD" <?php echo (isset($data['currency']) && $data['currency'] == 'USD') ? 'selected' : ''; ?>>USD (US Dollar)</option>
                    <option value="EUR" <?php echo (isset($data['currency']) && $data['currency'] == 'EUR') ? 'selected' : ''; ?>>EUR (Euro)</option>
                  </select>
                </div>
                
                <div class="mb-4">
                    <div class="form-check form-check-flat form-check-primary">
                        <label class="form-check-label">
                        <input type="checkbox" class="form-check-input" name="terms" 
                                <?php echo (isset($data['terms']) && $data['terms']) ? 'checked' : ''; ?> required>
                        Tôi đồng ý với tất cả các Điều khoản & Điều kiện
                        <i class="input-helper"></i>
                        </label>
                        <?php if(isset($data['errors']['terms'])): ?>
                        <small class="d-block text-danger"><?php echo $data['errors']['terms']; ?></small>
                        <?php endif; ?>
                    </div>
                </div>
                
                <div class="mt-3">
                  <button type="submit" class="btn btn-block btn-primary btn-lg font-weight-medium auth-form-btn">
                    ĐĂNG KÝ
                  </button>
                </div>
                
                <div class="text-center mt-4 font-weight-light">
                  Đã có tài khoản? <a href="<?php echo BASEURL; ?>/auth/login" class="text-primary">Đăng nhập</a>
                </div>
              </form>
            </div>
          </div>
        </div>
      </div>
      <!-- content-wrapper ends -->
    </div>
    <!-- page-body-wrapper ends -->
  </div>
  <!-- container-scroller -->
  
  <!-- plugins:js -->
  <script src="<?php echo BASEURL; ?>/assets/vendors/js/vendor.bundle.base.js"></script>
  <!-- endinject -->
  
  <script>
  document.addEventListener('DOMContentLoaded', function() {
    // Toggle password visibility
    const toggleButtons = document.querySelectorAll('.toggle-password');
    
    toggleButtons.forEach(button => {
      button.addEventListener('click', function() {
        const input = this.closest('.input-group').querySelector('input');
        const type = input.getAttribute('type') === 'password' ? 'text' : 'password';
        input.setAttribute('type', type);
        
        // Toggle icon
        const icon = this.querySelector('i');
        icon.classList.toggle('mdi-eye-outline');
        icon.classList.toggle('mdi-eye-off-outline');
      });
    });
    
    // Validate password strength
    const passwordInput = document.getElementById('password');
    const confirmInput = document.getElementById('confirm_password');
    const strengthIndicator = document.getElementById('password-strength-indicator');
    
    passwordInput.addEventListener('input', function() {
      validatePassword();
    });
    
    confirmInput.addEventListener('input', function() {
      validatePasswordMatch();
    });
    
    function validatePassword() {
      const password = passwordInput.value;
      let strength = 0;
      let message = '';
      let colorClass = '';
      
      if (password.length >= 8) strength += 1;
      if (password.match(/[A-Z]/)) strength += 1;
      if (password.match(/[a-z]/)) strength += 1;
      if (password.match(/[0-9]/)) strength += 1;
      
      switch (strength) {
        case 0:
        case 1:
          message = 'Yếu';
          colorClass = 'text-danger';
          break;
        case 2:
        case 3:
          message = 'Trung bình';
          colorClass = 'text-warning';
          break;
        case 4:
          message = 'Mạnh';
          colorClass = 'text-success';
          break;
      }
      
      if (password) {
        strengthIndicator.innerHTML = `<span class="badge ${colorClass}">${message}</span>`;
      } else {
        strengthIndicator.innerHTML = '';
      }
    }
    
    function validatePasswordMatch() {
      const password = passwordInput.value;
      const confirmPassword = confirmInput.value;
      
      if (confirmPassword && password !== confirmPassword) {
        confirmInput.setCustomValidity("Mật khẩu không khớp");
      } else {
        confirmInput.setCustomValidity("");
      }
    }
  });
  </script>
</body>
</html>