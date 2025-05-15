<footer class="footer">
  <div class="d-sm-flex justify-content-center justify-content-sm-between">
    <span class="text-muted d-block text-center text-sm-left d-sm-inline-block">Bản quyền © <?php echo date('Y'); ?></span>
    <span class="float-none float-sm-right d-block mt-1 mt-sm-0 text-center">Ứng dụng Quản lý Chi tiêu Cá nhân</span>
  </div>
</footer>

<!-- plugins:js -->
<script src="<?php echo BASEURL; ?>/assets/vendors/js/vendor.bundle.base.js"></script>
<!-- endinject -->
<!-- Plugin js for this page -->
<script src="<?php echo BASEURL; ?>/assets/vendors/jquery-bar-rating/jquery.barrating.min.js"></script>
<script src="<?php echo BASEURL; ?>/assets/vendors/chart.js/Chart.min.js"></script>
<script src="<?php echo BASEURL; ?>/assets/vendors/flot/jquery.flot.js"></script>
<script src="<?php echo BASEURL; ?>/assets/vendors/flot/jquery.flot.resize.js"></script>
<script src="<?php echo BASEURL; ?>/assets/vendors/flot/jquery.flot.categories.js"></script>
<script src="<?php echo BASEURL; ?>/assets/vendors/flot/jquery.flot.fillbetween.js"></script>
<script src="<?php echo BASEURL; ?>/assets/vendors/flot/jquery.flot.stack.js"></script>
<!-- Flot chart plugins -->
<script src="<?php echo BASEURL; ?>/assets/vendors/flot/jquery.flot.time.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/flot/0.8.3/jquery.flot.time.min.js"></script>
<!-- End plugin js for this page -->
<!-- inject:js -->
<script src="<?php echo BASEURL; ?>/assets/js/off-canvas.js"></script>
<script src="<?php echo BASEURL; ?>/assets/js/hoverable-collapse.js"></script>
<script src="<?php echo BASEURL; ?>/assets/js/misc.js"></script>
<script src="<?php echo BASEURL; ?>/assets/js/settings.js"></script>
<script src="<?php echo BASEURL; ?>/assets/js/todolist.js"></script>
<!-- endinject -->
<!-- Custom js for this page -->
<script src="<?php echo BASEURL; ?>/assets/js/dashboard.js"></script>
<!-- End custom js for this page -->

<?php if(isset($data['extra_js'])): ?>
  <?php foreach($data['extra_js'] as $js_file): ?>
    <script src="<?php echo BASEURL . $js_file; ?>"></script>
  <?php endforeach; ?>
<?php endif; ?>