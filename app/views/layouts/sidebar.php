<nav class="sidebar sidebar-offcanvas" id="sidebar">
  <ul class="nav">
    <li class="nav-item nav-profile border-bottom">
      <a href="<?php echo BASEURL; ?>/profile" class="nav-link flex-column">
        <div class="nav-profile-image">
          <img src="<?php echo BASEURL; ?>/assets/images/faces/face1.jpg" alt="profile">
        </div>
        <div class="nav-profile-text d-flex flex-column mt-3">
          <span class="font-weight-bold mb-2"><?php echo $_SESSION['username'] ?? 'Người dùng'; ?></span>
          <span class="text-secondary text-small"><?php echo $_SESSION['email'] ?? ''; ?></span>
        </div>
      </a>
    </li>
    <li class="nav-item pt-3">
      <a class="nav-link d-block" href="<?php echo BASEURL; ?>/dashboard">
        <img class="sidebar-brand-logo" src="<?php echo BASEURL; ?>/assets/images/logo.png" alt="" height="40">
        <h4>Quản lý chi tiêu</h4>
      </a>
    </li>
    <li class="pt-2 pb-1">
      <span class="nav-item-head">Trang quản trị</span>
    </li>
    <li class="nav-item">
      <a class="nav-link" href="<?php echo BASEURL; ?>/dashboard">
        <i class="mdi mdi-view-dashboard menu-icon"></i>
        <span class="menu-title">Tổng quan</span>
      </a>
    </li>
    <li class="nav-item">
      <a class="nav-link" data-toggle="collapse" href="#ui-expense" aria-expanded="false" aria-controls="ui-expense">
        <i class="mdi mdi-cash-multiple menu-icon"></i>
        <span class="menu-title">Quản lý chi tiêu</span>
        <i class="menu-arrow"></i>
      </a>
      <div class="collapse" id="ui-expense">
        <ul class="nav flex-column sub-menu">
          <li class="nav-item">
            <a class="nav-link" href="<?php echo BASEURL; ?>/expenses">Danh sách chi tiêu</a>
          </li>
          <li class="nav-item">
            <a class="nav-link" href="<?php echo BASEURL; ?>/expenses/create">Thêm chi tiêu</a>
          </li>
          <li class="nav-item">
            <a class="nav-link" href="<?php echo BASEURL; ?>/expense-categories">Danh mục chi tiêu</a>
          </li>
        </ul>
      </div>
    </li>
    <li class="nav-item">
      <a class="nav-link" data-toggle="collapse" href="#ui-income" aria-expanded="false" aria-controls="ui-income">
      <i class="mdi mdi-currency-usd menu-icon"></i>
      <span class="menu-title">Quản lý thu nhập</span>
        <i class="menu-arrow"></i>
      </a>
      <div class="collapse" id="ui-income">
        <ul class="nav flex-column sub-menu">
          <li class="nav-item">
            <a class="nav-link" href="<?php echo BASEURL; ?>/incomes">Danh sách thu nhập</a>
          </li>
          <li class="nav-item">
            <a class="nav-link" href="<?php echo BASEURL; ?>/incomes/create">Thêm thu nhập</a>
          </li>
          <li class="nav-item">
            <a class="nav-link" href="<?php echo BASEURL; ?>/income-categories">Danh mục thu nhập</a>
          </li>
        </ul>
      </div>
    </li>
    <li class="nav-item">
      <a class="nav-link" href="<?php echo BASEURL; ?>/reports">
        <i class="mdi mdi-chart-bar menu-icon"></i>
        <span class="menu-title">Báo cáo thống kê</span>
      </a>
    </li>
    <li class="nav-item">
      <a class="nav-link" href="<?php echo BASEURL; ?>/budget">
        <i class="mdi mdi-wallet menu-icon"></i>
        <span class="menu-title">Ngân sách</span>
      </a>
    </li>
    <li class="nav-item">
      <a class="nav-link" href="<?php echo BASEURL; ?>/profile">
        <i class="mdi mdi-account menu-icon"></i>
        <span class="menu-title">Thông tin cá nhân</span>
      </a>
    </li>
    <li class="nav-item">
      <a class="nav-link" href="<?php echo BASEURL; ?>/payment-methods">
        <i class="mdi mdi-credit-card menu-icon"></i>
        <span class="menu-title">Phương thức thanh toán</span>
      </a>
    </li>
    <?php if(isset($_SESSION['is_admin']) && $_SESSION['is_admin']): ?>
    <li class="nav-item">
      <a class="nav-link" href="<?php echo BASEURL; ?>/admin">
        <i class="mdi mdi-security menu-icon"></i>
        <span class="menu-title">Quản trị hệ thống</span>
      </a>
    </li>
    <?php endif; ?>
    <li class="nav-item">
      <a class="nav-link" href="<?php echo BASEURL; ?>/settings">
        <i class="mdi mdi-settings menu-icon"></i>
        <span class="menu-title">Cài đặt</span>
      </a>
    </li>
    <li class="nav-item pt-3">
      <a class="nav-link" href="<?php echo BASEURL; ?>/auth/logout">
        <i class="mdi mdi-logout-variant menu-icon"></i>
        <span class="menu-title">Đăng xuất</span>
      </a>
    </li>
  </ul>
</nav>