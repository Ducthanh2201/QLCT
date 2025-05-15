<nav class="navbar default-layout-navbar col-lg-12 col-12 p-0 fixed-top d-flex flex-row">
  <div class="navbar-menu-wrapper d-flex align-items-stretch">
    <button class="navbar-toggler navbar-toggler align-self-center" type="button" data-toggle="minimize">
      <span class="mdi mdi-chevron-double-left"></span>
    </button>
    <div class="text-center navbar-brand-wrapper d-flex align-items-center justify-content-center">
      <a class="navbar-brand brand-logo-mini" href="<?php echo BASEURL; ?>/dashboard">
        <img src="<?php echo BASEURL; ?>/assets/images/logo-mini.svg" alt="logo" />
      </a>
    </div>
    <ul class="navbar-nav">
      <li class="nav-item dropdown">
        <a class="nav-link" id="messageDropdown" href="#" data-toggle="dropdown" aria-expanded="false">
          <i class="mdi mdi-email-outline"></i>
          <?php if(isset($data['unread_messages']) && $data['unread_messages'] > 0): ?>
            <span class="count-symbol bg-warning"></span>
          <?php endif; ?>
        </a>
        <div class="dropdown-menu dropdown-menu-left navbar-dropdown preview-list" aria-labelledby="messageDropdown">
          <h6 class="p-3 mb-0 font-weight-semibold">Thông báo</h6>
          <div class="dropdown-divider"></div>
          
          <?php if(isset($data['messages']) && !empty($data['messages'])): ?>
            <?php foreach($data['messages'] as $message): ?>
              <a class="dropdown-item preview-item">
                <div class="preview-thumbnail">
                  <img src="<?php echo BASEURL; ?>/assets/images/faces/face<?php echo rand(1, 9); ?>.jpg" alt="image" class="profile-pic">
                </div>
                <div class="preview-item-content d-flex align-items-start flex-column justify-content-center">
                  <h6 class="preview-subject ellipsis mb-1 font-weight-normal"><?php echo $message['title']; ?></h6>
                  <p class="text-gray mb-0"><?php echo $message['time']; ?></p>
                </div>
              </a>
              <div class="dropdown-divider"></div>
            <?php endforeach; ?>
            <h6 class="p-3 mb-0 text-center text-primary font-13">
              <a href="<?php echo BASEURL; ?>/notifications">Xem tất cả thông báo</a>
            </h6>
          <?php else: ?>
            <a class="dropdown-item preview-item">
              <div class="preview-item-content d-flex align-items-start flex-column justify-content-center">
                <h6 class="preview-subject ellipsis mb-1 font-weight-normal">Không có thông báo mới</h6>
              </div>
            </a>
          <?php endif; ?>
        </div>
      </li>
      <li class="nav-item dropdown ml-3">
        <a class="nav-link" id="notificationDropdown" href="#" data-toggle="dropdown">
          <i class="mdi mdi-bell-outline"></i>
          <?php if(isset($data['unread_notifications']) && $data['unread_notifications'] > 0): ?>
            <span class="count-symbol bg-danger"></span>
          <?php endif; ?>
        </a>
        <div class="dropdown-menu dropdown-menu-left navbar-dropdown preview-list" aria-labelledby="notificationDropdown">
          <h6 class="px-3 py-3 font-weight-semibold mb-0">Cảnh báo</h6>
          <div class="dropdown-divider"></div>
          
          <?php if(isset($data['alerts']) && !empty($data['alerts'])): ?>
            <?php foreach($data['alerts'] as $alert): ?>
              <a class="dropdown-item preview-item">
                <div class="preview-thumbnail">
                  <div class="preview-icon bg-<?php echo $alert['type']; ?>">
                    <i class="mdi mdi-<?php echo $alert['icon']; ?>"></i>
                  </div>
                </div>
                <div class="preview-item-content d-flex align-items-start flex-column justify-content-center">
                  <h6 class="preview-subject font-weight-normal mb-0"><?php echo $alert['title']; ?></h6>
                  <p class="text-gray ellipsis mb-0"><?php echo $alert['time']; ?></p>
                </div>
              </a>
              <div class="dropdown-divider"></div>
            <?php endforeach; ?>
          <?php else: ?>
            <a class="dropdown-item preview-item">
              <div class="preview-item-content d-flex align-items-start flex-column justify-content-center">
                <h6 class="preview-subject font-weight-normal mb-0">Không có cảnh báo mới</h6>
              </div>
            </a>
          <?php endif; ?>
          
          <?php if(isset($data['alerts']) && !empty($data['alerts'])): ?>
            <h6 class="p-3 font-13 mb-0 text-primary text-center">
              <a href="<?php echo BASEURL; ?>/alerts">Xem tất cả cảnh báo</a>
            </h6>
          <?php endif; ?>
        </div>
      </li>
    </ul>
    <ul class="navbar-nav navbar-nav-right">
      <li class="nav-item nav-logout d-none d-md-block mr-3">
        <a class="nav-link" href="<?php echo BASEURL; ?>/budget">
          <span class="font-weight-medium">Ngân sách: </span>
          <span class="font-weight-bold text-<?php echo isset($data['budget_status']) ? $data['budget_status'] : 'success'; ?>">
            <?php echo isset($data['current_budget']) ? number_format($data['current_budget'], 0, ',', '.') . 'đ' : '0đ'; ?>
          </span>
        </a>
      </li>
      <li class="nav-item nav-logout d-none d-md-block">
        <a class="btn btn-sm btn-<?php echo isset($data['trend']) && $data['trend'] == 'up' ? 'success' : 'danger'; ?>">
          <?php echo isset($data['trend']) && $data['trend'] == 'up' ? '+' : '-'; ?>
          <?php echo isset($data['trend_value']) ? number_format($data['trend_value'], 0, ',', '.') : '0'; ?>%
        </a>
      </li>
      <li class="nav-item nav-profile dropdown d-none d-md-block">
        <a class="nav-link dropdown-toggle" id="profileDropdown" href="#" data-toggle="dropdown" aria-expanded="false">
          <div class="nav-profile-text">
            <?php echo isset($_SESSION['user_name']) ? $_SESSION['user_name'] : 'Người dùng'; ?>
          </div>
        </a>
        <div class="dropdown-menu center navbar-dropdown" aria-labelledby="profileDropdown">
          <a class="dropdown-item" href="<?php echo BASEURL; ?>/profile">
            <i class="mdi mdi-account-circle mr-3"></i> Hồ sơ </a>
          <div class="dropdown-divider"></div>
          <a class="dropdown-item" href="<?php echo BASEURL; ?>/settings">
            <i class="mdi mdi-settings mr-3"></i> Cài đặt </a>
          <div class="dropdown-divider"></div>
          <a class="dropdown-item" href="<?php echo BASEURL; ?>/auth/logout">
            <i class="mdi mdi-logout mr-3"></i> Đăng xuất </a>
        </div>
      </li>
      <li class="nav-item nav-logout d-none d-lg-block">
        <a class="nav-link" href="<?php echo BASEURL; ?>/dashboard">
          <i class="mdi mdi-home-circle"></i>
        </a>
      </li>
    </ul>
    <button class="navbar-toggler navbar-toggler-right d-lg-none align-self-center" type="button" data-toggle="offcanvas">
      <span class="mdi mdi-menu"></span>
    </button>
  </div>
</nav>