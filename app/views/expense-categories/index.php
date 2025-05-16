<?php require_once APPROOT . '/views/layouts/header.php'; ?>
<?php require_once APPROOT . '/views/layouts/sidebar.php'; ?>

<div class="container-fluid page-body-wrapper">
  <?php require_once APPROOT . '/views/partials/_settings-panel.php'; ?>
  <div class="main-panel">
    <div class="content-wrapper">
      <div class="page-header">
        <h3 class="page-title">
          <span class="page-title-icon bg-gradient-primary text-white mr-2">
            <i class="mdi mdi-format-list-bulleted"></i>
          </span>
          <?php echo $title; ?>
        </h3>
        <nav aria-label="breadcrumb">
          <ul class="breadcrumb">
            <li class="breadcrumb-item"><a href="<?php echo BASEURL; ?>/dashboard">Tổng quan</a></li>
            <li class="breadcrumb-item active" aria-current="page">Danh mục chi tiêu</li>
          </ul>
        </nav>
      </div>
      
      <?php if(isset($_SESSION['success'])): ?>
        <div class="alert alert-success">
          <?php 
            echo $_SESSION['success']; 
            unset($_SESSION['success']);
          ?>
        </div>
      <?php endif; ?>
      
      <?php if(isset($_SESSION['error'])): ?>
        <div class="alert alert-danger">
          <?php 
            echo $_SESSION['error']; 
            unset($_SESSION['error']);
          ?>
        </div>
      <?php endif; ?>
      
      <div class="row">
        <div class="col-12">
          <div class="card">
            <div class="card-body">
              <div class="d-flex justify-content-between align-items-center mb-4">
                <h4 class="card-title">Danh sách danh mục chi tiêu</h4>
                <div>
                  <form action="<?php echo BASEURL; ?>/expense-categories/import-samples" method="post" class="d-inline">
                    <button type="submit" class="btn btn-outline-info btn-icon-text">
                      <i class="mdi mdi-download btn-icon-prepend"></i>
                      Nhập mẫu
                    </button>
                  </form>
                  <a href="<?php echo BASEURL; ?>/expense-categories/create" class="btn btn-gradient-primary btn-icon-text">
                    <i class="mdi mdi-plus btn-icon-prepend"></i>
                    Thêm danh mục mới
                  </a>
                </div>
              </div>
              
              <?php if(empty($categories)): ?>
                <div class="alert alert-info">
                  <p>Bạn chưa có danh mục chi tiêu nào. Hãy thêm danh mục mới hoặc nhập danh mục mẫu.</p>
                </div>
              <?php else: ?>
                <div class="table-responsive">
                  <table class="table table-hover">
                    <thead>
                      <tr>
                        <th>ID</th>
                        <th>Tên danh mục</th>
                        <th>Mô tả</th>
                        <th>Màu sắc</th>
                        <th>Biểu tượng</th>
                        <th>Ngày tạo</th>
                        <th>Thao tác</th>
                      </tr>
                    </thead>
                    <tbody>
                      <?php foreach($categories as $category): ?>
                        <tr>
                          <td><?php echo $category->category_id; ?></td>
                          <td>
                            <div class="d-flex align-items-center">
                              <div class="category-color-indicator" style="width:15px; height:15px; border-radius:50%; background-color:<?php echo $category->color; ?>;"></div>
                              <span class="ml-2"><?php echo htmlspecialchars($category->name); ?></span>
                            </div>
                          </td>
                          <td>
                            <?php echo !empty($category->description) ? htmlspecialchars($category->description) : '<em>Không có mô tả</em>'; ?>
                          </td>
                          <td>
                            <div class="color-preview" style="width:30px; height:20px; background-color:<?php echo $category->color; ?>; border-radius:3px;"></div>
                          </td>
                          <td>
                            <?php if(!empty($category->icon)): ?>
                              <img src="<?php echo BASEURL; ?>/assets/images/icons/<?php echo $category->icon; ?>" alt="Icon" width="24" height="24" />
                            <?php else: ?>
                              <i class="mdi mdi-tag"></i>
                            <?php endif; ?>
                          </td>
                          <td><?php echo date('d/m/Y', strtotime($category->created_at)); ?></td>
                          <td>
                            <div class="dropdown">
                              <button class="btn btn-gradient-primary btn-sm dropdown-toggle" type="button" id="dropdownMenuButton<?php echo $category->category_id; ?>" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                Tùy chọn
                              </button>
                              <div class="dropdown-menu" aria-labelledby="dropdownMenuButton<?php echo $category->category_id; ?>">
                                <a class="dropdown-item" href="<?php echo BASEURL; ?>/expense-categories/edit/<?php echo $category->category_id; ?>">
                                  <i class="mdi mdi-pencil menu-icon"></i> Sửa
                                </a>
                                <a class="dropdown-item" href="<?php echo BASEURL; ?>/expense-categories/duplicate/<?php echo $category->category_id; ?>" 
                                   onclick="return confirm('Bạn có chắc muốn tạo bản sao của danh mục này?')">
                                  <i class="mdi mdi-content-copy menu-icon"></i> Sao chép
                                </a>
                                <div class="dropdown-divider"></div>
                                <form action="<?php echo BASEURL; ?>/expense-categories/delete/<?php echo $category->category_id; ?>" method="post" class="delete-form">
                                  <button type="submit" class="dropdown-item text-danger" onclick="return confirm('Bạn có chắc muốn xóa danh mục này?')">
                                    <i class="mdi mdi-delete menu-icon"></i> Xóa
                                  </button>
                                </form>
                              </div>
                            </div>
                          </td>
                        </tr>
                      <?php endforeach; ?>
                    </tbody>
                  </table>
                </div>
              <?php endif; ?>
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
  // Format color indicators
  const colorIndicators = document.querySelectorAll('.category-color-indicator');
  colorIndicators.forEach(indicator => {
    indicator.style.display = 'inline-block';
    indicator.style.marginRight = '5px';
  });
  
  // Add smooth animation for alerts
  const alerts = document.querySelectorAll('.alert');
  alerts.forEach(alert => {
    setTimeout(() => {
      alert.classList.add('fade-out');
      setTimeout(() => {
        alert.style.display = 'none';
      }, 500);
    }, 3000);
  });
});
</script>

<style>
.fade-out {
  opacity: 0;
  transition: opacity 0.5s ease-out;
}
</style>

<?php require_once APPROOT . '/views/layouts/footer_scripts.php'; ?>