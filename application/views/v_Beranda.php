<?php 
// 1. Data Keuangan Hari Ini
$today = date('Y-m-d');

// Kas masuk hari ini: pemasukan lainnya + tagihan siswa lunas
$pemasukan_today = $this->db->query("SELECT COALESCE(SUM(CAST(nominal_pemasukan AS DECIMAL(15,2))), 0) AS total FROM pemasukan p WHERE tgl_pemasukan = '$today' AND NOT EXISTS (SELECT 1 FROM tagihan_siswa ts WHERE ts.id_pemasukan = p.id_pemasukan)")->row()->total ?? 0;
$tagihan_today = $this->db->query("SELECT COALESCE(SUM(CAST(j.nominal_tagihan AS DECIMAL(15,2))), 0) AS total FROM tagihan_siswa ts JOIN jenis_tagihan j ON ts.kode_tagihan = j.kode_tagihan WHERE ts.status = 'Lunas' AND DATE(ts.tgl_pembayaran) = '$today'")->row()->total ?? 0;
$kas_masuk_today = $pemasukan_today + $tagihan_today;

// Kas keluar hari ini: semua pengeluaran (termasuk gaji yang sudah masuk via FK)
$kas_keluar_today = $this->db->query("SELECT COALESCE(SUM(CAST(nominal_pengeluaran AS DECIMAL(15,2))), 0) AS total FROM pengeluaran WHERE tgl_pengeluaran = '$today'")->row()->total ?? 0;

// Total semua pemasukan dan pengeluaran (saldo akhir kumulatif)
$total_masuk_all = $this->db->query("SELECT COALESCE(SUM(CAST(nominal_pemasukan AS DECIMAL(15,2))), 0) AS total FROM pemasukan p WHERE NOT EXISTS (SELECT 1 FROM tagihan_siswa ts WHERE ts.id_pemasukan = p.id_pemasukan)")->row()->total ?? 0;
$total_tagihan_all = $this->db->query("SELECT COALESCE(SUM(CAST(j.nominal_tagihan AS DECIMAL(15,2))), 0) AS total FROM tagihan_siswa ts JOIN jenis_tagihan j ON ts.kode_tagihan = j.kode_tagihan WHERE ts.status = 'Lunas'")->row()->total ?? 0;
$total_keluar_all = $this->db->query("SELECT COALESCE(SUM(CAST(nominal_pengeluaran AS DECIMAL(15,2))), 0) AS total FROM pengeluaran")->row()->total ?? 0;
$sisa_dana = $total_masuk_all + $total_tagihan_all - $total_keluar_all;

// Untuk card tampilan hari ini, ambil kas masuk & keluar
$g = ['kas_masuk' => $kas_masuk_today, 'kas_keluar' => $kas_keluar_today];

// 3. Statistik Sekolah
$count_guru  = $this->M_General->countAll('guru');
$count_siswa = $this->M_General->countAll('siswa');
$count_kelas = $this->M_General->countAll('kelas');

// 4. Data Grafik (7 Hari Terakhir) - dari v_laporan (aggregate view)
$chart_data = $this->db->query("SELECT DATE_FORMAT(tanggal, '%d %b') as tgl, kas_masuk, kas_keluar FROM v_laporan ORDER BY tanggal DESC LIMIT 7")->result();
$chart_labels = []; $chart_masuk = []; $chart_keluar = [];
foreach(array_reverse($chart_data) as $row) {
    $chart_labels[] = $row->tgl;
    $chart_masuk[]  = $row->kas_masuk;
    $chart_keluar[] = $row->kas_keluar;
}
?>

<!-- Google Font & CDN -->
<link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;600;700&display=swap" rel="stylesheet">
<link href='https://cdn.jsdelivr.net/npm/fullcalendar@5.11.3/main.min.css' rel='stylesheet' />
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

