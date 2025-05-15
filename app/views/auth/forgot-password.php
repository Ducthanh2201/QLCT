<?php
// filepath: c:\xampp\htdocs\ADM-QLCT\expense-management\app\views\auth\forgot-password.php
?>
<!DOCTYPE html>
<html lang="vi">
<head>
  <!-- Required meta tags -->
  <meta charset="utf-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no" />
  <title><?php echo SITENAME; ?> - Quên mật khẩu</title>
  <!-- plugins:css -->
  <link rel="stylesheet" href="<?php echo BASEURL; ?>/assets/vendors/mdi/css/materialdesignicons.min.css">
  <link rel="stylesheet" href="<?php echo BASEURL; ?>/assets/vendors/flag-icon-css/css/flag-icon.min.css">
  <link rel="stylesheet" href="<?php echo BASEURL; ?>/assets/vendors/css/vendor.bundle.base.css">
  <!-- Layout styles -->
  <link rel="stylesheet" href="<?php echo BASEURL; ?>/assets/css/demo_1/style.css" />
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
              <h4>Quên mật khẩu?</h4>
              <h6 class="font-weight-light">Nhập email của bạn để đặt lại mật khẩu</h6>
              
              <?php require APPROOT . '/views/partials/_message.php'; ?>
              
              <form class="pt-3" action="<?php echo BASEURL; ?>/auth/forgot-password" method="POST">
                <div class="form-group">
                  <input type="email" class="form-control form-control-lg" id="email" name="email" 
                         placeholder="Email" required>
                </div>
                <div class="mt-3">
                  <button type="submit" class="btn btn-block btn-primary btn-lg font-weight-medium auth-form-btn">
                    GỬI LIÊN KẾT ĐẶT LẠI MẬT KHẨU
                  </button>
                </div>
                <div class="text-center mt-4 font-weight-light">
                  <a href="<?php echo BASEURL; ?>/auth/login" class="text-primary">Quay lại đăng nhập</a>
                </div>
              </form>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
  
  <!-- plugins:js -->
  <script src="<?php echo BASEURL; ?>/assets/vendors/js/vendor.bundle.base.js"></script>
  <!-- endinject -->
</body>
</html>