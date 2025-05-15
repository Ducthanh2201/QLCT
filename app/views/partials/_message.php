<?php
// filepath: c:\xampp\htdocs\ADM-QLCT\expense-management\app\views\partials\_message.php
?>
<?php if(isset($_SESSION['message']) && !empty($_SESSION['message'])): ?>
    <div class="alert alert-<?php echo isset($_SESSION['message_type']) ? $_SESSION['message_type'] : 'info'; ?> alert-dismissible fade show" role="alert">
        <?php if(isset($_SESSION['message_icon'])): ?>
            <i class="mdi <?php echo $_SESSION['message_icon']; ?> mr-2"></i>
        <?php endif; ?>
        <strong>
            <?php 
                echo isset($_SESSION['message_title']) ? $_SESSION['message_title'] . ': ' : '';
                echo $_SESSION['message']; 
            ?>
        </strong>
        <button type="button" class="close" data-dismiss="alert" aria-label="Đóng">
            <span aria-hidden="true">&times;</span>
        </button>
    </div>
    <?php 
        // Xóa message sau khi hiển thị
        unset($_SESSION['message']);
        unset($_SESSION['message_type']);
        unset($_SESSION['message_title']);
        unset($_SESSION['message_icon']);
    ?>
<?php endif; ?>

<?php if(isset($data['errors']) && !empty($data['errors'])): ?>
    <div class="alert alert-danger alert-dismissible fade show" role="alert">
        <i class="mdi mdi-alert-circle mr-2"></i>
        <strong>Có lỗi xảy ra:</strong>
        <ul class="mb-0 mt-2">
            <?php foreach($data['errors'] as $error): ?>
                <li><?php echo $error; ?></li>
            <?php endforeach; ?>
        </ul>
        <button type="button" class="close" data-dismiss="alert" aria-label="Đóng">
            <span aria-hidden="true">&times;</span>
        </button>
    </div>
<?php endif; ?>

<?php if(isset($data['success']) && !empty($data['success'])): ?>
    <div class="alert alert-success alert-dismissible fade show" role="alert">
        <i class="mdi mdi-check-circle mr-2"></i>
        <strong><?php echo $data['success']; ?></strong>
        <button type="button" class="close" data-dismiss="alert" aria-label="Đóng">
            <span aria-hidden="true">&times;</span>
        </button>
    </div>
<?php endif; ?>

<?php
// Hiển thị thông báo lỗi hoặc thành công từ session hoặc biến $data
if (isset($_SESSION['success'])) {
    echo '<div class="alert alert-success alert-dismissible fade show" role="alert">
            <i class="mdi mdi-check-circle-outline mr-2"></i>
            <strong>' . $_SESSION['success'] . '</strong>
            <button type="button" class="close" data-dismiss="alert" aria-label="Đóng">
              <span aria-hidden="true">&times;</span>
            </button>
          </div>';
    unset($_SESSION['success']);
}

if (isset($_SESSION['error'])) {
    echo '<div class="alert alert-danger alert-dismissible fade show" role="alert">
            <i class="mdi mdi-alert-circle-outline mr-2"></i>
            <strong>' . $_SESSION['error'] . '</strong>
            <button type="button" class="close" data-dismiss="alert" aria-label="Đóng">
              <span aria-hidden="true">&times;</span>
            </button>
          </div>';
    unset($_SESSION['error']);
}

if (isset($data['error'])) {
    echo '<div class="alert alert-danger alert-dismissible fade show" role="alert">
            <i class="mdi mdi-alert-circle-outline mr-2"></i>
            <strong>' . $data['error'] . '</strong>
            <button type="button" class="close" data-dismiss="alert" aria-label="Đóng">
              <span aria-hidden="true">&times;</span>
            </button>
          </div>';
}

if (isset($data['success'])) {
    echo '<div class="alert alert-success alert-dismissible fade show" role="alert">
            <i class="mdi mdi-check-circle-outline mr-2"></i>
            <strong>' . $data['success'] . '</strong>
            <button type="button" class="close" data-dismiss="alert" aria-label="Đóng">
              <span aria-hidden="true">&times;</span>
            </button>
          </div>';
}

// Nếu có mảng errors nhưng không phải là thông báo đơn lẻ
if (isset($data['errors']) && is_array($data['errors']) && !empty($data['errors'])) {
    echo '<div class="alert alert-danger alert-dismissible fade show" role="alert">
            <i class="mdi mdi-alert-circle-outline mr-2"></i>
            <strong>Vui lòng sửa các lỗi sau:</strong>
            <ul class="mb-0 mt-2 pl-3">';
    foreach($data['errors'] as $error) {
        echo '<li>' . $error . '</li>';
    }
    echo '</ul>
            <button type="button" class="close" data-dismiss="alert" aria-label="Đóng">
              <span aria-hidden="true">&times;</span>
            </button>
          </div>';
}
?>