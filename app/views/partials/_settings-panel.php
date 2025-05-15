<?php
// filepath: c:\xampp\htdocs\ADM-QLCT\expense-management\app\views\partials\_settings-panel.php
?>
<!-- Settings Panel -->
<div id="settings-trigger"><i class="mdi mdi-settings"></i></div>
<div id="theme-settings" class="settings-panel">
  <i class="settings-close mdi mdi-close"></i>
  <p class="settings-heading">TÙY CHỈNH GIAO DIỆN</p>
  <div class="sidebar-bg-options selected" id="sidebar-default-theme">
    <div class="img-ss rounded-circle bg-light border mr-3"></div>Sáng
  </div>
  <div class="sidebar-bg-options" id="sidebar-dark-theme">
    <div class="img-ss rounded-circle bg-dark border mr-3"></div>Tối
  </div>
  <p class="settings-heading mt-2">MÀU SẮC HEADER</p>
  <div class="color-tiles mx-0 px-4">
    <div class="tiles default primary"></div>
    <div class="tiles success"></div>
    <div class="tiles warning"></div>
    <div class="tiles danger"></div>
    <div class="tiles info"></div>
    <div class="tiles dark"></div>
    <div class="tiles light"></div>
  </div>
  
  <div class="mt-4 pt-3 border-top">
    <p class="settings-heading">CÀI ĐẶT NGƯỜI DÙNG</p>
    <div class="settings-section">
      <div class="form-group mb-3">
        <label>Đơn vị tiền tệ</label>
        <select class="form-control" id="currencySelect">
          <option value="VND" <?php echo (isset($_SESSION['currency']) && $_SESSION['currency'] == 'VND') ? 'selected' : ''; ?>>VNĐ (₫)</option>
          <option value="USD" <?php echo (isset($_SESSION['currency']) && $_SESSION['currency'] == 'USD') ? 'selected' : ''; ?>>USD ($)</option>
          <option value="EUR" <?php echo (isset($_SESSION['currency']) && $_SESSION['currency'] == 'EUR') ? 'selected' : ''; ?>>EUR (€)</option>
        </select>
      </div>
      
      <div class="form-group mb-3">
        <label>Định dạng ngày</label>
        <select class="form-control" id="dateFormatSelect">
          <option value="d/m/Y" <?php echo (isset($_SESSION['date_format']) && $_SESSION['date_format'] == 'd/m/Y') ? 'selected' : ''; ?>>DD/MM/YYYY</option>
          <option value="m/d/Y" <?php echo (isset($_SESSION['date_format']) && $_SESSION['date_format'] == 'm/d/Y') ? 'selected' : ''; ?>>MM/DD/YYYY</option>
          <option value="Y-m-d" <?php echo (isset($_SESSION['date_format']) && $_SESSION['date_format'] == 'Y-m-d') ? 'selected' : ''; ?>>YYYY-MM-DD</option>
        </select>
      </div>
      
      <div class="form-group">
        <div class="form-check form-check-flat form-check-primary">
          <label class="form-check-label">
            <input type="checkbox" class="form-check-input" id="notificationToggle" 
                  <?php echo (isset($_SESSION['notifications_enabled']) && $_SESSION['notifications_enabled']) ? 'checked' : ''; ?>>
            Bật thông báo
          </label>
        </div>
      </div>
      
      <div class="form-group">
        <div class="form-check form-check-flat form-check-primary">
          <label class="form-check-label">
            <input type="checkbox" class="form-check-input" id="budgetAlertToggle"
                  <?php echo (isset($_SESSION['budget_alerts']) && $_SESSION['budget_alerts']) ? 'checked' : ''; ?>>
            Cảnh báo khi vượt ngân sách
          </label>
        </div>
      </div>
      
      <div class="form-group mt-4">
        <button type="button" id="saveSettings" class="btn btn-primary btn-sm btn-block">
          <i class="mdi mdi-content-save mr-1"></i> Lưu cài đặt
        </button>
      </div>
    </div>
  </div>
</div>
<!-- End Settings Panel -->

<script>
document.addEventListener('DOMContentLoaded', function() {
  // Lưu cài đặt qua AJAX
  document.getElementById('saveSettings').addEventListener('click', function() {
    const currency = document.getElementById('currencySelect').value;
    const dateFormat = document.getElementById('dateFormatSelect').value;
    const notifications = document.getElementById('notificationToggle').checked;
    const budgetAlerts = document.getElementById('budgetAlertToggle').checked;
    
    fetch('<?php echo BASEURL; ?>/settings/update-preferences', {
      method: 'POST',
      headers: {
        'Content-Type': 'application/x-www-form-urlencoded',
      },
      body: `currency=${currency}&date_format=${dateFormat}&notifications=${notifications}&budget_alerts=${budgetAlerts}`
    })
    .then(response => response.json())
    .then(data => {
      if(data.success) {
        // Hiển thị thông báo thành công
        const alert = document.createElement('div');
        alert.className = 'alert alert-success alert-dismissible fade show';
        alert.role = 'alert';
        alert.innerHTML = `
          <i class="mdi mdi-check-circle mr-2"></i>
          <strong>Lưu cài đặt thành công</strong>
          <button type="button" class="close" data-dismiss="alert" aria-label="Đóng">
            <span aria-hidden="true">&times;</span>
          </button>
        `;
        
        document.querySelector('.settings-panel').prepend(alert);
        
        // Tự động đóng thông báo sau 3 giây
        setTimeout(() => {
          alert.classList.remove('show');
          setTimeout(() => alert.remove(), 150);
        }, 3000);
      } else {
        // Hiển thị thông báo lỗi
        alert('Có lỗi xảy ra. Vui lòng thử lại!');
      }
    })
    .catch(error => {
      console.error('Error:', error);
      alert('Có lỗi xảy ra khi lưu cài đặt');
    });
  });
  
  // Xử lý sự kiện thay đổi theme
  document.getElementById('sidebar-default-theme').addEventListener('click', function() {
    document.body.classList.remove('sidebar-dark');
    this.classList.add('selected');
    document.getElementById('sidebar-dark-theme').classList.remove('selected');
  });
  
  document.getElementById('sidebar-dark-theme').addEventListener('click', function() {
    document.body.classList.add('sidebar-dark');
    this.classList.add('selected');
    document.getElementById('sidebar-default-theme').classList.remove('selected');
  });
  
  // Xử lý sự kiện thay đổi màu header
  const colorTiles = document.querySelectorAll('.color-tiles .tiles');
  colorTiles.forEach(tile => {
    tile.addEventListener('click', function() {
      const color = this.classList[1]; // primary, success, warning, etc.
      
      // Xóa tất cả class màu header hiện tại
      document.querySelector('.navbar').classList.remove('navbar-primary', 'navbar-success', 'navbar-warning', 
        'navbar-danger', 'navbar-info', 'navbar-dark', 'navbar-light');
      
      // Thêm class màu header mới
      document.querySelector('.navbar').classList.add(`navbar-${color}`);
      
      // Lưu cài đặt
      fetch('<?php echo BASEURL; ?>/settings/update-theme', {
        method: 'POST',
        headers: {
          'Content-Type': 'application/x-www-form-urlencoded',
        },
        body: `header_color=${color}`
      });
    });
  });
});
</script>