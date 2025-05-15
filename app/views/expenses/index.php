<?php
// filepath: c:\xampp\htdocs\ADM-QLCT\expense-management\app\views\expenses\index.php
require APPROOT . '/views/layouts/header.php';
require APPROOT . '/views/layouts/sidebar.php';
?>

<style>
  /* Dot màu cho danh mục */
  .color-dot {
    width: 12px;
    height: 12px;
    border-radius: 50%;
    display: inline-block;
  }
  
  /* Định dạng cho badge */
  .badge {
    font-size: 0.8rem;
    padding: 0.3rem 0.5rem;
  }
  
  /* Định dạng cho các card thống kê */
  .card-stat {
    position: relative;
    background: linear-gradient(45deg, #7B1FA2, #9C27B0);
    color: white;
  }
  
  .card-stat .badge {
    font-size: 0.75rem;
  }
  
  /* Định dạng cho table */
  .table thead th {
    border-top: none;
    border-bottom: 2px solid #ebedf2;
    font-weight: 500;
    font-size: 0.875rem;
    text-transform: uppercase;
  }
  
  .table td {
    font-size: 0.875rem;
    padding: 0.9rem;
    vertical-align: middle;
  }
  
  /* Cải thiện bộ lọc */
  #start_date, #end_date {
    height: calc(2rem + 2px);
    padding: 0.375rem 0.75rem;
  }
  
  /* Hiệu ứng hover cho button */
  .btn-outline-secondary:hover {
    background-color: #6c757d;
    color: white;
  }
  
  /* Pagination */
  .pagination {
    margin-bottom: 0;
  }
  
  .pagination .page-link {
    padding: 0.25rem 0.5rem;
    font-size: 0.875rem;
    line-height: 1.5;
  }
  
  /* Biểu đồ */
  .chart-container {
    position: relative;
    margin: auto;
    height: 300px;
  }
</style>

