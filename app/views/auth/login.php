<?php
// filepath: c:\xampp\htdocs\ADM-QLCT\expense-management\app\views\auth\login.php
?>
<!DOCTYPE html>
<html lang="vi">
<head>
  <!-- Required meta tags -->
  <meta charset="utf-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no" />
  <title><?php echo SITENAME; ?> - Đăng nhập</title>
  <!-- plugins:css -->
  <link rel="stylesheet" href="<?php echo BASEURL; ?>/assets/vendors/mdi/css/materialdesignicons.min.css">
  <link rel="stylesheet" href="<?php echo BASEURL; ?>/assets/vendors/flag-icon-css/css/flag-icon.min.css">
  <link rel="stylesheet" href="<?php echo BASEURL; ?>/assets/vendors/css/vendor.bundle.base.css">
  <!-- endinject -->
  <!-- Layout styles -->
  <link rel="stylesheet" href="<?php echo BASEURL; ?>/assets/css/demo_1/style.css" />
  <!-- End layout styles -->
  <link rel="shortcut icon" href="<?php echo BASEURL; ?>/assets/images/favicon.png" />
</head>
<body>
  <div class="container-scroller">
    <div class="container-fluid page-body-wrapper full-page-wrapper">
      <div class="content-wrapper d-flex align-items-center auth">
        <div class="row flex-grow">
          <div class="col-lg-4 mx-auto">
            <div class="auth-form-light text-left p-5">
              <div class="brand-logo">
                <img src="<?php echo BASEURL; ?>/assets/images/logo.png" alt="logo">
              </div>
              <h4>Xin chào! Hãy bắt đầu</h4>
              <h6 class="font-weight-light">Đăng nhập để tiếp tục.</h6>
              
              <?php require APPROOT . '/views/partials/_message.php'; ?>
              
              <form class="pt-3" action="<?php echo BASEURL; ?>/auth/login" method="POST">
                <div class="form-group">
                  <input type="email" class="form-control form-control-lg" id="email" name="email" 
                         placeholder="Email" value="<?php echo isset($data['email']) ? $data['email'] : ''; ?>" required>
                </div>
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
                </div>
                <div class="mt-3">
                  <button type="submit" class="btn btn-block btn-primary btn-lg font-weight-medium auth-form-btn">
                    ĐĂNG NHẬP
                  </button>
                </div>
                <div class="my-2 d-flex justify-content-between align-items-center">
                    <div class="form-check form-check-flat form-check-primary">
                        <label class="form-check-label">
                        <input type="checkbox" name="remember" class="form-check-input">
                        Ghi nhớ đăng nhập
                        <i class="input-helper"></i>
                        </label>
                    </div>
                    <a href="<?php echo BASEURL; ?>/auth/forgot-password" class="auth-link text-black">Quên mật khẩu?</a>
                </div>
                <div class="text-center mt-4 font-weight-light">
                  Chưa có tài khoản? <a href="<?php echo BASEURL; ?>/auth/register" class="text-primary">Đăng ký</a>
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
    // Đọc tham số URL để kiểm tra nếu được chuyển hướng từ trang đăng ký
    const urlParams = new URLSearchParams(window.location.search);
    const registeredEmail = urlParams.get('registered_email');
    
    if (registeredEmail) {
        // Điền email vào form đăng nhập
        document.getElementById('email').value = registeredEmail;
        // Đặt focus vào trường mật khẩu
        document.getElementById('password').focus();
    }
    
    // Toggle password visibility
    const togglePassword = document.querySelector('.toggle-password');
    const password = document.querySelector('#password');
    
    togglePassword.addEventListener('click', function() {
        const type = password.getAttribute('type') === 'password' ? 'text' : 'password';
        password.setAttribute('type', type);
        
        // Toggle icon
        const icon = this.querySelector('i');
        icon.classList.toggle('mdi-eye-outline');
        icon.classList.toggle('mdi-eye-off-outline');
    });
  });
  </script>
</body>
</html>