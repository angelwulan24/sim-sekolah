<?php $aktif = $this->uri->segment(1); ?>
<ul class="sidebar-menu" data-widget="tree">
    <li class="header">MENU UTAMA</li>
    <?php if($this->session->userdata('role') != 3) { ?>
    <li class = "<?php echo activate_menu('Beranda')?>"><a href="<?= base_url()?>Beranda"><i class="fa fa-dashboard" style="color: #3498db;"></i> <span>Beranda</span><span class="pull-right-container"></span></a></li>
    <li class="treeview <?php if ($aktif == 'Guru' || $aktif == 'Siswa' || $aktif == 'Kelas' || $aktif == 'Transaksi') echo 'active' ?>">
        <a href="#"><i class="fa fa-database" style="color: #2ecc71;"></i> <span>Data Master</span>
            <span class="pull-right-container"><i class="fa fa-angle-left pull-right"></i></span>
        </a>
        <ul class="treeview-menu">
            <li class = "<?php echo activate_menu('Guru')?>"><a href="<?= base_url()?>Guru"><i class="fa fa-circle-o text-aqua"></i>Data Guru</a></li>
            <li class = "<?php echo activate_menu('Kelas')?>"><a href="<?= base_url()?>Kelas"><i class="fa fa-circle-o text-yellow"></i>Data Kelas</a></li>
            <li class = "<?php echo activate_menu('Siswa')?>"><a href="<?= base_url()?>Siswa"><i class="fa fa-circle-o text-green"></i>Data Siswa</a></li>
            <li class = "<?php echo activate_menu('Transaksi')?>"><a href="<?= base_url()?>Transaksi"><i class="fa fa-circle-o text-red"></i>Jenis Tagihan</a></li>
        </ul>
    </li>
    <?php if($this->session->userdata('role') != 2) { ?>
    <li class="treeview <?php if ($aktif == 'Tagihan' || $aktif == 'Lainnya') echo 'active' ?>">
        <a href="#"><i class="fa fa-level-down" style="color: #f1c40f;"></i> <span>Pemasukan</span>
            <span class="pull-right-container"><i class="fa fa-angle-left pull-right"></i></span>
        </a>
        <ul class="treeview-menu">
            <li class = "<?php echo activate_menu('Tagihan')?>"><a href="<?= base_url()?>Tagihan"><i class="fa fa-circle-o text-yellow"></i> Tagihan Siswa</a></li>
            <li class = "<?php echo activate_menu('Lainnya')?>"><a href="<?= base_url()?>Lainnya"><i class="fa fa-circle-o text-aqua"></i> Pemasukan Lainnya</a></li>
        </ul>
    </li>
    <li class = "<?php echo activate_menu('Pengeluaran')?>"><a href="<?= base_url()?>Pengeluaran"><i class="fa fa-level-up" style="color: #e74c3c;"></i> <span>Pengeluaran</span><span class="pull-right-container"></span></a></li>
    <li class = "<?php echo activate_menu('Tunggakan')?>"><a href="<?= base_url()?>Tunggakan"><i class="fa fa-exclamation-triangle" style="color: #e67e22;"></i> <span>Info Tunggakan</span><span class="pull-right-container"></span></a></li>
    <?php } ?>
    <?php if($this->session->userdata('role') != 2) { ?>
    <li class = "<?php echo activate_menu('Whatsapp')?>"><a href="<?= base_url()?>Whatsapp"><i class="fa fa-whatsapp" style="color: #25D366;"></i> <span>WhatsApp Gateway</span><span class="pull-right-container"></span></a></li>
    <?php } ?>
    <li class = "<?php echo activate_menu('Laporan')?>"><a href="<?= base_url()?>Laporan"><i class="fa fa-line-chart" style="color: #9b59b6;"></i> <span>Laporan</span><span class="pull-right-container"></span></a></li>
    <?php } else { ?>
        <li class = "<?php echo activate_menu('StudentArea')?>"><a href="<?= base_url()?>StudentArea"><i class="fa fa-money" style="color: #f39c12;"></i> <span>Tagihan Saya</span><span class="pull-right-container"></span></a></li>
    <?php } ?>
</ul>