<div class="container-fluid page-body-wrapper">
  <?php require APPROOT . '/views/partials/_settings-panel.php'; ?>
  <?php require APPROOT . '/views/layouts/navbar.php'; ?>
  
  <div class="main-panel">
    <div class="content-wrapper">
      <?php require APPROOT . '/views/partials/_message.php'; ?>
      
      <div class="page-header">
        <h3 class="page-title">Quản lý Chi tiêu</h3>
        <nav aria-label="breadcrumb">
          <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="<?php echo BASEURL; ?>/dashboard">Dashboard</a></li>
            <li class="breadcrumb-item active" aria-current="page">Danh sách chi tiêu</li>
          </ol>
        </nav>
      </div>
      
      <div class="row">
        <div class="col-lg-12 grid-margin stretch-card">
          <div class="card">
            <div class="card-body">
              <div class="d-flex justify-content-between align-items-center mb-4">
                <h4 class="card-title">Danh sách chi tiêu</h4>
                <a href="<?php echo BASEURL; ?>/expenses/create" class="btn btn-primary btn-sm">
                  <i class="mdi mdi-plus-circle"></i> Thêm mới
                </a>
              </div>
              
              <!-- Cập nhật phần bộ lọc -->
              <div class="row mb-4">
                <div class="col-md-4 mb-3">
                  <div class="input-group">
                    <div class="input-group-prepend">
                      <span class="input-group-text bg-primary text-white">Từ</span>
                    </div>
                    <input type="date" class="form-control" id="start_date" name="start_date" 
                           value="<?php echo isset($data['filters']['start_date']) ? $data['filters']['start_date'] : date('Y-m-01'); ?>">
                  </div>
                </div>
                <div class="col-md-4 mb-3">
                  <div class="input-group">
                    <div class="input-group-prepend">
                      <span class="input-group-text bg-primary text-white">Đến</span>
                    </div>
                    <input type="date" class="form-control" id="end_date" name="end_date" 
                           value="<?php echo isset($data['filters']['end_date']) ? $data['filters']['end_date'] : date('Y-m-t'); ?>">
                  </div>
                </div>
                <div class="col-md-4 mb-3">
                  <button type="button" class="btn btn-primary btn-block" onclick="applyDateFilter()">
                    <i class="mdi mdi-filter"></i> Lọc theo ngày
                  </button>
                </div>
              </div>
              
              <div class="row mb-2">
                <div class="col-12">
                  <div class="btn-group btn-group-sm btn-block">
                    <button type="button" class="btn btn-outline-secondary" onclick="setDateRange('today')">Hôm nay</button>
                    <button type="button" class="btn btn-outline-secondary" onclick="setDateRange('yesterday')">Hôm qua</button>
                    <button type="button" class="btn btn-outline-secondary" onclick="setDateRange('week')">Tuần này</button>
                    <button type="button" class="btn btn-outline-secondary" onclick="setDateRange('month')">Tháng này</button>
                    <button type="button" class="btn btn-outline-secondary" onclick="setDateRange('year')">Năm nay</button>
                  </div>
                </div>
              </div>
              
              <div class="row mb-4">
                <div class="col-md-8 mb-3 mb-md-0">
                  <form action="<?php echo BASEURL; ?>/expenses" method="get" class="d-flex">
                    <!-- Ẩn các input cho filter khác -->
                    <input type="hidden" id="hidden_start_date" name="start_date" value="<?php echo isset($data['filters']['start_date']) ? $data['filters']['start_date'] : ''; ?>">
                    <input type="hidden" id="hidden_end_date" name="end_date" value="<?php echo isset($data['filters']['end_date']) ? $data['filters']['end_date'] : ''; ?>">
                    <input type="hidden" id="hidden_category" name="category" value="<?php echo isset($data['filters']['category_id']) ? $data['filters']['category_id'] : ''; ?>">
                    
                    <div class="input-group w-100">
                      <input type="text" class="form-control" placeholder="Tìm kiếm theo mô tả" name="search" value="<?php echo isset($data['filters']['search']) ? $data['filters']['search'] : ''; ?>">
                      <div class="input-group-append">
                        <button class="btn btn-sm btn-primary" type="submit">
                          <i class="mdi mdi-magnify"></i> Tìm kiếm
                        </button>
                      </div>
                    </div>
                  </form>
                </div>
                <div class="col-md-4">
                  <div class="input-group">
                    <div class="input-group-prepend">
                      <span class="input-group-text bg-primary text-white">
                        <i class="mdi mdi-tag-multiple"></i>
                      </span>
                    </div>
                    <select class="form-control" id="filterCategory" onchange="filterByCategory(this.value)">
                      <option value="">-- Tất cả danh mục --</option>
                      <?php if(isset($data['categories']) && !empty($data['categories'])): ?>
                        <?php foreach($data['categories'] as $category): ?>
                          <option value="<?php echo $category->category_id; ?>" <?php echo (isset($data['filters']['category_id']) && $data['filters']['category_id'] == $category->category_id) ? 'selected' : ''; ?>>
                            <?php echo $category->name; ?>
                          </option>
                        <?php endforeach; ?>
                      <?php endif; ?>
                    </select>
                  </div>
                </div>
              </div>
              
              <!-- Hiển thị thẻ filter đang áp dụng -->
              <?php if(!empty($data['filters']['search']) || !empty($data['filters']['category_id'])): ?>
                <div class="mb-4">
                  <div class="d-flex flex-wrap">
                    <?php if(!empty($data['filters']['search'])): ?>
                      <div class="badge badge-info mb-2 mr-2 p-2">
                        <i class="mdi mdi-magnify"></i> Tìm kiếm: <?php echo $data['filters']['search']; ?>
                        <a href="<?php echo BASEURL; ?>/expenses?<?php echo !empty($data['filters']['category_id']) ? 'category=' . $data['filters']['category_id'] . '&' : ''; ?>start_date=<?php echo $data['filters']['start_date']; ?>&end_date=<?php echo $data['filters']['end_date']; ?>" class="text-white ml-2">
                          <i class="mdi mdi-close"></i>
                        </a>
                      </div>
                    <?php endif; ?>
                    
                    <?php if(!empty($data['filters']['category_id'])): ?>
                      <?php foreach($data['categories'] as $category): ?>
                        <?php if($category->category_id == $data['filters']['category_id']): ?>
                          <div class="badge badge-primary mb-2 mr-2 p-2">
                            <i class="mdi mdi-tag-multiple"></i> Danh mục: <?php echo $category->name; ?>
                            <a href="<?php echo BASEURL; ?>/expenses?<?php echo !empty($data['filters']['search']) ? 'search=' . $data['filters']['search'] . '&' : ''; ?>start_date=<?php echo $data['filters']['start_date']; ?>&end_date=<?php echo $data['filters']['end_date']; ?>" class="text-white ml-2">
                              <i class="mdi mdi-close"></i>
                            </a>
                          </div>
                          <?php break; ?>
                        <?php endif; ?>
                      <?php endforeach; ?>
                    <?php endif; ?>
                    
                    <?php if(!empty($data['filters']['start_date']) && !empty($data['filters']['end_date'])): ?>
                      <div class="badge badge-success mb-2 mr-2 p-2">
                        <i class="mdi mdi-calendar-range"></i> 
                        Từ <?php echo date('d/m/Y', strtotime($data['filters']['start_date'])); ?> 
                        đến <?php echo date('d/m/Y', strtotime($data['filters']['end_date'])); ?>
                        <a href="<?php echo BASEURL; ?>/expenses?<?php echo !empty($data['filters']['search']) ? 'search=' . $data['filters']['search'] . '&' : ''; ?><?php echo !empty($data['filters']['category_id']) ? 'category=' . $data['filters']['category_id'] : ''; ?>" class="text-white ml-2">
                          <i class="mdi mdi-close"></i>
                        </a>
                      </div>
                    <?php endif; ?>
                    
                    <?php if(!empty($data['filters']['search']) || !empty($data['filters']['category_id']) || (!empty($data['filters']['start_date']) && !empty($data['filters']['end_date']))): ?>
                      <a href="<?php echo BASEURL; ?>/expenses" class="btn btn-sm btn-outline-danger mb-2">
                        <i class="mdi mdi-filter-remove"></i> Xóa tất cả bộ lọc
                      </a>
                    <?php endif; ?>
                  </div>
                </div>
              <?php endif; ?>
              
              <?php if(isset($data['expenses']) && !empty($data['expenses'])): ?>
                <div class="table-responsive">
                  <table class="table table-hover table-striped">
                    <thead>
                      <tr>
                        <th class="text-center">#</th>
                        <th>Ngày</th>
                        <th>Danh mục</th>
                        <th>Mô tả</th>
                        <th class="text-right">Số tiền</th>
                        <th class="text-center">Phương thức</th>
                        <th class="text-center">Trạng thái</th>
                        <th class="text-center">Thao tác</th>
                      </tr>
                    </thead>
                    <tbody>
                      <?php foreach($data['expenses'] as $expense): ?>
                        <tr>
                          <td class="text-center"><?php echo $expense->expense_id; ?></td>
                          <td><?php echo date('d/m/Y', strtotime($expense->expense_date)); ?></td>
                          <td>
                            <div class="d-flex align-items-center">
                              <div class="color-dot mr-2" style="background-color: <?php echo $expense->category_color; ?>"></div>
                              <span><?php echo $expense->category_name; ?></span>
                            </div>
                          </td>
                          <td><?php echo $expense->description; ?></td>
                          <td class="text-right text-danger font-weight-bold">
                            <?php echo number_format($expense->amount, 0, ',', '.'); ?>₫
                          </td>
                          <td class="text-center">
                            <?php if(isset($expense->payment_method)): ?>
                              <?php if($expense->payment_method == 'Tiền mặt'): ?>
                                <span class="badge badge-info" data-toggle="tooltip" title="Tiền mặt">
                                  <i class="mdi mdi-cash"></i> Tiền mặt
                                </span>
                              <?php elseif($expense->payment_method == 'Chuyển khoản'): ?>
                                <span class="badge badge-primary" data-toggle="tooltip" title="Chuyển khoản">
                                  <i class="mdi mdi-bank"></i> Ngân hàng
                                </span>
                              <?php elseif($expense->payment_method == 'Thẻ tín dụng/ghi nợ'): ?>
                                <span class="badge badge-warning" data-toggle="tooltip" title="Thẻ tín dụng/ghi nợ">
                                  <i class="mdi mdi-credit-card"></i> Thẻ
                                </span>
                              <?php else: ?>
                                <span class="badge badge-secondary" data-toggle="tooltip" title="<?php echo $expense->payment_method ?? 'Không xác định'; ?>">
                                  <i class="mdi mdi-dots-horizontal"></i> <?php echo isset($expense->payment_method) ? $expense->payment_method : 'Khác'; ?>
                                </span>
                              <?php endif; ?>
                            <?php else: ?>
                              <span class="badge badge-secondary" data-toggle="tooltip" title="Không có thông tin thanh toán">
                                <i class="mdi mdi-help-circle"></i> Không xác định
                              </span>
                            <?php endif; ?>
                          </td>
                          <td class="text-center">
                            <?php if($expense->status_name == 'Đã thanh toán'): ?>
                              <span class="badge badge-success" data-toggle="tooltip" title="Đã thanh toán">
                                <i class="mdi mdi-check"></i> Đã TT
                              </span>
                            <?php elseif($expense->status_name == 'Chờ thanh toán'): ?>
                              <span class="badge badge-warning" data-toggle="tooltip" title="Chờ thanh toán">
                                <i class="mdi mdi-clock"></i> Chờ TT
                              </span>
                            <?php else: ?>
                              <span class="badge badge-secondary" data-toggle="tooltip" title="<?php echo $expense->status_name; ?>">
                                <i class="mdi mdi-help-circle"></i> <?php echo $expense->status_name; ?>
                              </span>
                            <?php endif; ?>
                          </td>
                          <td class="text-center">
                            <div class="dropdown">
                              <button class="btn btn-sm btn-outline-primary dropdown-toggle" type="button" id="dropdownMenuButton<?php echo $expense->expense_id; ?>" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                <i class="mdi mdi-dots-vertical"></i>
                              </button>
                              <div class="dropdown-menu dropdown-menu-right" aria-labelledby="dropdownMenuButton<?php echo $expense->expense_id; ?>">
                                <a class="dropdown-item" href="<?php echo BASEURL; ?>/expenses/edit/<?php echo $expense->expense_id; ?>">
                                  <i class="mdi mdi-pencil text-primary"></i> Sửa
                                </a>
                                <a class="dropdown-item" href="<?php echo BASEURL; ?>/expenses/detail/<?php echo $expense->expense_id; ?>">
                                  <i class="mdi mdi-eye text-info"></i> Chi tiết
                                </a>
                                <?php if($expense->status_name == 'Chờ thanh toán'): ?>
                                  <a class="dropdown-item mark-paid" href="javascript:void(0)" data-id="<?php echo $expense->expense_id; ?>">
                                    <i class="mdi mdi-check-circle text-success"></i> Đánh dấu đã thanh toán
                                  </a>
                                <?php endif; ?>
                                <div class="dropdown-divider"></div>
                                <a class="dropdown-item text-danger delete-item" href="javascript:void(0)" data-id="<?php echo $expense->expense_id; ?>" data-toggle="modal" data-target="#deleteModal">
                                  <i class="mdi mdi-delete text-danger"></i> Xóa
                                </a>
                              </div>
                            </div>
                          </td>
                        </tr>
                      <?php endforeach; ?>
                    </tbody>
                  </table>
                </div>
                
                <!-- Phân trang -->
                <?php if(isset($data['pagination']) && $data['pagination']['totalPages'] > 1): ?>
                  <div class="mt-4 d-flex justify-content-between align-items-center">
                    <div>
                      Hiển thị <?php echo $data['pagination']['from']; ?> đến <?php echo $data['pagination']['to']; ?> của <?php echo $data['pagination']['total']; ?> khoản chi tiêu
                    </div>
                    <ul class="pagination pagination-sm">
                      <?php if($data['pagination']['currentPage'] > 1): ?>
                        <li class="page-item">
                          <a class="page-link" href="<?php echo BASEURL; ?>/expenses?page=1<?php echo isset($_GET['search']) ? '&search=' . $_GET['search'] : ''; ?><?php echo isset($_GET['category']) ? '&category=' . $_GET['category'] : ''; ?>">
                            <i class="mdi mdi-chevron-double-left"></i>
                          </a>
                        </li>
                        <li class="page-item">
                          <a class="page-link" href="<?php echo BASEURL; ?>/expenses?page=<?php echo $data['pagination']['currentPage'] - 1; ?><?php echo isset($_GET['search']) ? '&search=' . $_GET['search'] : ''; ?><?php echo isset($_GET['category']) ? '&category=' . $_GET['category'] : ''; ?>">
                            <i class="mdi mdi-chevron-left"></i>
                          </a>
                        </li>
                      <?php endif; ?>
                      
                      <?php
                        $start = max(1, $data['pagination']['currentPage'] - 2);
                        $end = min($data['pagination']['totalPages'], $data['pagination']['currentPage'] + 2);
                        
                        for($i = $start; $i <= $end; $i++):
                      ?>
                        <li class="page-item <?php echo $i == $data['pagination']['currentPage'] ? 'active' : ''; ?>">
                          <a class="page-link" href="<?php echo BASEURL; ?>/expenses?page=<?php echo $i; ?><?php echo isset($_GET['search']) ? '&search=' . $_GET['search'] : ''; ?><?php echo isset($_GET['category']) ? '&category=' . $_GET['category'] : ''; ?>">
                            <?php echo $i; ?>
                          </a>
                        </li>
                      <?php endfor; ?>
                      
                      <?php if($data['pagination']['currentPage'] < $data['pagination']['totalPages']): ?>
                        <li class="page-item">
                          <a class="page-link" href="<?php echo BASEURL; ?>/expenses?page=<?php echo $data['pagination']['currentPage'] + 1; ?><?php echo isset($_GET['search']) ? '&search=' . $_GET['search'] : ''; ?><?php echo isset($_GET['category']) ? '&category=' . $_GET['category'] : ''; ?>">
                            <i class="mdi mdi-chevron-right"></i>
                          </a>
                        </li>
                        <li class="page-item">
                          <a class="page-link" href="<?php echo BASEURL; ?>/expenses?page=<?php echo $data['pagination']['totalPages']; ?><?php echo isset($_GET['search']) ? '&search=' . $_GET['search'] : ''; ?><?php echo isset($_GET['category']) ? '&category=' . $_GET['category'] : ''; ?>">
                            <i class="mdi mdi-chevron-double-right"></i>
                          </a>
                        </li>
                      <?php endif; ?>
                    </ul>
                  </div>
                <?php endif; ?>
                
              <?php else: ?>
                <div class="text-center py-5">
                  <i class="mdi mdi-cash-multiple text-muted" style="font-size: 4rem;"></i>
                  <h5 class="text-muted mt-3">Chưa có khoản chi tiêu nào</h5>
                  <p class="text-muted">Thêm khoản chi tiêu đầu tiên của bạn để bắt đầu theo dõi.</p>
                  <a href="<?php echo BASEURL; ?>/expenses/create" class="btn btn-primary mt-3">
                    <i class="mdi mdi-plus-circle"></i> Thêm khoản chi tiêu
                  </a>
                </div>
              <?php endif; ?>
            </div>
          </div>
        </div>
      </div>
      
      <!-- Biểu đồ phân tích chi tiêu -->
      <div class="row mt-4">
        <div class="col-md-6 grid-margin stretch-card">
          <div class="card">
            <div class="card-body">
              <h4 class="card-title">Chi tiêu theo danh mục</h4>
              <div class="chart-container" style="height: 300px;">
                <canvas id="expensesByCategoryChart"></canvas>
              </div>
            </div>
          </div>
        </div>
        <div class="col-md-6 grid-margin stretch-card">
          <div class="card">
            <div class="card-body">
              <h4 class="card-title">Chi tiêu theo thời gian</h4>
              <div class="chart-container" style="height: 300px;">
                <canvas id="expensesByTimeChart"></canvas>
              </div>
            </div>
          </div>
        </div>
      </div>
      
      <!-- Thống kê nhanh -->
      <div class="row">
        <!-- Card tổng chi tiêu tháng này -->
        <div class="col-md-4 grid-margin stretch-card">
          <div class="card bg-gradient-primary text-white">
            <div class="card-body">
              <div class="d-flex justify-content-between align-items-start">
                <div>
                  <h4 class="font-weight-bold mb-1">Chi tiêu tháng này</h4>
                  <h2 class="font-weight-bold mb-1">
                    <?php echo number_format($data['stats']['total_this_month'], 0, ',', '.'); ?>₫
                  </h2>
                  <p class="mb-0">
                    <?php if($data['stats']['month_compare'] > 0): ?>
                      <span class="badge badge-danger">
                        <i class="mdi mdi-arrow-up"></i> <?php echo abs($data['stats']['month_compare']); ?>%
                      </span>
                      <span class="ml-1">so với tháng trước</span>
                    <?php elseif($data['stats']['month_compare'] < 0): ?>
                      <span class="badge badge-success">
                        <i class="mdi mdi-arrow-down"></i> <?php echo abs($data['stats']['month_compare']); ?>%
                      </span>
                      <span class="ml-1">so với tháng trước</span>
                    <?php else: ?>
                      <span class="badge badge-light">
                        <i class="mdi mdi-minus"></i> 0%
                      </span>
                      <span class="ml-1">so với tháng trước</span>
                    <?php endif; ?>
                  </p>
                </div>
                <div class="icon-container">
                  <i class="mdi mdi-cash-multiple icon-lg"></i>
                </div>
              </div>
            </div>
          </div>
        </div>
        
        <!-- Card chi tiêu hôm nay -->
        <div class="col-md-4 grid-margin stretch-card">
          <div class="card bg-gradient-success text-white">
            <div class="card-body">
              <div class="d-flex justify-content-between align-items-start">
                <div>
                  <h4 class="font-weight-bold mb-1">Chi tiêu hôm nay</h4>
                  <h2 class="font-weight-bold mb-1">
                    <?php echo number_format($data['stats']['today'], 0, ',', '.'); ?>₫
                  </h2>
                  <p class="mb-0">
                    <span class="text-light"><?php echo date('d/m/Y'); ?></span>
                  </p>
                </div>
                <div class="icon-container">
                  <i class="mdi mdi-calendar-today icon-lg"></i>
                </div>
              </div>
            </div>
          </div>
        </div>
        
        <!-- Card dự báo tháng -->
        <div class="col-md-4 grid-margin stretch-card">
          <div class="card bg-gradient-info text-white">
            <div class="card-body">
              <div class="d-flex justify-content-between align-items-start">
                <div>
                  <h4 class="font-weight-bold mb-1">Dự báo tháng này</h4>
                  <h2 class="font-weight-bold mb-1">
                    <?php echo number_format($data['stats']['forecast'], 0, ',', '.'); ?>₫
                  </h2>
                  <p class="mb-0">
                    <span class="text-light">Dựa trên xu hướng hiện tại</span>
                  </p>
                </div>
                <div class="icon-container">
                  <i class="mdi mdi-chart-line icon-lg"></i>
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
    
    <!-- Modal xác nhận xóa -->
    <div class="modal fade" id="deleteModal" tabindex="-1" role="dialog" aria-labelledby="deleteModalLabel" aria-hidden="true">
      <div class="modal-dialog" role="document">
        <div class="modal-content">
          <div class="modal-header">
            <h5 class="modal-title" id="deleteModalLabel">Xác nhận xóa</h5>
            <button type="button" class="close" data-dismiss="modal" aria-label="Close">
              <span aria-hidden="true">&times;</span>
            </button>
          </div>
          <div class="modal-body">
            Bạn có chắc chắn muốn xóa khoản chi tiêu này? Hành động này không thể hoàn tác.
          </div>
          <div class="modal-footer">
            <button type="button" class="btn btn-secondary" data-dismiss="modal">Hủy bỏ</button>
            <form id="deleteForm" action="<?php echo BASEURL; ?>/expenses/delete" method="post">
              <input type="hidden" name="expense_id" id="deleteExpenseId">
              <button type="submit" class="btn btn-danger">Xác nhận xóa</button>
            </form>
          </div>
        </div>
      </div>
    </div>
    
    <?php require APPROOT . '/views/layouts/footer.php'; ?>
  </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
  // Script xử lý khi người dùng click vào nút xóa
  const deleteLinks = document.querySelectorAll('.delete-item');
  deleteLinks.forEach(link => {
    link.addEventListener('click', function(e) {
      const id = this.getAttribute('data-id');
      document.getElementById('deleteExpenseId').value = id;
    });
  });
  
  // Khởi tạo tooltip cho các phần tử có data-toggle="tooltip"
  $('[data-toggle="tooltip"]').tooltip();
  
  // Khởi tạo datepicker nếu có
  if ($.fn.datepicker) {
    $('.datepicker').datepicker({
      format: 'yyyy-mm-dd',
      todayBtn: 'linked',
      autoclose: true,
      language: 'vi'
    });
  }
  
  // Khởi tạo biểu đồ nếu có dữ liệu
  if (document.getElementById('expensesByCategoryChart')) {
    const categoryData = <?php echo json_encode($data['charts']['by_category'] ?? []); ?>;
    
    if (categoryData && categoryData.labels && categoryData.labels.length > 0) {
      const categoryCtx = document.getElementById('expensesByCategoryChart').getContext('2d');
      new Chart(categoryCtx, {
        type: 'doughnut',
        data: {
          labels: categoryData.labels,
          datasets: [{
            data: categoryData.values,
            backgroundColor: categoryData.colors,
            borderWidth: 1
          }]
        },
        options: {
          responsive: true,
          maintainAspectRatio: false,
          legend: {
            position: 'right',
            labels: {
              boxWidth: 12,
              padding: 10
            }
          },
          tooltips: {
            callbacks: {
              label: function(tooltipItem, data) {
                const dataset = data.datasets[tooltipItem.datasetIndex];
                const total = dataset.data.reduce((prev, curr) => prev + curr, 0);
                const currentValue = dataset.data[tooltipItem.index];
                const percentage = Math.round((currentValue / total) * 100);
                return `${data.labels[tooltipItem.index]}: ${formatCurrency(currentValue)} (${percentage}%)`;
              }
            }
          }
        }
      });
    } else {
      document.getElementById('expensesByCategoryChart').parentNode.innerHTML = `
        <div class="text-center py-5 text-muted">
          <i class="mdi mdi-chart-pie" style="font-size: 3rem;"></i>
          <p class="mt-3">Không có dữ liệu chi tiêu theo danh mục</p>
        </div>
      `;
    }
  }
  
  if (document.getElementById('expensesByTimeChart')) {
    const timeData = <?php echo json_encode($data['charts']['by_time'] ?? []); ?>;
    
    if (timeData && timeData.labels && timeData.labels.length > 0) {
      const timeCtx = document.getElementById('expensesByTimeChart').getContext('2d');
      new Chart(timeCtx, {
        type: 'line',
        data: {
          labels: timeData.labels,
          datasets: [{
            label: 'Chi tiêu',
            data: timeData.values,
            backgroundColor: 'rgba(255, 99, 132, 0.2)',
            borderColor: 'rgba(255, 99, 132, 1)',
            borderWidth: 2,
            tension: 0.4,
            pointBackgroundColor: 'rgba(255, 99, 132, 1)',
            pointRadius: 4
          }]
        },
        options: {
          responsive: true,
          maintainAspectRatio: false,
          scales: {
            yAxes: [{
              ticks: {
                beginAtZero: true,
                callback: function(value) {
                  return formatCurrencyShort(value);
                }
              },
              gridLines: {
                color: 'rgba(0, 0, 0, 0.05)',
                zeroLineColor: 'rgba(0, 0, 0, 0.1)'
              }
            }],
            xAxes: [{
              gridLines: {
                color: 'rgba(0, 0, 0, 0.05)',
                zeroLineColor: 'rgba(0, 0, 0, 0.1)'
              }
            }]
          },
          tooltips: {
            callbacks: {
              label: function(tooltipItem, data) {
                return `Chi tiêu: ${formatCurrency(tooltipItem.yLabel)}`;
              }
            }
          }
        }
      });
    } else {
      document.getElementById('expensesByTimeChart').parentNode.innerHTML = `
        <div class="text-center py-5 text-muted">
          <i class="mdi mdi-chart-line" style="font-size: 3rem;"></i>
          <p class="mt-3">Không có dữ liệu chi tiêu theo thời gian</p>
        </div>
      `;
    }
  }
  
  // Format số tiền cho dễ đọc
  function formatCurrency(amount) {
    return new Intl.NumberFormat('vi-VN', {
      style: 'currency',
      currency: 'VND',
      minimumFractionDigits: 0
    }).format(amount);
  }
  
  // Format số tiền rút gọn
  function formatCurrencyShort(amount) {
    if (amount >= 1000000000) {
      return (amount / 1000000000).toFixed(1) + ' tỷ';
    } else if (amount >= 1000000) {
      return (amount / 1000000).toFixed(1) + ' tr';
    } else if (amount >= 1000) {
      return (amount / 1000).toFixed(0) + ' k';
    }
    return amount;
  }

  // Tìm tất cả các liên kết chi tiết
  var detailLinks = document.querySelectorAll('a[href*="/expenses/view/"]');
  
  // Sửa các liên kết cũ sang định dạng mới
  detailLinks.forEach(function(link) {
      var href = link.getAttribute('href');
      link.setAttribute('href', href.replace('/expenses/view/', '/expenses/detail/'));
  });
  
  console.log("Đã cập nhật " + detailLinks.length + " liên kết chi tiết");
});

