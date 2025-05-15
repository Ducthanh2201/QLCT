<?php
require APPROOT . '/views/layouts/header.php';
require APPROOT . '/views/layouts/sidebar.php';
?>

<div class="container-fluid page-body-wrapper">
  <?php require APPROOT . '/views/partials/_settings-panel.php'; ?>
  <?php require APPROOT . '/views/layouts/navbar.php'; ?>
  
  <div class="main-panel">
    <div class="content-wrapper">
      <?php require APPROOT . '/views/partials/_message.php'; ?>
      
      <div class="page-header">
        <h3 class="page-title">Quản lý phương thức thanh toán</h3>
        <nav aria-label="breadcrumb">
          <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="<?php echo BASEURL; ?>/dashboard">Dashboard</a></li>
            <li class="breadcrumb-item active" aria-current="page">Phương thức thanh toán</li>
          </ol>
        </nav>
      </div>
      
      <div class="row">
        <!-- Tài khoản ngân hàng -->
        <div class="col-md-6 grid-margin stretch-card">
          <div class="card">
            <div class="card-body">
              <div class="d-flex justify-content-between align-items-center mb-4">
                <h4 class="card-title">Tài khoản ngân hàng</h4>
                <a href="<?php echo BASEURL; ?>/payment-methods/add-bank-account" class="btn btn-primary btn-sm">
                  <i class="mdi mdi-plus-circle"></i> Thêm mới
                </a>
              </div>
              
              <?php if(empty($data['bank_accounts'])): ?>
                <div class="alert alert-info">
                  Bạn chưa thêm tài khoản ngân hàng nào.
                </div>
              <?php else: ?>
                <div class="table-responsive">
                  <table class="table table-hover">
                    <thead>
                      <tr>
                        <th>Ngân hàng</th>
                        <th>Số tài khoản</th>
                        <th>Chủ tài khoản</th>
                        <th>Trạng thái</th>
                        <th>Thao tác</th>
                      </tr>
                    </thead>
                    <tbody>
                      <?php foreach($data['bank_accounts'] as $account): ?>
                        <tr>
                          <td><?php echo $account->bank_name; ?></td>
                          <td><?php echo $account->account_number; ?></td>
                          <td><?php echo $account->account_holder; ?></td>
                          <td>
                            <?php if($account->is_default): ?>
                              <span class="badge badge-success">Mặc định</span>
                            <?php endif; ?>
                          </td>
                          <td>
                            <a href="<?php echo BASEURL; ?>/payment-methods/edit-bank-account/<?php echo $account->id; ?>" class="btn btn-outline-primary btn-sm">
                              <i class="mdi mdi-pencil"></i>
                            </a>
                            <button type="button" class="btn btn-outline-danger btn-sm" onclick="confirmDelete('bank-account', <?php echo $account->id; ?>)">
                              <i class="mdi mdi-delete"></i>
                            </button>
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
        
        <!-- Thẻ thanh toán -->
        <div class="col-md-6 grid-margin stretch-card">
          <div class="card">
            <div class="card-body">
              <div class="d-flex justify-content-between align-items-center mb-4">
                <h4 class="card-title">Thẻ thanh toán</h4>
                <a href="<?php echo BASEURL; ?>/payment-methods/add-payment-card" class="btn btn-primary btn-sm">
                  <i class="mdi mdi-plus-circle"></i> Thêm mới
                </a>
              </div>
              
              <?php if(empty($data['cards'])): ?>
                <div class="alert alert-info">
                  Bạn chưa thêm thẻ thanh toán nào.
                </div>
              <?php else: ?>
                <div class="table-responsive">
                  <table class="table table-hover">
                    <thead>
                      <tr>
                        <th>Ngân hàng</th>
                        <th>Số thẻ</th>
                        <th>Loại thẻ</th>
                        <th>Trạng thái</th>
                        <th>Thao tác</th>
                      </tr>
                    </thead>
                    <tbody>
                      <?php foreach($data['cards'] as $card): ?>
                        <tr>
                          <td><?php echo $card->bank_name; ?></td>
                          <td>**** **** **** <?php echo substr($card->card_number, -4); ?></td>
                          <td><?php echo $card->card_type; ?></td>
                          <td>
                            <?php if($card->is_default): ?>
                              <span class="badge badge-success">Mặc định</span>
                            <?php endif; ?>
                          </td>
                          <td>
                            <a href="<?php echo BASEURL; ?>/payment-methods/edit-payment-card/<?php echo $card->id; ?>" class="btn btn-outline-primary btn-sm">
                              <i class="mdi mdi-pencil"></i>
                            </a>
                            <button type="button" class="btn btn-outline-danger btn-sm" onclick="confirmDelete('payment-card', <?php echo $card->id; ?>)">
                              <i class="mdi mdi-delete"></i>
                            </button>
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
    
    <?php require APPROOT . '/views/layouts/footer.php'; ?>
  </div>
</div>

<script>
function confirmDelete(type, id) {
  if(confirm('Bạn có chắc chắn muốn xóa?')) {
    window.location.href = '<?php echo BASEURL; ?>/payment-methods/delete-' + type + '/' + id;
  }
}
</script>

<?php require APPROOT . '/views/layouts/footer_scripts.php'; ?>