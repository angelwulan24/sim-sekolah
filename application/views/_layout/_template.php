<!DOCTYPE html>
<html>
    <head>
        <title><?php echo $title; ?></title>
        <meta charset="utf-8">
        <meta http-equiv="X-UA-Compatible" content="IE=edge">
        <meta content="width=device-width, initial-scale=1, maximum-scale=1, user-scalable=no" name="viewport">
        <meta name="description" content=" " /> 
        <meta name="keywords" content=" " />
         <link rel="shortcut icon" type="text/css" href="<?php echo base_url('assets/dist/img/MI.png')?>">

        <!-- File Css -->
        <?php echo @$_css; ?>
        <script src="<?php echo base_url('assets/')?>bower_components/jquery/dist/jquery.min.js"></script>
        
        <style>
        /* Premium Green Gradient */
        :root {
            --green-gradient: linear-gradient(135deg, #00A75D 0%, #008f50 100%);
            --green-gradient-hover: linear-gradient(135deg, #008f50 0%, #007a44 100%);
            --pastel-green: #00A75D;       /* Vibrant WhatsApp Green */
            --pastel-green-hover: #008f50; /* Slightly darker */
            --pastel-green-light: #e6f7ef;
            --pastel-green-bg: #f0fbf5;
            --vibrant-green: #00A75D;
            --text-dark: #004d2b;
            --text-light: #ffffff;
            --shadow-sm: 0 2px 5px rgba(0,0,0,0.05);
            --shadow-md: 0 4px 12px rgba(0,0,0,0.08);
            --shadow-hover: 0 8px 20px rgba(0,167,93,0.2);
            --transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
        }

        /* General Layout with Gradient */
        body.skin-green-light .main-header .navbar {
            background: var(--green-gradient) !important;
            box-shadow: var(--shadow-md);
        }
        body.skin-green-light .main-header .logo {
            background: var(--green-gradient) !important;
            color: var(--text-light) !important;
            font-weight: bold;
            border-bottom: 1px solid rgba(255,255,255,0.1);
            transition: var(--transition);
        }
        
        /* Unified Button & Label Colors with Gradient */
        .btn-primary, .btn-success, .btn-info {
            background: var(--green-gradient) !important;
            border: none !important;
            color: #ffffff !important;
            box-shadow: var(--shadow-sm);
            transition: var(--transition);
        }
        .btn-primary:hover, .btn-success:hover, .btn-info:hover {
            background: var(--green-gradient-hover) !important;
            box-shadow: var(--shadow-hover);
            transform: translateY(-1px);
        }
        .label-success {
            background: var(--green-gradient) !important;
            color: #ffffff !important;
        }
        
        /* Select2 & Filter Styling */
        .select2-container--default .select2-selection--single {
            border-color: #d2d6de !important;
        }
        .select2-container--default.select2-container--focus .select2-selection--single {
            border-color: var(--pastel-green) !important;
            color: var(--text-light) !important;
            transition: var(--transition);
        }
        body.skin-green-light .main-header li.user-header {
            background-color: var(--pastel-green) !important;
        }
        
        /* Sidebar Styling */
        body.skin-green-light .wrapper, body.skin-green-light .main-sidebar, body.skin-green-light .left-side {
            background-color: #ffffff !important;
            box-shadow: 2px 0 10px rgba(0,0,0,0.03);
            z-index: 800;
        }
        body.skin-green-light .sidebar-menu>li>a {
            transition: var(--transition);
        }
        body.skin-green-light .sidebar-menu>li:hover>a {
            background: var(--pastel-green-light) !important;
            color: var(--text-dark) !important;
            border-left-color: var(--pastel-green-hover) !important;
            padding-left: 20px; /* Micro-interaction: slide right */
        }
        body.skin-green-light .sidebar-menu>li.active>a {
            background: var(--pastel-green-light) !important;
            color: var(--text-dark) !important;
            border-left-color: var(--pastel-green-hover) !important;
            font-weight: bold;
        }
        body.skin-green-light .sidebar-menu>li.header {
            background: #ffffff !important;
            color: var(--pastel-green-hover) !important;
            font-weight: bold;
            letter-spacing: 0.5px;
        }
        body.skin-green-light .content-wrapper, body.skin-green-light .right-side {
            background-color: var(--pastel-green-bg) !important;
        }
        body.skin-green-light .sidebar a {
            color: var(--text-dark) !important;
        }
        body.skin-green-light .user-panel>.info, body.skin-green-light .user-panel>.info>a {
            color: var(--text-dark) !important;
        }

        /* Box & Cards (Interactive) */
        .box {
            border-radius: 10px;
            box-shadow: var(--shadow-sm) !important;
            border-top: none !important;
            transition: var(--transition);
            background: #fff;
        }
        .box:hover {
            box-shadow: var(--shadow-md) !important;
            transform: translateY(-2px); /* Micro-interaction: lift */
        }
        .box.box-primary {
            border-top: 3px solid var(--pastel-green) !important;
        }
        .box.box-success {
            border-top: 3px solid var(--pastel-green-hover) !important;
        }
        
        /* Buttons (Interactive) */
        .btn {
            transition: var(--transition);
            border-radius: 6px;
        }
        .btn-primary {
            background-color: var(--pastel-green) !important;
            border-color: var(--pastel-green) !important;
            color: #fff !important;
            font-weight: 600;
        }
        .btn-primary:hover, .btn-primary:active, .btn-primary:focus {
            background-color: var(--pastel-green-hover) !important;
            border-color: var(--pastel-green-hover) !important;
            transform: translateY(-2px);
            box-shadow: var(--shadow-hover);
        }
        .btn-success {
            background-color: #7ab59a !important; /* Slightly distinct green */
            border-color: #6c9f8c !important;
            color: #fff !important;
            font-weight: 600;
        }
        .btn-success:hover {
            background-color: #6c9f8c !important;
            transform: translateY(-2px);
            box-shadow: var(--shadow-hover);
        }
        
        /* Pagination */
        .pagination>.active>a, .pagination>.active>a:focus, .pagination>.active>a:hover, .pagination>.active>span, .pagination>.active>span:focus, .pagination>.active>span:hover {
            background-color: var(--pastel-green) !important;
            border-color: var(--pastel-green-hover) !important;
            color: #fff !important;
        }

        /* Tables & Labels */
        .bg-blue {
            background-color: var(--pastel-green) !important;
            color: #fff !important;
        }
        .label-primary {
            background-color: var(--pastel-green) !important;
            color: #fff !important;
        }
        .label-success {
            background-color: #7ab59a !important;
            color: #fff !important;
        }
        .table-striped>tbody>tr:nth-of-type(odd) {
            background-color: #fcfdfc !important;
        }
        .table-hover>tbody>tr:hover {
            background-color: var(--pastel-green-light) !important;
            transition: background-color 0.2s ease;
        }
        .table-hover>tbody>tr:hover {
            background-color: var(--pastel-green-light) !important;
            transition: background-color 0.2s ease;
        }
        </style>
    </head>
    <SCRIPT language=Javascript>
        function Angka(evt){
            var charCode = (evt.which) ? evt.which : event.keyCode
                if (charCode > 31 && (charCode < 48 || charCode > 57))
                return false;
            return true;
        }
    </SCRIPT>

<?php 
$masuk = $this->db->query("SELECT id_users AS id, email, password, nama_users AS name, role, gambar FROM users WHERE id_users = '".$this->session->userdata('id')."'")->row_array();
$foto_profil = base_url('assets/dist/img/') . (!empty($masuk['gambar']) ? $masuk['gambar'] : 'user.png');

if ($masuk['role'] == 3) {
    $siswa = $this->db->query("SELECT foto_siswa AS foto FROM siswa WHERE nis_siswa = '".$masuk['email']."'")->row_array();
    if ($siswa && !empty($siswa['foto'])) {
        $foto_profil = base_url('assets/images/siswa/') . $siswa['foto'];
    }
}
?>
    <body class="hold-transition skin-green-light sidebar-mini">
        <div class="wrapper">
            <header class="main-header">
                 <!-- Header -->
                <?php echo @$_header; ?>
            </header>
            <aside class="main-sidebar">
                <section class="sidebar">
                    <div class="user-panel">
                        <div class="pull-left image">
                          <img src="<?php echo $foto_profil; ?>" class="img-circle" alt="User Image" style="object-fit:cover; width:45px; height:45px;">
                        </div>
                        <div class="pull-left info">
                          <p><?= $masuk['name']?></p>
                        </div>
                    </div>
                    <!-- Sidebar -->
                    <?php echo @$_sidebar; ?>
                </section>
            </aside>
            <div class="content-wrapper">
                <section class="content-header">
                    <h1> <i class="<?php echo isset($icon) ? $icon : 'fa fa-dashboard'; ?>"></i> <?php echo isset($judul) ? $judul : 'Data Siswa'; ?></h1>
                    <?php echo $this->breadcrumb->output(); ?>
                </section>
                <section class="content">
                    <div class="row">
                        <!-- Content -->
                        <?php echo @$_content; ?>        
                    </div>      
                </section>
            </div>
            <!-- Footer -->
            <?php echo @$_footer;?>
          <div class="control-sidebar-bg"></div>
      </div>
      <!-- File Js -->
      <?php echo @$_js; ?>

    <script>
        $(document).ready(function () {

            $('.select2').css('width','100%').select2({allowClear:false})
                .on('change', function(){
                    $(this).closest('form').validate().element($(this));
            });

            $('.datepicker').datepicker({
                autoclose:true,format: "yyyy-mm-dd",
                todayHighlight: true,})
                .on('changeDate', function(ev) {
                    $(this).closest('form').validate().element($(this));
            });

        $('.tabel').DataTable({
            "oLanguage": {
                "sSearch"       :"<i class='fa fa-search fa-fw'></i> Cari: ",
                "sLengthMenu"   :"Tampilkan _MENU_ data",
                "sInfo"         :"Menampilkan _START_ sampai _END_ dari _TOTAL_ data",
                "sInfoFiltered" :"(disaring dari _MAX_ total data)", 
                "sZeroRecords"  :"Data Pencarian kosong", 
                "sEmptyTable"   :"Data kosong", 
                "sInfoEmpty"    :"Menampilkan 0 sampai 0 data",
                "sProcessing"   :"Sedang memproses...", 
                "oPaginate": {
                    "sPrevious" :"Sebelumnya",
                    "sNext"     :"Selanjutnya",
                    "sFirst"    :"Pertama",
                    "sLast"     :"Terakhir"
                }
            },
            "processing": true
        });

            $('.sidebar-menu').tree();

            $('input[type="radio"].minimal').iCheck({
                checkboxClass: 'icheckbox_minimal-blue',
                radioClass   : 'iradio_minimal-blue'
            });

                            $(document).one('ajaxloadstart.page', function(e) {
                    //in ajax mode, remove remaining elements before leaving page
                    $('[class*=select2]').remove();
                });

        });
        </script>

        <?php if ($this->session->userdata('role') == 2): ?>
        <style>
            a[data-target="#modal-tambah"],
            a[data-target="#modal-import"],
            a[onclick^="Tambah"],
            button#simpan {
                display: none !important;
            }
        </style>
        <script>
            $(document).ready(function(){
                function hideMutatingActions() {
                    $('a:contains("Tambah Data"), button:contains("Tambah Data")').remove();
                    $('a:contains("Ubah"), button:contains("Ubah")').remove();
                    $('a:contains("Hapus"), button:contains("Hapus")').remove();
                    $('a:contains("Bayar"), button:contains("Bayar")').remove();
                    $('a:contains("Import Data"), button:contains("Import Data")').remove();
                    $('button[type="submit"]:contains("Simpan")').remove();
                    $('button[type="submit"]:contains("Bayar")').remove();
                }
                
                hideMutatingActions();
                
                // For tables that use DataTables, re-run after draw
                setInterval(hideMutatingActions, 500); 
                $(document).ajaxComplete(function() {
                    hideMutatingActions();
                });
            });
        </script>
        <?php endif; ?>

    </body>
</html>
