<?php
require_once APPROOT . '/views/layouts/header.php';
require_once APPROOT . '/views/layouts/sidebar.php';
?>

<div class="container-fluid page-body-wrapper">
  <?php require_once APPROOT . '/views/partials/_settings-panel.php'; ?>
  <?php require_once APPROOT . '/views/layouts/navbar.php'; ?>
  
  <div class="main-panel">
    <div class="content-wrapper">
      <?php require_once APPROOT . '/views/partials/_message.php'; ?>
      
      <!-- Chi tiết chi tiêu -->
      <div class="page-header">
        <h3 class="page-title">Chi tiết khoản chi tiêu</h3>
        <nav aria-label="breadcrumb">
          <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="<?php echo BASEURL; ?>/dashboard">Dashboard</a></li>
            <li class="breadcrumb-item"><a href="<?php echo BASEURL; ?>/expenses">Danh sách chi tiêu</a></li>
            <li class="breadcrumb-item active" aria-current="page">Chi tiết</li>
          </ol>
        </nav>
      </div>
      
      <div class="row">
        <div class="col-md-8 grid-margin stretch-card">
          <div class="card">
            <div class="card-body">
              <h4 class="card-title">Thông tin chi tiêu</h4>
              
              <?php if(isset($expense) && !empty($expense)): ?>
                <div class="table-responsive">
                  <table class="table table-bordered">
                    <tbody>
                      <tr>
                        <th width="30%">ID chi tiêu</th>
                        <td><?php echo $expense->expense_id ?? ($expense->id ?? 'N/A'); ?></td>
                      </tr>
                      <tr>
                        <th width="30%">Loại chi tiêu</th>
                        <td><?php echo $expense->category_name ?? 'N/A'; ?></td>
                      </tr>
                      <tr>
                        <th>Số tiền</th>
                        <td><?php echo number_format($expense->amount ?? 0, 0, ',', '.'); ?> VNĐ</td>
                      </tr>
                      <tr>
                        <th>Ngày chi tiêu</th>
                        <td><?php echo isset($expense->expense_date) ? date('d/m/Y', strtotime($expense->expense_date)) : 'N/A'; ?></td>
                      </tr>
                      <tr>
                        <th>Mô tả</th>
                        <td><?php echo $expense->description ?? 'Không có mô tả'; ?></td>
                      </tr>
                      <tr>
                        <th>Trạng thái</th>
                        <td>
                          <?php if(isset($expense->status_name)): ?>
                            <?php if($expense->status_name == 'Đã thanh toán'): ?>
                              <span class="badge badge-success">
                                <i class="mdi mdi-check"></i> Đã thanh toán
                              </span>
                            <?php elseif($expense->status_name == 'Chờ thanh toán'): ?>
                              <span class="badge badge-warning">
                                <i class="mdi mdi-clock"></i> Chờ thanh toán
                              </span>
                            <?php else: ?>
                              <span class="badge badge-secondary">
                                <i class="mdi mdi-help-circle"></i> <?php echo $expense->status_name; ?>
                              </span>
                            <?php endif; ?>
                          <?php else: ?>
                            <span class="badge badge-secondary">Không xác định</span>
                          <?php endif; ?>
                        </td>
                      </tr>
                      <?php if(isset($expense->receipt_url) && !empty($expense->receipt_url)): ?>
                      <tr>
                        <th>Hình ảnh hóa đơn</th>
                        <td>
                          <a href="<?php echo BASEURL . '/' . $expense->receipt_url; ?>" target="_blank">
                            <img src="<?php echo BASEURL . '/' . $expense->receipt_url; ?>" 
                                 class="img-fluid" style="max-height: 300px;" alt="Hóa đơn">
                          </a>
                        </td>
                      </tr>
                      <?php endif; ?>
                    </tbody>
                  </table>
                </div>
                
                <div class="mt-4">
                  <a href="<?php echo BASEURL; ?>/expenses/edit/<?php echo $expense->expense_id ?? ($expense->id ?? ''); ?>" class="btn btn-primary mr-2">
                    <i class="mdi mdi-pencil"></i> Sửa chi tiêu
                  </a>
                  <button type="button" class="btn btn-danger" data-toggle="modal" data-target="#deleteModal">
                    <i class="mdi mdi-delete"></i> Xóa chi tiêu
                  </button>
                  <a href="<?php echo BASEURL; ?>/expenses" class="btn btn-light">
                    <i class="mdi mdi-arrow-left"></i> Quay lại
                  </a>
                </div>
              <?php else: ?>
                <div class="alert alert-danger">
                  <i class="mdi mdi-alert-circle-outline mr-2"></i>
                  Không tìm thấy thông tin chi tiêu
                </div>
                <a href="<?php echo BASEURL; ?>/expenses" class="btn btn-primary">
                  <i class="mdi mdi-arrow-left"></i> Quay lại danh sách
                </a>
              <?php endif; ?>
            </div>
          </div>
        </div>
        
        <div class="col-md-4 grid-margin stretch-card">
          <div class="card">
            <div class="card-body">
              <h4 class="card-title">Thông tin thanh toán</h4>
              
              <?php if(isset($payment) && !empty($payment)): ?>
                <div class="table-responsive">
                  <table class="table table-bordered">
                    <tbody>
                      <tr>
                        <th>Phương thức</th>
                        <td><?php echo $payment->method_name ?? 'N/A'; ?></td>
                      </tr>
                      <tr>
                        <th>Ngày thanh toán</th>
                        <td><?php echo isset($payment->payment_date) ? date('d/m/Y', strtotime($payment->payment_date)) : 'N/A'; ?></td>
                      </tr>
                    </tbody>
                  </table>
                </div>
              <?php else: ?>
                <div class="alert alert-info">
                  <i class="mdi mdi-information mr-2"></i>
                  Không có thông tin thanh toán
                </div>
              <?php endif; ?>
            </div>
          </div>
          
          <!-- Thống kê chi tiêu -->
          <div class="card mt-4">
            <div class="card-body">
              <h4 class="card-title">Thống kê</h4>
              
              <div class="stats-item mb-3">
                <p class="mb-1">Chi tiêu trung bình tháng</p>
                <h4 class="font-weight-bold">
                  <?php echo number_format($stats['monthly_avg'] ?? 0, 0, ',', '.'); ?> VNĐ
                </h4>
              </div>
              
              <div class="stats-item">
                <p class="mb-1">Chi tiêu trung bình cho danh mục này</p>
                <h4 class="font-weight-bold">
                  <?php echo number_format($stats['category_avg'] ?? 0, 0, ',', '.'); ?> VNĐ
                </h4>
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
            <form action="<?php echo BASEURL; ?>/expenses/delete" method="post">
              <input type="hidden" name="expense_id" value="<?php echo $expense->expense_id ?? ($expense->id ?? ''); ?>">
              <button type="submit" class="btn btn-danger">Xác nhận xóa</button>
            </form>
          </div>
        </div>
      </div>
    </div>
    
    <?php require_once APPROOT . '/views/layouts/footer.php'; ?>
  </div>
</div>

<?php require_once APPROOT . '/views/layouts/footer_scripts.php'; ?>