// Hàm filter theo category
function filterByCategory(categoryId) {
  document.getElementById('hidden_category').value = categoryId;
  document.querySelector('form').submit();
}

// Hàm filter theo ngày
function applyDateFilter() {
  const startDate = document.getElementById('start_date').value;
  const endDate = document.getElementById('end_date').value;
  
  document.getElementById('hidden_start_date').value = startDate;
  document.getElementById('hidden_end_date').value = endDate;
  document.querySelector('form').submit();
}

// Hàm tạo phím tắt nhanh cho thời gian
function setDateRange(range) {
  const now = new Date();
  let startDate, endDate;
  
  switch(range) {
    case 'today':
      startDate = now.toISOString().split('T')[0];
      endDate = now.toISOString().split('T')[0];
      break;
    case 'yesterday':
      const yesterday = new Date(now);
      yesterday.setDate(yesterday.getDate() - 1);
      startDate = yesterday.toISOString().split('T')[0];
      endDate = yesterday.toISOString().split('T')[0];
      break;
    case 'week':
      const firstDay = new Date(now);
      firstDay.setDate(firstDay.getDate() - firstDay.getDay());
      startDate = firstDay.toISOString().split('T')[0];
      endDate = now.toISOString().split('T')[0];
      break;
    case 'month':
      startDate = new Date(now.getFullYear(), now.getMonth(), 1).toISOString().split('T')[0];
      endDate = new Date(now.getFullYear(), now.getMonth() + 1, 0).toISOString().split('T')[0];
      break;
    case 'year':
      startDate = new Date(now.getFullYear(), 0, 1).toISOString().split('T')[0];
      endDate = now.toISOString().split('T')[0];
      break;
  }
  
  document.getElementById('start_date').value = startDate;
  document.getElementById('end_date').value = endDate;
}
</script>

<?php require APPROOT . '/views/layouts/footer_scripts.php'; ?>