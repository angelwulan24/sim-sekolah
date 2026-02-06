<!DOCTYPE html>
<html>
<head>
  <meta charset="utf-8">
  <meta http-equiv="X-UA-Compatible" content="IE=edge">
  <title><?= $title?></title>
  <meta content="width=device-width, initial-scale=1, maximum-scale=1, user-scalable=no" name="viewport">
  <link rel="stylesheet" href="<?= base_url('assets/')?>bower_components/bootstrap/dist/css/bootstrap.min.css">
  <link rel="stylesheet" href="<?= base_url('assets/')?>bower_components/font-awesome/css/font-awesome.min.css">
  <link rel="stylesheet" href="<?= base_url('assets/')?>bower_components/Ionicons/css/ionicons.min.css">
  <link rel="stylesheet" href="<?= base_url('assets/')?>dist/css/AdminLTE.min.css">
  <link rel="shorcut icon" type="text/css" href="<?php echo base_url('assets/dist/img/ikhlas.png')?>">
  <link rel="stylesheet" href="https://fonts.googleapis.com/css?family=Source+Sans+Pro:300,400,600,700,300italic,400italic,600italic">
</head>
<body style="background-color: #66a3ff " class="hold-transition login-page">
<div class="login-box">
  <div class="login-logo">
    <img style="width: 40%" src="<?=base_url('assets/dist/img/ikhlas.png')?>">
  </div>
  <div class="login-box-body">
    <p class="login-box-msg"><strong style="color:#66a3ff  ">Verifikasi Kode OTP</strong></p>
    <?= $this->session->flashdata('message'); ?>

    <form action="<?= base_url('Auth/verify_otp')?>" method="post">
      <div class="form-group has-feedback">
        <input type="text" class="form-control" autocomplete="off" name="otp" id="otp" placeholder="Masukan Kode OTP">
        <span class="glyphicon glyphicon-lock form-control-feedback"></span>
          <?= form_error('otp','<small class="text-danger">','</small>'); ?>
      </div>
      <div class="row">
        <div class="col-xs-12">
          <button type="submit" class="btn btn-primary btn-block btn-rounded">Verifikasi</button>
        </div>
      </div>
    </form>
    <br>
    <div class="text-center">
        <a href="<?=base_url('Auth/login_wa')?>">Kirim Ulang OTP</a>
    </div>
  </div>
</div>
<script src="<?= base_url('assets/')?>bower_components/jquery/dist/jquery.min.js"></script>
<script src="<?= base_url('assets/')?>bower_components/bootstrap/dist/js/bootstrap.min.js"></script>
</body>
</html>
