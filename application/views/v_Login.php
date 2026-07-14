<!DOCTYPE html>
<html>

<head>
  <meta charset="utf-8">
  <meta http-equiv="X-UA-Compatible" content="IE=edge">
  <title><?= $title ?></title>
  <!-- Tell the browser to be responsive to screen width -->
  <meta content="width=device-width, initial-scale=1, maximum-scale=1, user-scalable=no" name="viewport">
  <!-- Bootstrap 3.3.7 -->
  <link rel="stylesheet" href="<?= base_url('assets/') ?>bower_components/bootstrap/dist/css/bootstrap.min.css">
  <!-- Font Awesome -->
  <link rel="stylesheet" href="<?= base_url('assets/') ?>bower_components/font-awesome/css/font-awesome.min.css">
  <!-- Ionicons -->
  <link rel="stylesheet" href="<?= base_url('assets/') ?>bower_components/Ionicons/css/ionicons.min.css">
  <!-- Theme style -->
  <link rel="stylesheet" href="<?= base_url('assets/') ?>dist/css/AdminLTE.min.css">
  <link rel="shortcut icon" type="text/css" href="<?php echo base_url('assets/dist/img/MI.png') ?>">

  <!-- Google Font -->
  <link rel="stylesheet" href="https://fonts.googleapis.com/css?family=Source+Sans+Pro:300,400,600,700,300italic,400italic,600italic">
  <!-- Custom Pastel Green Theme for Login -->
    <style>
    :root {
        --pastel-green: #27ae60;       /* Vibrant Emerald Green */
        --pastel-green-hover: #2ecc71; /* Brighter Green for Hover/Glow */
        --pastel-green-light: #d4edd9;
        --pastel-green-bg: #f4faf6;
        --text-dark: #1b5e20;
        --shadow-sm: 0 4px 15px rgba(0,0,0,0.05);
        --shadow-hover: 0 6px 20px rgba(39,174,96,0.2);
        --transition: all 0.3s ease;
    }
    body {
        background-color: var(--pastel-green-bg) !important;
    }
    .login-box-body {
        border-radius: 12px;
        box-shadow: var(--shadow-sm);
        border-top: 5px solid var(--pastel-green);
        transition: var(--transition);
    }
    .login-box-body:hover {
        transform: translateY(-3px);
        box-shadow: var(--shadow-hover);
    }
    .btn-primary {
        background-color: var(--pastel-green) !important;
        border-color: var(--pastel-green) !important;
        color: #fff !important;
        font-weight: 600;
        border-radius: 6px;
        transition: var(--transition);
    }
    .btn-primary:hover, .btn-primary:active, .btn-primary:focus {
        background-color: var(--pastel-green-hover) !important;
        border-color: var(--pastel-green-hover) !important;
        transform: translateY(-3px) scale(1.025);
        box-shadow: 0 0 20px rgba(46, 204, 113, 0.9), 0 0 40px rgba(46, 204, 113, 0.5), 0 8px 16px rgba(0,0,0,0.1);
        text-shadow: 0 0 8px rgba(255, 255, 255, 0.8);
        filter: brightness(1.05);
    }
  </style>
</head>

<body class="hold-transition login-page">
  <div class="login-box">
    <div class="login-logo">
      <img style="width: 40%" src="<?= base_url('assets/dist/img/MI.png') ?>">
    </div>
    <!-- /.login-logo -->
    <div class="login-box-body">
      <p class="login-box-msg">
        <span style="color:var(--text-dark); font-size: 0.85em; display: block; margin-bottom: 2px;">APLIKASI KEUANGAN SEKOLAH</span>
        <strong style="color:var(--text-dark); font-size: 1.25em; display: block;">MI DAAR EL-MUFLIHIN</strong>
      </p>
      <?= $this->session->flashdata('message'); ?>

      <form action="<?= base_url('Auth') ?>" method="post">
        <div class="form-group has-feedback">
          <input type="text" class="form-control" autocomplete="off" value="<?= set_value('email') ?>" name="email" id="email" placeholder="NIS / Email">
          <span class="glyphicon glyphicon-user form-control-feedback"></span>
          <?= form_error('email', '<small class="text-danger">', '</small>'); ?>
        </div>
        <div class="form-group has-feedback">
          <input type="password" name="password" class="form-control" placeholder="Password">
          <span class="glyphicon glyphicon-lock form-control-feedback"></span>
          <?= form_error('password', '<small class="text-danger">', '</small>'); ?>
        </div>
        <div class="row">
          <!-- /.col -->
          <div class="col-xs-12">
            <button type="submit" class="btn btn-primary btn-block btn-rounded">Login</button>
          </div>
          <!-- /.col -->
        </div>
      </form>
      <br>

      <br>
    </div>
    <!-- /.login-box-body -->
  </div>
  <!-- /.login-box -->

  <!-- jQuery 3 -->
  <script src="<?= base_url('assets/') ?>bower_components/jquery/dist/jquery.min.js"></script>
  <!-- Bootstrap 3.3.7 -->
  <script src="<?= base_url('assets/') ?>bower_components/bootstrap/dist/js/bootstrap.min.js"></script>
  <!-- iCheck -->
  <script src="<?= base_url('assets/') ?>plugins/iCheck/icheck.min.js"></script>
  <!-- Sweetalert2 -->
  <script src="<?= base_url('assets/') ?>bower_components/sweetalert2/sweetalert2.all.min.js"></script>
  
  <?php if ($this->session->flashdata('logout_success')): ?>
    <script>
      $(document).ready(function() {
        Swal({
          title: 'Logout Berhasil',
          text: 'Anda telah keluar dari sistem.',
          type: 'success',
          timer: 2000,
          showConfirmButton: false
        });
      });
    </script>
  <?php endif; ?>
</body>

</html>