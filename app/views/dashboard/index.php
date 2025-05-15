<?php require APPROOT . '/views/layouts/header.php'; ?>
<?php require APPROOT . '/views/layouts/sidebar.php'; ?>

<div class="container-fluid page-body-wrapper">
  <?php require APPROOT . '/views/partials/_settings-panel.php'; ?>
  <?php require APPROOT . '/views/layouts/navbar.php'; ?>
  
  <!-- Phần nội dung chính -->
  <div class="main-panel">
    <div class="content-wrapper pb-0">
      <div class="page-header flex-wrap">
        <div class="header-left">
          <button class="btn btn-primary mb-2 mb-md-0 mr-2" onclick="window.location.href='<?php echo BASEURL; ?>/expenses/create'"> 
            Tạo khoản chi mới 
          </button>
          <button class="btn btn-outline-primary bg-white mb-2 mb-md-0" onclick="window.location.href='<?php echo BASEURL; ?>/incomes/create'"> 
            Tạo khoản thu mới 
          </button>
        </div>
        <div class="header-right d-flex flex-wrap mt-2 mt-sm-0">
          <div class="d-flex align-items-center">
            <a href="<?php echo BASEURL; ?>/dashboard">
              <p class="m-0 pr-3">Tổng Quan</p>
            </a>
            <a class="pl-3 mr-4" href="<?php echo BASEURL; ?>/reports">
              <p class="m-0">Báo Cáo</p>
            </a>
          </div>
          <button type="button" class="btn btn-primary mt-2 mt-sm-0 btn-icon-text" onclick="window.location.href='<?php echo BASEURL; ?>/budget/create'">
            <i class="mdi mdi-plus-circle"></i> Thiết lập ngân sách </button>
        </div>
      </div>

      <!-- Hàng đầu tiên - Tổng quan tài chính -->
      <div class="row">
        <div class="col-xl-9 stretch-card grid-margin">
          <div class="card">
            <div class="card-body">
              <div class="d-flex justify-content-between flex-wrap">
                <div>
                  <div class="card-title mb-0">Thống Kê Tài Chính</div>
                  <h3 class="font-weight-bold mb-0">
                    <?php echo number_format($data['total_balance'] ?? 0, 0, ',', '.'); ?>đ
                  </h3>
                </div>
                <div>
                  <div class="d-flex flex-wrap pt-2 justify-content-between sales-header-right">
                    <div class="d-flex mr-5">
                      <button type="button" class="btn btn-social-icon btn-outline-sales">
                        <i class="mdi mdi-cash-plus"></i>
                      </button>
                      <div class="pl-2">
                        <h4 class="mb-0 font-weight-semibold head-count"> 
                          <?php echo number_format($data['total_income'] ?? 0, 0, ',', '.'); ?>đ 
                        </h4>
                        <span class="font-10 font-weight-semibold text-muted">TỔNG THU NHẬP</span>
                      </div>
                    </div>
                    <div class="d-flex mr-3 mt-2 mt-sm-0">
                      <button type="button" class="btn btn-social-icon btn-outline-sales profit">
                        <i class="mdi mdi-cash-minus text-info"></i>
                      </button>
                      <div class="pl-2">
                        <h4 class="mb-0 font-weight-semibold head-count"> 
                          <?php echo number_format($data['total_expense'] ?? 0, 0, ',', '.'); ?>đ 
                        </h4>
                        <span class="font-10 font-weight-semibold text-muted">TỔNG CHI TIÊU</span>
                      </div>
                    </div>
                  </div>
                </div>
              </div>
              <p class="text-muted font-13 mt-2 mt-sm-0"> 
                Biểu đồ thể hiện thu nhập và chi tiêu trong 30 ngày qua 
                <a class="text-muted font-13" href="<?php echo BASEURL; ?>/reports"><u>Xem chi tiết</u></a>
              </p>
              <div class="flot-chart-wrapper">
                <div id="flotChart" class="flot-chart">
                  <canvas class="flot-base"></canvas>
                </div>
              </div>
            </div>
          </div>
        </div>
        <div class="col-xl-3 stretch-card grid-margin">
          <div class="card card-img">
            <div class="card-body d-flex align-items-center">
              <div class="text-white">
                <h1 class="font-20 font-weight-semibold mb-0">Tiến độ ngân sách</h1>
                <h1 class="font-20 font-weight-semibold">tháng <?php echo date('m/Y'); ?></h1>
                <p>Đã chi: <strong><?php echo $data['budget_spent_percent'] ?? 0; ?>%</strong> ngân sách</p>
                <div class="progress progress-md">
                  <div class="progress-bar <?php echo $data['budget_progress_class'] ?? 'bg-success'; ?>" 
                    role="progressbar" 
                    style="width: <?php echo $data['budget_spent_percent'] ?? 0; ?>%" 
                    aria-valuenow="<?php echo $data['budget_spent_percent'] ?? 0; ?>" 
                    aria-valuemin="0" 
                    aria-valuemax="100">
                  </div>
                </div>
                <p class="font-10 font-weight-semibold mt-2">
                  <?php echo $data['budget_message'] ?? 'Chưa có dữ liệu ngân sách.'; ?>
                </p>
                <button class="btn bg-white font-12" onclick="window.location.href='<?php echo BASEURL; ?>/budget'">
                  Chi tiết ngân sách
                </button>
              </div>
            </div>
          </div>
        </div>
      </div>

      <!-- Hàng thứ hai - Thống kê chi tiêu và thu nhập -->
      <div class="row">
        <div class="col-sm-6 stretch-card grid-margin">
          <div class="card">
            <div class="card-body">
              <div class="d-flex justify-content-between">
                <div class="card-title">Thu nhập <small class="d-block text-muted">
                  <?php echo $data['date_range'] ?? date('01/m/Y') . ' - ' . date('d/m/Y'); ?>
                </small>
                </div>
                <div class="d-flex text-muted font-20">
                  <i class="mdi mdi-printer mouse-pointer" onclick="window.print()"></i>
                  <i class="mdi mdi-help-circle-outline ml-2 mouse-pointer" title="Thông tin về thu nhập trong tháng"></i>
                </div>
              </div>
              <h3 class="font-weight-bold mb-0"> 
                <?php echo number_format($data['monthly_income'] ?? 0, 0, ',', '.'); ?>đ 
                <span class="text-<?php echo (($data['income_trend'] ?? 0) >= 0) ? 'success' : 'danger'; ?> h5">
                  <?php echo $data['income_trend'] ?? 0; ?>%
                  <i class="mdi mdi-arrow-<?php echo (($data['income_trend'] ?? 0) >= 0) ? 'up' : 'down'; ?>"></i>
                </span>
              </h3>
              <span class="text-muted font-13">So với tháng trước</span>
              <div class="line-chart-wrapper">
                <canvas id="incomeLineChart" height="80"></canvas>
              </div>
            </div>
          </div>
        </div>
        <div class="col-sm-6 stretch-card grid-margin">
          <div class="card">
            <div class="card-body">
              <div class="d-flex justify-content-between">
                <div class="card-title">Chi tiêu <small class="d-block text-muted">
                  <?php echo $data['date_range'] ?? date('01/m/Y') . ' - ' . date('d/m/Y'); ?>
                </small>
                </div>
                <div class="d-flex text-muted font-20">
                  <i class="mdi mdi-printer mouse-pointer" onclick="window.print()"></i>
                  <i class="mdi mdi-help-circle-outline ml-2 mouse-pointer" title="Thông tin về chi tiêu trong tháng"></i>
                </div>
              </div>
              <h3 class="font-weight-bold mb-0">
                <?php echo number_format($data['monthly_expense'] ?? 0, 0, ',', '.'); ?>đ
                <span class="text-<?php echo (($data['expense_trend'] ?? 0) <= 0) ? 'success' : 'danger'; ?> h5">
                  <?php echo $data['expense_trend'] ?? 0; ?>%
                  <i class="mdi mdi-arrow-<?php echo (($data['expense_trend'] ?? 0) > 0) ? 'up' : 'down'; ?>"></i>
                </span>
              </h3>
              <span class="text-muted font-13">So với tháng trước</span>
              <div class="bar-chart-wrapper">
                <canvas id="expenseBarChart" height="80"></canvas>
              </div>
            </div>
          </div>
        </div>
      </div>

      <!-- Hàng thứ ba - Các khoản chi gần đây -->
      <div class="row">
        <?php if (!empty($data['recent_expenses'] ?? [])): ?>
          <?php foreach ($data['recent_expenses'] as $expense): ?>
            <div class="col-sm-4 stretch-card grid-margin">
              <div class="card">
                <div class="card-body p-0">
                  <img class="img-fluid w-100" 
                    src="<?php echo BASEURL; ?>/assets/images/categories/<?php echo $expense['category_image']; ?>" 
                    alt="<?php echo $expense['category_name']; ?>" />
                </div>
                <div class="card-body px-3 text-dark">
                  <div class="d-flex justify-content-between">
                    <p class="text-muted font-13 mb-0"><?php echo strtoupper($expense['category_name']); ?></p>
                    <a href="<?php echo BASEURL; ?>/expenses/edit/<?php echo $expense['id']; ?>">
                      <i class="mdi mdi-pencil"></i>
                    </a>
                  </div>
                  <h5 class="font-weight-semibold"><?php echo $expense['description']; ?></h5>
                  <div class="d-flex justify-content-between font-weight-semibold">
                    <p class="mb-0">
                      <i class="mdi mdi-calendar-clock text-primary pr-1"></i>
                      <?php echo date('d/m/Y', strtotime($expense['date'])); ?>
                    </p>
                    <p class="mb-0 text-danger"><?php echo number_format($expense['amount'], 0, ',', '.'); ?>đ</p>
                  </div>
                </div>
              </div>
            </div>
          <?php endforeach; ?>
        <?php else: ?>
          <div class="col-12">
            <div class="card">
              <div class="card-body">
                <p class="text-center">Chưa có khoản chi tiêu nào được ghi nhận.</p>
                <div class="text-center">
                  <a href="<?php echo BASEURL; ?>/expenses/create" class="btn btn-primary">Thêm khoản chi tiêu mới</a>
                </div>
              </div>
            </div>
          </div>
        <?php endif; ?>
      </div>

      <!-- Hàng thứ tư - Thống kê chi tiết và bảng -->
      <div class="row">
        <div class="col-xl-4 grid-margin">
          <div class="card card-stat stretch-card mb-3">
            <div class="card-body">
              <div class="d-flex justify-content-between">
                <div class="text-white">
                  <h3 class="font-weight-bold mb-0">
                    <?php echo number_format($data['this_month'], 0, ',', '.'); ?>đ
                  </h3>
                  <h6>Tháng này</h6>
                  <div class="badge badge-<?php echo ($data['month_compare'] >= 0) ? 'success' : 'danger'; ?>">
                    <?php echo $data['month_compare']; ?>%
                  </div>
                </div>
                <div class="flot-bar-wrapper">
                  <div id="column-chart" class="flot-chart"></div>
                </div>
              </div>
            </div>
          </div>
          <div class="card stretch-card mb-3">
            <div class="card-body d-flex flex-wrap justify-content-between">
              <div>
                <h4 class="font-weight-semibold mb-1 text-black">Chi tiêu cố định</h4>
                <h6 class="text-muted">Các khoản chi tiêu định kỳ</h6>
              </div>
              <h3 class="text-danger font-weight-bold">
                <?php echo number_format($data['fixed_expenses'], 0, ',', '.'); ?>đ
              </h3>
            </div>
          </div>
          <div class="card stretch-card mb-3">
            <div class="card-body d-flex flex-wrap justify-content-between">
              <div>
                <h4 class="font-weight-semibold mb-1 text-black">Thu nhập cố định</h4>
                <h6 class="text-muted">Thu nhập hàng tháng</h6>
              </div>
              <h3 class="text-success font-weight-bold">
                <?php echo number_format($data['fixed_income'], 0, ',', '.'); ?>đ
              </h3>
            </div>
          </div>
          <div class="card mt-3">
            <div class="card-body d-flex flex-wrap justify-content-between">
              <div>
                <h4 class="font-weight-semibold mb-1 text-black">Số dư</h4>
                <h6 class="text-muted">Sau khi trừ chi phí cố định</h6>
              </div>
              <h3 class="text-<?php echo ($data['balance_after_fixed'] >= 0) ? 'success' : 'danger'; ?> font-weight-bold">
                <?php echo number_format($data['balance_after_fixed'], 0, ',', '.'); ?>đ
              </h3>
            </div>
          </div>
        </div>
        <div class="col-xl-8 stretch-card grid-margin">
          <div class="card">
            <div class="card-body pb-0">
              <h4 class="card-title mb-0">Chi tiêu theo danh mục</h4>
            </div>
            <div class="card-body p-0">
              <div class="table-responsive">
                <table class="table custom-table text-dark">
                  <thead>
                    <tr>
                      <th>Danh mục</th>
                      <th>% Ngân sách</th>
                      <th>Thực tế</th>
                      <th>Còn lại</th>
                    </tr>
                  </thead>
                  <tbody>
                    <?php if (!empty($data['category_expenses'])): ?>
                      <?php foreach ($data['category_expenses'] as $category): ?>
                        <tr>
                          <td>
                            <img src="<?php echo BASEURL; ?>/assets/images/categories/<?php echo $category['image']; ?>" class="mr-2" alt="image" />
                            <?php echo $category['name']; ?>
                          </td>
                          <td>
                            <div class="d-flex">
                              <span class="pr-2 d-flex align-items-center"><?php echo $category['budget_percent']; ?>%</span>
                              <div class="progress">
                                <div class="progress-bar bg-<?php echo $category['status']; ?>" 
                                  role="progressbar" 
                                  style="width: <?php echo $category['spent_percent']; ?>%">
                                </div>
                              </div>
                            </div>
                          </td>
                          <td><?php echo number_format($category['spent'], 0, ',', '.'); ?>đ</td>
                          <td><?php echo number_format($category['remaining'], 0, ',', '.'); ?>đ</td>
                        </tr>
                      <?php endforeach; ?>
                    <?php else: ?>
                      <tr>
                        <td colspan="4" class="text-center">Chưa có dữ liệu chi tiêu theo danh mục</td>
                      </tr>
                    <?php endif; ?>
                  </tbody>
                </table>
              </div>
              <a class="text-black font-13 d-block pt-2 pb-2 pb-lg-0 font-weight-bold pl-4" 
                 href="<?php echo BASEURL; ?>/reports/categories">Xem chi tiết</a>
            </div>
          </div>
        </div>
      </div>

      <!-- Hàng thứ năm - Biểu đồ tròn phân tích -->
      <div class="row">
        <div class="col-sm-12 stretch-card grid-margin">
          <div class="card">
            <div class="row">
              <div class="col-md-4">
                <div class="card border-0">
                  <div class="card-body">
                    <div class="card-title">Chi tiêu theo danh mục</div>
                    <div class="d-flex flex-wrap">
                      <div class="doughnut-wrapper w-50">
                        <canvas id="categoryDoughnutChart" width="100" height="100"></canvas>
                      </div>
                      <div id="category-chart-legend" class="pl-lg-3 rounded-legend align-self-center flex-grow legend-vertical legend-bottom-left"></div>
                    </div>
                  </div>
                </div>
              </div>
              <div class="col-md-4">
                <div class="card border-0">
                  <div class="card-body">
                    <div class="card-title">Chi tiêu theo thời gian</div>
                    <div class="d-flex flex-wrap">
                      <div class="doughnut-wrapper w-50">
                        <canvas id="timeDoughnutChart" width="100" height="100"></canvas>
                      </div>
                      <div id="time-chart-legend" class="pl-lg-3 rounded-legend align-self-center flex-grow legend-vertical legend-bottom-left"></div>
                    </div>
                  </div>
                </div>
              </div>
              <div class="col-md-4">
                <div class="card border-0">
                  <div class="card-body">
                    <div class="card-title">Chi tiêu theo phương thức</div>
                    <div class="d-flex flex-wrap">
                      <div class="doughnut-wrapper w-50">
                        <canvas id="methodDoughnutChart" width="100" height="100"></canvas>
                      </div>
                      <div id="method-chart-legend" class="pl-lg-3 rounded-legend align-self-center flex-grow legend-vertical legend-bottom-left"></div>
                    </div>
                  </div>
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>

      <!-- Hàng thứ sáu - Widget summary và upcoming events -->
      <div class="row">
        <div class="col-sm-6 col-xl-4 stretch-card grid-margin">
          <div class="card">
            <div class="card-body">
              <div class="card-title mb-2">Khoản chi tiêu sắp tới (<?php echo count($data['upcoming_expenses']); ?>)</div>
              <h3 class="mb-3"><?php echo date('d/m/Y'); ?></h3>
              
              <?php if (!empty($data['upcoming_expenses'])): ?>
                <?php foreach ($data['upcoming_expenses'] as $index => $expense): ?>
                  <div class="d-flex border-bottom <?php echo ($index === 0) ? 'border-top' : ''; ?> py-3">
                    <div class="form-check">
                      <label class="form-check-label">
                        <input type="checkbox" class="form-check-input" data-id="<?php echo $expense['id']; ?>" 
                          <?php echo ($expense['status'] == 'paid') ? 'checked' : ''; ?> />
                      </label>
                    </div>
                    <div class="pl-2">
                      <span class="font-12 text-muted">
                        <?php echo date('D, M d, H:i', strtotime($expense['due_date'])); ?>
                      </span>
                      <p class="m-0 text-black"><?php echo $expense['description']; ?> - 
                        <span class="text-<?php echo ($expense['status'] == 'paid') ? 'success' : 'danger'; ?>">
                          <?php echo number_format($expense['amount'], 0, ',', '.'); ?>đ
                        </span>
                      </p>
                    </div>
                  </div>
                <?php endforeach; ?>
              <?php else: ?>
                <div class="d-flex border-bottom border-top py-3">
                  <div class="pl-2">
                    <p class="m-0 text-black">Không có khoản chi tiêu sắp tới nào</p>
                  </div>
                </div>
              <?php endif; ?>

              <div class="d-flex pt-3 justify-content-center">
                <a href="<?php echo BASEURL; ?>/expenses/upcoming" class="btn btn-primary btn-sm">
                  Xem tất cả
                </a>
              </div>
            </div>
          </div>
        </div>
        <div class="col-sm-6 col-xl-4 stretch-card grid-margin">
          <div class="card">
            <div class="card-body">
              <div class="d-flex border-bottom mb-4 pb-2">
                <div class="hexagon">
                  <div class="hex-mid hexagon-warning">
                    <i class="mdi mdi-cash-multiple"></i>
                  </div>
                </div>
                <div class="pl-4">
                  <h4 class="font-weight-bold text-warning mb-0">
                    <?php echo number_format($data['total_budget'], 0, ',', '.'); ?>đ
                  </h4>
                  <h6 class="text-muted">Tổng ngân sách</h6>
                </div>
              </div>
              <div class="d-flex border-bottom mb-4 pb-2">
                <div class="hexagon">
                  <div class="hex-mid hexagon-danger">
                    <i class="mdi mdi-cash-minus"></i>
                  </div>
                </div>
                <div class="pl-4">
                  <h4 class="font-weight-bold text-danger mb-0">
                    <?php echo number_format($data['total_expense_month'], 0, ',', '.'); ?>đ
                  </h4>
                  <h6 class="text-muted">Đã chi tiêu tháng này</h6>
                </div>
              </div>
              <div class="d-flex border-bottom mb-4 pb-2">
                <div class="hexagon">
                  <div class="hex-mid hexagon-success">
                    <i class="mdi mdi-cash-plus"></i>
                  </div>
                </div>
                <div class="pl-4">
                  <h4 class="font-weight-bold text-success mb-0">
                    <?php echo number_format($data['total_income_month'], 0, ',', '.'); ?>đ
                  </h4>
                  <h6 class="text-muted">Thu nhập tháng này</h6>
                </div>
              </div>
              <div class="d-flex border-bottom mb-4 pb-2">
                <div class="hexagon">
                  <div class="hex-mid hexagon-info">
                    <i class="mdi mdi-wallet"></i>
                  </div>
                </div>
                <div class="pl-4">
                  <h4 class="font-weight-bold text-info mb-0">
                    <?php echo number_format($data['savings'], 0, ',', '.'); ?>đ
                  </h4>
                  <h6 class="text-muted">Tiết kiệm</h6>
                </div>
              </div>
              <div class="d-flex">
                <div class="hexagon">
                  <div class="hex-mid hexagon-primary">
                    <i class="mdi mdi-bank"></i>
                  </div>
                </div>
                <div class="pl-4">
                  <h4 class="font-weight-bold text-primary mb-0">
                    <?php echo $data['savings_percentage']; ?>%
                  </h4>
                  <h6 class="text-muted mb-0">Tỉ lệ tiết kiệm</h6>
                </div>
              </div>
            </div>
          </div>
        </div>
        <div class="col-sm-6 col-xl-4 stretch-card grid-margin">
          <div class="card">
            <div class="card-body">
              <div class="card-title">Mục tiêu tài chính</div>
              <?php if (!empty($data['financial_goals'])): ?>
                <?php foreach ($data['financial_goals'] as $goal): ?>
                  <div class="d-flex mt-3 mb-4">
                    <div class="flex-grow-1">
                      <h5><?php echo $goal['name']; ?></h5>
                      <div class="d-flex justify-content-between">
                        <p class="mb-0"><?php echo number_format($goal['current'], 0, ',', '.'); ?>đ / <?php echo number_format($goal['target'], 0, ',', '.'); ?>đ</p>
                        <p class="mb-0"><?php echo $goal['percentage']; ?>%</p>
                      </div>
                      <div class="progress progress-md mt-1">
                        <div class="progress-bar bg-<?php echo $goal['status']; ?>" style="width: <?php echo $goal['percentage']; ?>%"></div>
                      </div>
                    </div>
                  </div>
                <?php endforeach; ?>
              <?php else: ?>
                <div class="text-center py-4">
                  <p>Bạn chưa có mục tiêu tài chính nào</p>
                  <a href="<?php echo BASEURL; ?>/goals/create" class="btn btn-primary">Tạo mục tiêu mới</a>
                </div>
              <?php endif; ?>
            </div>
          </div>
        </div>
      </div>
    </div>

    <!-- Footer -->
    <?php require APPROOT . '/views/layouts/footer.php'; ?>
  </div>
