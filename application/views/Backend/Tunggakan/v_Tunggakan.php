<div class="col-xs-12">
	<div class="box box-primary">
        <div class="box-header">
            <h3 class="box-title"><i class="fa fa-exclamation-triangle"></i> Daftar Tunggakan Siswa </h3>
        </div>
        <div class="box-body">
            <div class="row" style="margin-bottom: 20px;">
                <form action="<?= base_url('Tunggakan') ?>" method="GET">
                    <div class="col-md-4">
                        <label>Filter Berdasarkan Kelas:</label>
                        <select name="id_kelas" class="form-control" onchange="this.form.submit()">
                            <option value="">-- Semua Kelas --</option>
                            <?php foreach($kelas as $k): ?>
                                <option value="<?= $k->id ?>" <?= $id_kelas == $k->id ? 'selected' : '' ?>><?= $k->nama ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                </form>
            </div>

            <?php if($this->session->userdata('role') != 2): ?>
            <div class="row" style="margin-bottom: 10px;">
                <div class="col-md-12">
                    <button class="btn btn-success" onclick="kirim_wa_masal()">
                        <i class="fa fa-whatsapp"></i> &nbsp; Kirim Notifikasi Pengingat
                    </button>
                </div>
            </div>
            <?php endif; ?>
	    	<div class="table-responsive">    	
		        <table id="list-tunggakan" class="table table-bordered table-striped table-hover">
		            <thead>
			            <tr>
                            <th style="width: 10px;">No</th>
                            <?php if($this->session->userdata('role') != 2): ?>
                            <th width="20" class="text-center"><input type="checkbox" id="check-all"></th>
                            <?php endif; ?>
                            <th>Nama Siswa</th>
                            <th>Kelas</th>
                            <th>Telpon Wali</th>
                            <th>Rincian Tunggakan</th>



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
                                <?php if($this->session->userdata('role') != 2): ?>
                                <td class="text-center">
                                    <?php if(!empty($t->telpon)): ?>
                                        <input type="checkbox" class="wa-checkbox" value="<?= $t->id ?>">
                                    <?php else: ?>
                                        <input type="checkbox" disabled title="Nomor telpon belum diisi">
                                    <?php endif; ?>
                                </td>
                                <?php endif; ?>
                                <td><?= $t->name ?></td>
                                <td><span class="label label-info"><?= $t->nama_kelas ?></span></td>
                                <td><?= empty($t->telpon) ? '<span class="label label-danger">Kosong</span>' : $t->telpon ?></td>
                                <td class="text-left">
                                    <ul style="padding-left: 15px; margin-bottom: 0;">
                                        <?php 
                                        $rincian_arr = [];
                                        foreach($t->rincian_tunggakan as $item): 
                                            $tenggat = !empty($item->tenggat_waktu) ? " (Tenggat: " . date('d/m/y', strtotime($item->tenggat_waktu)) . ")" : "";
                                            $nominal = number_format($item->nominal, 0, ',', '.');
                                            $rincian_arr[] = $item->jenis_tagihan . " : " . $nominal . $tenggat;
                                        ?>
                                            <li><?= $item->jenis_tagihan ?> : <strong><?= $nominal ?></strong> <?= $tenggat ?></li>
                                        <?php endforeach; ?>
                                    </ul>
                                    <?php if($this->session->userdata('role') != 2 && !empty($t->telpon)): ?>
                                        <input type="hidden" id="rincian-<?= $t->id ?>" value="<?= implode('|', $rincian_arr) ?>">
                                        <input type="hidden" id="total-<?= $t->id ?>" value="<?= $t->total_tunggakan ?>">
                                    <?php endif; ?>
                                </td>
                                <td><strong><?= 'Rp ' . number_format($t->total_tunggakan, 0, ',', '.') ?></strong></td>
                                <?php if($this->session->userdata('role') != 2): ?>
                                <td>
                                    <?php if(!empty($t->telpon)): ?>
                                    <button class="btn btn-success btn-xs" onclick="kirim_wa(<?= $t->id ?>, '<?= implode('|', $rincian_arr) ?>', <?= $t->total_tunggakan ?>)">
                                        <i class="fa fa-whatsapp"></i> &nbsp; Kirim Notifikasi
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

    function kirim_wa(id, rincian, total) {
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
                        rincian: rincian, 
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

    // Select all logic
    $('#check-all').on('click', function(){
        var isChecked = $(this).prop('checked');
        var table = $('#list-tunggakan').DataTable();
        table.$('.wa-checkbox').prop('checked', isChecked);
    });

    function kirim_wa_masal() {
        var table = $('#list-tunggakan').DataTable();
        var selected = [];
        
        table.$('.wa-checkbox:checked').each(function() {
            var id = $(this).val();
            var rincian = $('#rincian-' + id).val();
            var total = $('#total-' + id).val();
            
            selected.push({
                id_siswa: id,
                rincian: rincian,
                total: total
            });
        });

        if (selected.length === 0) {
            Swal({
                title: 'Perhatian!',
                text: 'Silahkan pilih minimal satu siswa untuk dikirimkan pesan pengingat.',
                type: 'warning'
            });
            return;
        }

        Swal({
            title: "Kirim Pengingat Masal?",
            text: "Akan mengirim pesan ke " + selected.length + " siswa/wali. Proses ini mungkin memakan waktu.",
            type: "warning",
            showCancelButton: true,
            confirmButtonColor: "#28a745",
            confirmButtonText: "Ya, Kirim Semua!",
            cancelButtonText: "Batal"
        }).then((result) => {
            if (result.value) {
                Swal({
                    title: 'Memproses...',
                    text: 'Sedang mengirim pesan WhatsApp. Mohon tunggu...',
                    allowOutsideClick: false,
                    onOpen: () => {
                        Swal.showLoading()
                    }
                });

                $.ajax({
                    url: '<?= base_url() ?>Tunggakan/kirim_pengingat_masal',
                    type: 'POST',
                    data: {
                        data_kirim: selected
                    },
                    dataType: 'json',
                    success: function(res) {
                        if(res.status) {
                            Swal({
                                title: 'Selesai!',
                                text: res.message,
                                type: 'success'
                            }).then(() => {
                                // Uncheck all after success
                                $('#check-all').prop('checked', false);
                                table.$('.wa-checkbox').prop('checked', false);
                            });
                        } else {
                            Swal({
                                title: 'Gagal!',
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
