<div class="col-xs-12">
	<div class="box box-primary">
        <div class="box-header">
            <h3 class="box-title"><i class="fa fa-exclamation-triangle"></i> Daftar Tunggakan Siswa</h3>
        </div>
	    <div class="box-body">
	    	<div class="table-responsive">    	
		        <table id="list-tunggakan" class="table table-bordered table-striped table-hover">
		            <thead>
			            <tr>
                            <th style="width: 10px;">No</th>
                            <th>Nama Siswa</th>
                            <th>Telpon Wali</th>
                            <th>Tagihan SPP</th>
                            <th>Tagihan Ujian</th>
                            <th>Tagihan Buku</th>
                            <th>Tagihan Baju</th>
                            <th>Total Tunggakan</th>
                            <?php if($this->session->userdata('role') != 2): ?>
                            <th width="120">Aksi Pengingat</th>
                            <?php endif; ?>
			            </tr>
		            </thead>
		            <tbody>
                        <?php $no = 1; foreach($tunggakan as $t): ?>
                            <tr>
                                <td><?= $no++ ?></td>
                                <td><?= $t->name ?></td>
                                <td><?= empty($t->telpon) ? '<span class="label label-danger">Kosong</span>' : $t->telpon ?></td>
                                <td><?= 'Rp ' . number_format($t->tunggakan_spp, 0, ',', '.') ?></td>
                                <td><?= 'Rp ' . number_format($t->tunggakan_ujian, 0, ',', '.') ?></td>
                                <td><?= 'Rp ' . number_format($t->tunggakan_buku, 0, ',', '.') ?></td>
                                <td><?= 'Rp ' . number_format($t->tunggakan_baju, 0, ',', '.') ?></td>
                                <td><strong><?= 'Rp ' . number_format($t->total_tunggakan, 0, ',', '.') ?></strong></td>
                                <?php if($this->session->userdata('role') != 2): ?>
                                <td>
                                    <?php if(!empty($t->telpon)): ?>
                                    <button class="btn btn-success btn-xs" onclick="kirim_wa(<?= $t->id ?>, <?= $t->tunggakan_spp ?>, <?= $t->tunggakan_ujian ?>, <?= $t->tunggakan_buku ?>, <?= $t->tunggakan_baju ?>, <?= $t->total_tunggakan ?>)">
                                        <i class="fa fa-whatsapp"></i> &nbsp; Ingatkan
                                    </button>
                                    <?php else: ?>
                                    <button class="btn btn-default btn-xs" disabled title="Nomor telpon belum diisi"><i class="fa fa-whatsapp"></i> &nbsp; Ingatkan</button>
                                    <?php endif; ?>
                                </td>
                                <?php endif; ?>
                            </tr>
                        <?php endforeach; ?>
		            </tbody>
		        </table>
	       	</div>
	    </div>
    </div>
</div>

<script>
    $(document).ready(function(){
        $('#list-tunggakan').DataTable({
            oLanguage: {
                sSearch       :"<i class='fa fa-search fa-fw'></i> Cari: ",
                sLengthMenu   :"Tampilkan _MENU_ data",
                sInfo         :"Menampilkan _START_ sampai _END_ dari _TOTAL_ data",
                sInfoFiltered :"(disaring dari _MAX_ total data)", 
                sZeroRecords  :"Tidak ditemukan siswa dengan tunggakan", 
                sEmptyTable   :"Data kosong / Semua siswa lunas", 
                sInfoEmpty    :"Menampilkan 0 sampai 0 data",
                sProcessing   :"Sedang memproses...", 
                oPaginate: {
                    sPrevious :"Sebelumnya",
                    sNext     :"Selanjutnya",
                    sFirst    :"Pertama",
                    sLast     :"Terakhir"
                }
            }
        });
    });

    function kirim_wa(id, spp, ujian, buku, baju, total) {
        Swal({
            title: "Kirim Pengingat Tagihan?",
            text: "Kirim pesan peringatan detail tunggakan kepada Siswa/Ortu via WhatsApp Gateway?",
            type: "info",
            showCancelButton: true,
            confirmButtonColor: "#28a745",
            confirmButtonText: "Ya, Kirim Sekarang!",
            cancelButtonText: "Batal"
        }).then((result) => {
            if (result.value) {
                Swal({
                    title: 'Memproses...',
                    text: 'Sedang mengirim pesan WhatsApp...',
                    onOpen: () => {
                        Swal.showLoading()
                    }
                });

                $.ajax({
                    url: '<?= base_url() ?>Tunggakan/kirim_pengingat',
                    type: 'POST',
                    data: {
                        id_siswa: id,
                        spp: spp,
                        ujian: ujian,
                        buku: buku,
                        baju: baju,
                        total: total
                    },
                    dataType: 'json',
                    success: function(res) {
                        if(res.status) {
                            Swal({
                                title: 'Berhasil!',
                                text: res.message,
                                type: 'success'
                            });
                        } else {
                            Swal({
                                title: 'Gagal Mengirim!',
                                text: res.message,
                                type: 'error'
                            });
                        }
                    },
                    error: function() {
                        Swal({
                            title: 'Oops!',
                            text: 'Terjadi kesalahan sistem atau jembatan WA Gateway tidak aktif.',
                            type: 'error'
                        });
                    }
                });
            }
        });
    }
</script>