</div>

<script>
// Dữ liệu biểu đồ từ controller
var incomeData = <?php echo json_encode($data['income_chart_data'] ?? []); ?>;
var expenseData = <?php echo json_encode($data['expense_chart_data'] ?? []); ?>;
var categoryData = <?php echo json_encode($data['category_chart_data'] ?? []); ?>;
var timeData = <?php echo json_encode($data['time_chart_data'] ?? []); ?>;
var methodData = <?php echo json_encode($data['method_chart_data'] ?? []); ?>;

// Khởi tạo biểu đồ khi trang đã sẵn sàng
document.addEventListener('DOMContentLoaded', function() {
  // Biểu đồ tổng quan thu chi
  if ($("#flotChart").length) {
    initFlotChart();
  }
  
  // Biểu đồ thu nhập
  if ($("#incomeLineChart").length) {
    initIncomeChart();
  }
  
  // Biểu đồ chi tiêu
  if ($("#expenseBarChart").length) {
    initExpenseChart();
  }
  
  // Biểu đồ phân loại
  if ($("#categoryDoughnutChart").length) {
    initDoughnutCharts();
  }
  
  // Khởi tạo xử lý sự kiện
  initCheckboxHandlers();
});

// Các hàm khởi tạo biểu đồ
function initFlotChart() {
  // Đảm bảo dữ liệu biểu đồ không rỗng
  if (!incomeData.length && !expenseData.length) {
    // Dữ liệu mẫu nếu không có dữ liệu thực
    const today = new Date();
    const dummyData = [];
    
    for (let i = 30; i >= 0; i--) {
      const date = new Date(today);
      date.setDate(today.getDate() - i);
      dummyData.push([date.getTime(), 0]);
    }
    
    incomeData = dummyData;
    expenseData = dummyData;
  }
  
  // Code khởi tạo biểu đồ Flot
  $.plot('#flotChart', [{
    data: incomeData,
    color: '#4CAF50',
    lines: {
      fillColor: 'rgba(76, 175, 80, 0.2)',
      fill: true
    }
  }, {
    data: expenseData,
    color: '#F44336',
    lines: {
      fillColor: 'rgba(244, 67, 54, 0.2)',
      fill: true
    }
  }], {
    series: {
      shadowSize: 0,
      lines: {
        show: true,
        lineWidth: 2,
        fill: true
      }
    },
    grid: {
      borderColor: '#eee',
      borderWidth: 1,
      hoverable: true,
      backgroundColor: 'transparent'
    },
    xaxis: {
      tickColor: '#eee',
      mode: "time",
      timeformat: "%d/%m",
      minTickSize: [1, "day"]
    },
    yaxis: {
      tickColor: '#eee',
      min: 0
    },
    tooltip: {
      show: true,
      content: "Ngày %x: %y đồng",
      xDateFormat: "%d/%m/%Y"
    }
  });
}

