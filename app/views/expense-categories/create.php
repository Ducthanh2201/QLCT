<?php require_once APPROOT . '/views/layouts/header.php'; ?>
<?php require_once APPROOT . '/views/layouts/sidebar.php'; ?>

<div class="container-fluid page-body-wrapper">
  <?php require_once APPROOT . '/views/partials/_settings-panel.php'; ?>
  <div class="main-panel">
    <div class="content-wrapper">
      <div class="page-header">
        <h3 class="page-title">
          <span class="page-title-icon bg-gradient-primary text-white mr-2">
            <i class="mdi mdi-plus"></i>
          </span>
          <?php echo $title; ?>
        </h3>
        <nav aria-label="breadcrumb">
          <ul class="breadcrumb">
            <li class="breadcrumb-item"><a href="<?php echo BASEURL; ?>/dashboard">Tổng quan</a></li>
            <li class="breadcrumb-item"><a href="<?php echo BASEURL; ?>/expense-categories">Danh mục chi tiêu</a></li>
            <li class="breadcrumb-item active" aria-current="page">Thêm mới</li>
          </ul>
        </nav>
      </div>
      
      <div class="row">
        <div class="col-md-8 grid-margin stretch-card">
          <div class="card">
            <div class="card-body">
              <h4 class="card-title">Thêm danh mục chi tiêu mới</h4>
              <p class="card-description">Vui lòng điền đầy đủ thông tin bên dưới</p>
              
              <?php if(isset($error)): ?>
                <div class="alert alert-danger"><?php echo $error; ?></div>
              <?php endif; ?>
              
              <form class="forms-sample" method="post" action="<?php echo BASEURL; ?>/expense-categories/create">
                <div class="form-group">
                  <label for="name">Tên danh mục <span class="text-danger">*</span></label>
                  <input type="text" class="form-control" id="name" name="name" placeholder="Nhập tên danh mục" 
                         value="<?php echo isset($name) ? htmlspecialchars($name) : ''; ?>" required>
                  <?php if(isset($errors['name'])): ?>
                    <small class="text-danger"><?php echo $errors['name']; ?></small>
                  <?php endif; ?>
                </div>
                
                <div class="form-group">
                  <label for="description">Mô tả</label>
                  <textarea class="form-control" id="description" name="description" rows="4" 
                            placeholder="Mô tả về danh mục này"><?php echo isset($description) ? htmlspecialchars($description) : ''; ?></textarea>
                </div>
                
                <div class="form-group">
                  <label for="color">Màu sắc</label>
                  <div class="d-flex align-items-center">
                    <input type="color" class="form-control color-picker" id="color" name="color" 
                           value="<?php echo isset($color) ? htmlspecialchars($color) : '#4CAF50'; ?>" style="width:50px; height:40px;">
                    <span class="ml-2" id="color-hex-value"><?php echo isset($color) ? htmlspecialchars($color) : '#4CAF50'; ?></span>
                  </div>
                </div>
                
                <div class="form-group">
                  <label for="icon">Biểu tượng</label>
                  <select class="form-control" id="icon" name="icon">
                    <?php foreach($icons as $value => $label): ?>
                      <option value="<?php echo $value; ?>" <?php echo (isset($icon) && $icon === $value) ? 'selected' : ''; ?>>
                        <?php echo htmlspecialchars($label); ?>
                      </option>
                    <?php endforeach; ?>
                  </select>
                </div>
                
                <div class="form-group">
                  <label>Xem trước</label>
                  <div class="category-preview p-3 border rounded">
                    <div class="d-flex align-items-center">
                      <div class="preview-icon">
                        <?php if(isset($icon)): ?>
                          <img src="<?php echo BASEURL; ?>/assets/images/icons/<?php echo $icon; ?>" alt="Icon" width="24" height="24" id="preview-icon-img" />
                        <?php else: ?>
                          <img src="<?php echo BASEURL; ?>/assets/images/icons/default.png" alt="Icon" width="24" height="24" id="preview-icon-img" />
                        <?php endif; ?>
                      </div>
                      <div class="ml-3">
                        <div class="d-flex align-items-center">
                          <div id="preview-color" style="width:15px; height:15px; border-radius:50%; background-color:<?php echo isset($color) ? htmlspecialchars($color) : '#4CAF50'; ?>;"></div>
                          <span class="ml-2" id="preview-name"><?php echo isset($name) ? htmlspecialchars($name) : 'Tên danh mục'; ?></span>
                        </div>
                        <p class="text-muted mt-1 mb-0" id="preview-description">
                          <?php echo isset($description) && !empty($description) ? htmlspecialchars($description) : 'Mô tả danh mục'; ?>
                        </p>
                      </div>
                    </div>
                  </div>
                </div>
                
                <button type="submit" class="btn btn-gradient-primary mr-2">Lưu</button>
                <a href="<?php echo BASEURL; ?>/expense-categories" class="btn btn-light">Hủy</a>
              </form>
            </div>
          </div>
        </div>
        
        <div class="col-md-4 grid-margin stretch-card">
          <div class="card">
            <div class="card-body">
              <h4 class="card-title">Hướng dẫn</h4>
              <p>Danh mục chi tiêu giúp bạn phân loại và theo dõi các khoản chi tiêu một cách hiệu quả.</p>
              <ul class="mt-3">
                <li>Tên danh mục nên ngắn gọn và dễ nhớ</li>
                <li>Mỗi danh mục nên có màu sắc riêng để dễ nhận biết</li>
                <li>Thêm mô tả để hiểu rõ hơn về mục đích của danh mục</li>
                <li>Bạn có thể tùy chỉnh biểu tượng cho từng danh mục</li>
              </ul>
              
              <div class="mt-4">
                <h5>Các danh mục thông dụng</h5>
                <div class="d-flex flex-wrap mt-3">
                  <?php
                  $commonCategories = [
                      ['name' => 'Ăn uống', 'color' => '#FF5722'],
                      ['name' => 'Di chuyển', 'color' => '#2196F3'],
                      ['name' => 'Mua sắm', 'color' => '#9C27B0'],
                      ['name' => 'Giải trí', 'color' => '#FF9800'],
                      ['name' => 'Hóa đơn', 'color' => '#F44336'],
                  ];
                  
                  foreach($commonCategories as $cat):
                  ?>
                    <div class="common-category mr-2 mb-2" 
                         data-name="<?php echo $cat['name']; ?>" 
                         data-color="<?php echo $cat['color']; ?>">
                      <div class="d-flex align-items-center p-2 border rounded" style="cursor: pointer;">
                        <div style="width:10px; height:10px; border-radius:50%; background-color:<?php echo $cat['color']; ?>;"></div>
                        <span class="ml-2"><?php echo $cat['name']; ?></span>
                      </div>
                    </div>
                  <?php endforeach; ?>
                </div>
              </div>
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
  // Live preview handling
  const nameInput = document.getElementById('name');
  const descInput = document.getElementById('description');
  const colorInput = document.getElementById('color');
  const iconSelect = document.getElementById('icon');
  const colorHexValue = document.getElementById('color-hex-value');
  
  const previewName = document.getElementById('preview-name');
  const previewDesc = document.getElementById('preview-description');
  const previewColor = document.getElementById('preview-color');
  const previewIcon = document.getElementById('preview-icon-img');
  
  nameInput.addEventListener('input', function() {
    previewName.textContent = this.value || 'Tên danh mục';
  });
  
  descInput.addEventListener('input', function() {
    previewDesc.textContent = this.value || 'Mô tả danh mục';
  });
  
  colorInput.addEventListener('input', function() {
    previewColor.style.backgroundColor = this.value;
    colorHexValue.textContent = this.value;
  });
  
  iconSelect.addEventListener('change', function() {
    previewIcon.src = '<?php echo BASEURL; ?>/assets/images/icons/' + this.value;
  });
  
  // Common category suggestion
  const commonCategories = document.querySelectorAll('.common-category');
  commonCategories.forEach(cat => {
    cat.addEventListener('click', function() {
      const catName = this.getAttribute('data-name');
      const catColor = this.getAttribute('data-color');
      
      nameInput.value = catName;
      colorInput.value = catColor;
      colorHexValue.textContent = catColor;
      
      // Update preview
      previewName.textContent = catName;
      previewColor.style.backgroundColor = catColor;
    });
  });
});
</script>

<?php require_once APPROOT . '/views/layouts/footer_scripts.php'; ?>