<style>
    body, .box-title, .amount, .title { font-family: 'Inter', sans-serif !important; }
    
    /* Calendar tweak */
    .fc .fc-toolbar-title { font-size: 1rem !important; font-weight: 700; }
    .fc .fc-button { padding: 2px 5px !important; font-size: 10px !important; }

    /* Welcome Card Styling */
    .welcome-card {
        background: #fff; border-radius: 4px; padding: 25px; margin-bottom: 25px;
        display: flex; align-items: center; box-shadow: 0 1px 3px rgba(0,0,0,0.1);
        border: 1px solid #e1e1e1; border-left: 5px solid #3c8dbc;
    }
    .welcome-card img { width: 80px; height: auto; margin-right: 25px; }
    .welcome-card h2 { margin: 0; font-size: 26px; font-weight: 700; color: #333; font-family: 'Inter', sans-serif; }
    .welcome-card p { margin: 5px 0 0; font-size: 16px; color: #666; font-family: 'Inter', sans-serif; }
    .welcome-card .school-name { color: #3c8dbc; font-weight: 700; }

    /* Cards */
    .finance-card {
        background: #fff; border-radius: 4px; margin-bottom: 15px;
        display: flex; align-items: stretch; height: 85px;
        box-shadow: 0 1px 3px rgba(0,0,0,0.1); border: 1px solid #eee; overflow: hidden;
    }
    .finance-card .icon-box { width: 75px; display: flex; align-items: center; justify-content: center; color: #fff; font-size: 24px; }
    .finance-card .content-box { padding: 10px 15px; flex: 1; display: flex; flex-direction: column; justify-content: center; }
    .finance-card .title { font-size: 10px; font-weight: 700; color: #888; text-transform: uppercase; margin-bottom: 2px; }
    .finance-card .amount { font-size: 20px; font-weight: 700; color: #333; }
    
    .bg-pemasukan { background: #00a65a; }
    .bg-pengeluaran { background: #dd4b39; }
    .bg-saldo { background: #00c0ef; }
    .bg-siswa { background: #f39c12; }
    .bg-guru { background: #605ca8; }
    .bg-kelas { background: #3c8dbc; }

    .box { border-radius: 3px; box-shadow: 0 1px 3px rgba(0,0,0,0.1); margin-bottom: 15px; }
    .box-title { font-size: 14px !important; }
</style>

<div class="row">
    <div class="col-md-12">
        <!-- Welcome Header -->
        <div class="welcome-card">
            <img src="<?= base_url('assets/dist/img/MI.png') ?>" alt="Logo Sekolah">
            <div class="welcome-text">
                <h2 style="color: #333;">Selamat Datang, <?= ucwords($this->session->userdata('name') ?? 'Admin') ?>!</h2>
                <p style="color: #444;">Di SISTEM KEUANGAN MI DAAR EL-MUFLIHIN</p>
            </div>
        </div>
    </div>
</div>

<div class="row">
    <!-- Data Guru -->
    <div class="col-lg-4 col-xs-6">
        <div class="small-box bg-aqua">
            <div class="inner">
                <h3><?= $count_guru ?></h3>
                <p>Data Guru</p>
            </div>
            <div class="icon">
                <i class="fa fa-graduation-cap"></i>
            </div>
            <a href="<?= base_url('Guru') ?>" class="small-box-footer">More info <i class="fa fa-arrow-circle-right"></i></a>
        </div>
    </div>
    <!-- Data Kelas -->
    <div class="col-lg-4 col-xs-6">
        <div class="small-box bg-orange">
            <div class="inner">
                <h3><?= $count_kelas ?></h3>
                <p>Data Kelas</p>
            </div>
            <div class="icon">
                <i class="fa fa-university"></i>
            </div>
            <a href="<?= base_url('Kelas') ?>" class="small-box-footer">More info <i class="fa fa-arrow-circle-right"></i></a>
        </div>
    </div>
    <!-- Data Siswa -->
    <div class="col-lg-4 col-xs-6">
        <div class="small-box bg-blue">
            <div class="inner">
                <h3><?= $count_siswa ?></h3>
                <p>Data Siswa</p>
            </div>
            <div class="icon">
                <i class="fa fa-users"></i>
            </div>
            <a href="<?= base_url('Siswa') ?>" class="small-box-footer">More info <i class="fa fa-arrow-circle-right"></i></a>
        </div>
    </div>
</div>

<div class="row">
    <!-- Pemasukan -->
    <div class="col-md-4">
        <div class="finance-card">
            <div class="icon-box bg-pemasukan"><i class="fa fa-download"></i></div>
            <div class="content-box">
                <span class="title">PEMASUKAN</span>
                <span class="amount">Rp <?= number_format($g['kas_masuk'], 0, ',', '.') ?></span>
            </div>
        </div>
    </div>
    <!-- Pengeluaran -->
    <div class="col-md-4">
        <div class="finance-card">
            <div class="icon-box bg-pengeluaran"><i class="fa fa-upload"></i></div>
            <div class="content-box">
                <span class="title">PENGELUARAN</span>
                <span class="amount">Rp <?= number_format($g['kas_keluar'], 0, ',', '.') ?></span>
            </div>
        </div>
    </div>
    <!-- Saldo Akhir -->
    <div class="col-md-4">
        <div class="finance-card">
            <div class="icon-box bg-kelas"><i class="fa fa-folder-open"></i></div>
            <div class="content-box">
                <span class="title">SALDO AKHIR</span>
                <span class="amount">Rp <?= number_format($sisa_dana, 0, ',', '.') ?></span>
            </div>
        </div>
    </div>
</div>

<div class="row" style="margin-top: 10px;">
    <!-- 1. Kalender -->
    <div class="col-md-5">
        <div class="box box-primary">
            <div class="box-body" style="padding: 10px;">
                <div id='calendar' style="max-width: 100%;"></div>
            </div>
        </div>
    </div>

    <!-- 2. Grafik -->
    <div class="col-md-7">
        <div class="box box-info">
            <div class="box-header with-border">
                <h3 class="box-title"><i class="fa fa-line-chart"></i> Grafik Keuangan (7 Hari Terakhir)</h3>
            </div>
            <div class="box-body">
                <canvas id="financeChart" style="height: 335px; width: 100%;"></canvas>
            </div>
        </div>
    </div>
</div>

<!-- Scripts -->
<script src='https://cdn.jsdelivr.net/npm/fullcalendar@5.11.3/main.min.js'></script>
<script src='https://cdn.jsdelivr.net/npm/fullcalendar@5.11.3/locales/id.js'></script>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        // 1. Calendar
        var calendarEl = document.getElementById('calendar');
        var calendar = new FullCalendar.Calendar(calendarEl, {
            locale: 'id',
            initialView: 'dayGridMonth',
            headerToolbar: { left: 'prev,next', center: 'title', right: '' },
            height: 380,
            events: [{ title: 'SPP', start: '<?= date('Y-m-14') ?>', color: '#f39c12' }]
        });
        calendar.render();

        // 2. Chart
        const ctx = document.getElementById('financeChart').getContext('2d');
        new Chart(ctx, {
            type: 'bar',
            data: {
                labels: <?= json_encode($chart_labels) ?>,
                datasets: [
                    {
                        label: 'Pemasukan',
                        data: <?= json_encode($chart_masuk) ?>,
                        backgroundColor: '#00a65a',
                        borderRadius: 5
                    },
                    {
                        label: 'Pengeluaran',
                        data: <?= json_encode($chart_keluar) ?>,
                        backgroundColor: '#dd4b39',
                        borderRadius: 5
                    }
                ]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: { legend: { position: 'top', labels: { boxWidth: 10, font: { size: 10 } } } },
                scales: {
                    y: { beginAtZero: true, ticks: { font: { size: 10 }, callback: function(value) { return 'Rp ' + value.toLocaleString(); } } },
                    x: { ticks: { font: { size: 10 } } }
                }
            }
        });
    });
</script>