// Thêm các hàm khởi tạo biểu đồ còn lại...
// Khởi tạo biểu đồ với xử lý dữ liệu rỗng an toàn

// Xử lý sự kiện đánh dấu hoàn thành cho chi phí
function initCheckboxHandlers() {
  document.querySelectorAll('.form-check-input').forEach(checkbox => {
    checkbox.addEventListener('change', function() {
      const expenseId = this.getAttribute('data-id');
      const status = this.checked ? 'paid' : 'pending';
      
      // Gửi yêu cầu AJAX để cập nhật trạng thái
      fetch(`${BASEURL}/expenses/update-status`, {
        method: 'POST',
        headers: {
          'Content-Type': 'application/x-www-form-urlencoded',
        },
        body: `id=${expenseId}&status=${status}`
      })
      .then(response => response.json())
      .then(data => {
        if (data.success) {
          // Cập nhật giao diện nếu cần
          const textElement = this.closest('.d-flex').querySelector('.text-black span');
          textElement.className = status === 'paid' ? 'text-success' : 'text-danger';
        } else {
          alert('Có lỗi xảy ra khi cập nhật trạng thái.');
          this.checked = !this.checked; // Trả lại trạng thái cũ
        }
      })
      .catch(error => {
        console.error('Error:', error);
        alert('Có lỗi xảy ra khi cập nhật trạng thái.');
        this.checked = !this.checked; // Trả lại trạng thái cũ
      });
    });
  });
}
</script>