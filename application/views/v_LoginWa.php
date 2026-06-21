<!DOCTYPE html>
<html>

<head>
  <meta charset="utf-8">
  <meta http-equiv="X-UA-Compatible" content="IE=edge">
  <title><?= $title ?></title>
  <meta content="width=device-width, initial-scale=1, maximum-scale=1, user-scalable=no" name="viewport">
  <link rel="stylesheet" href="<?= base_url('assets/') ?>bower_components/bootstrap/dist/css/bootstrap.min.css">
  <link rel="stylesheet" href="<?= base_url('assets/') ?>bower_components/font-awesome/css/font-awesome.min.css">
  <link rel="stylesheet" href="<?= base_url('assets/') ?>bower_components/Ionicons/css/ionicons.min.css">
  <link rel="stylesheet" href="<?= base_url('assets/') ?>dist/css/AdminLTE.min.css">
  <link rel="shorcut icon" type="text/css" href="<?php echo base_url('assets/dist/img/MI.png') ?>">
  <link rel="stylesheet" href="https://fonts.googleapis.com/css?family=Source+Sans+Pro:300,400,600,700,300italic,400italic,600italic">
  <!-- Custom Pastel Green Theme for Login -->
  <style>
    :root {
        --pastel-green: #A2D5AB;       /* Matched Green */
        --pastel-green-hover: #8BBF95; /* Darker accent */
        --pastel-green-light: #E1F2E5;
        --pastel-green-bg: #F2FAF4;
        --text-dark: #2D4A3E;
        --shadow-sm: 0 4px 15px rgba(0,0,0,0.05);
        --shadow-hover: 0 6px 20px rgba(162,213,171,0.25);
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
    .btn-success {
        background-color: var(--pastel-green) !important;
        border-color: #8BBF95 !important;
        color: #fff !important;
        font-weight: 600;
        border-radius: 6px;
        transition: var(--transition);
    }
    .btn-success:hover, .btn-success:active, .btn-success:focus {
        background-color: var(--pastel-green-hover) !important;
        border-color: var(--pastel-green-hover) !important;
        transform: translateY(-2px);
        box-shadow: 0 4px 10px rgba(162,213,171,0.3);
    }
  </style>
</head>

<body class="hold-transition login-page">
  <div class="login-box">
    <div class="login-logo">
      <img style="width: 40%" src="<?= base_url('assets/dist/img/MI.png') ?>">
    </div>
    <div class="login-box-body">
      <p class="login-box-msg">
        <span style="color:var(--text-dark); font-size: 0.85em; display: block; margin-bottom: 2px;">APLIKASI KEUANGAN SEKOLAH</span>
        <strong style="color:var(--text-dark); font-size: 1.25em; display: block;">MI DAAR EL-MUFLIHIN</strong>
      </p>
      <?= $this->session->flashdata('message'); ?>

      <form action="<?= base_url('Auth/login_wa') ?>" method="post">
        <div class="form-group has-feedback">
          <input type="text" class="form-control" autocomplete="off" value="<?= set_value('email') ?>" name="email" id="email" placeholder="Email Terdaftar">
          <span class="glyphicon glyphicon-envelope form-control-feedback"></span>
          <?= form_error('email', '<small class="text-danger">', '</small>'); ?>
        </div>
        <div class="row">
          <div class="col-xs-12">
            <button type="submit" class="btn btn-success btn-block btn-rounded"><i class="fa fa-whatsapp"></i> Kirim OTP</button>
          </div>
        </div>
      </form>
      <br>
      <div class="text-center">
        <a href="<?= base_url('Auth') ?>">Kembali ke Login Biasa</a>
      </div>
    </div>
  </div>
  <script src="<?= base_url('assets/') ?>bower_components/jquery/dist/jquery.min.js"></script>
  <script src="<?= base_url('assets/') ?>bower_components/bootstrap/dist/js/bootstrap.min.js"></script>
</body>

</html>