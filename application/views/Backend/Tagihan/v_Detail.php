<div class="row">

	<div class="col-md-3">
		<div class="box box-primary">
            <div class="box-body box-profile">
            	<?php if(!empty($siswa->foto)) { ?>
              		<img class="profile-user-img img-responsive img-circle" src="<?=base_url('assets/images/siswa/'.$siswa->foto)?>" alt="User profile picture" style="height: 100px; width: 100px; object-fit: cover;">
              	<?php } else { ?>
					<img class="profile-user-img img-responsive img-circle" src="https://via.placeholder.com/100" alt="User profile picture" style="height: 100px; width: 100px; object-fit: cover;">
				<?php } ?>
              		<h3 class="profile-username text-center"><?=$siswa->name?></h3>

              		<p class="text-muted text-center"><?=$siswa->nis?></p>

              		<ul class="list-group list-group-unbordered">
		                <li class="list-group-item">
		                  <b>Jenis Kelamin</b> <a class="pull-right"><?=$siswa->sex?></a>
		                </li>
		                <li class="list-group-item">
		                  <b>No Telpon</b> <a class="pull-right"><?=$siswa->telpon?></a>
		                </li>
		                <li class="list-group-item">
		                  <b>Kelas</b> <a class="pull-right"><?=$siswa->nama_kelas?></a>
		                </li>
              		</ul>
            </div>
            <!-- /.box-body -->
      	</div>
	</div>

    <div class="col-md-9">

        <div class="row" style="margin-bottom: 15px;">
            <div class="col-md-7 col-sm-7" style="padding-top: 5px;">
                <button class="btn btn-success" id="btn-bayar-terpilih" disabled>
                    <i class="fa fa-money"></i> Bayar Tagihan
                </button>
                <button class="btn btn-danger" id="btn-hapus-terpilih" disabled>
                    <i class="fa fa-trash"></i> Hapus Tagihan
                </button>
            </div>

            <div class="col-md-5 col-sm-5 text-right">
                <form method="GET" action="<?=base_url('Tagihan/Detail/'.$id_siswa)?>" class="form-inline">
                    <div class="form-group">
                        <label style="margin-right: 10px;">Tahun Ajaran:</label>
                        <select name="tahun_ajaran" class="form-control" onchange="this.form.submit()">
                            <option value="">-- Semua Tahun --</option>
                            <?php foreach($list_tahun as $thn): ?>
                                <option value="<?=$thn->tahun_ajaran?>" <?=($tahun_filter == $thn->tahun_ajaran) ? 'selected' : ''?>>
                                    <?=$thn->tahun_ajaran?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                </form>
            </div>
        </div>

        <!-- TABEL SPP -->
        <div class="box box-success">
            <div class="box-header with-border">
                <i class="fa fa-money"></i>
                <h3 class="box-title">Tagihan SPP</h3>
            </div>
            <div class="box-body">
                <div class="alert alert-info" style="margin-bottom: 10px;">
                    <i class="fa fa-info-circle"></i> <b>Informasi:</b> Tenggat waktu pembayaran SPP setiap bulannya adalah pada <b>tanggal 14</b>.
                </div>
                <div class="table-responsive">
                <table class="table table-bordered table-striped">
                    <thead>
                        <tr>
                            <th width="30"><input type="checkbox" class="check-all"></th>
                            <th width="30">No</th>
                            <th>Bulan</th>
                            <th>Tahun Ajaran</th>
                            <th>Nominal</th>
                            <th>Tenggat Waktu</th>
                            <th>Status</th>
                            <th>Waktu Bayar</th>
                            <th>Tempat Bayar</th>
                            <th style="white-space: nowrap; width: 180px;">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php 
                        $no = 1;
                        foreach($tagihan_spp as $s) { 
                        ?>
                        <tr>
                            <td>
                                <?php if($s->status == 'Belum Lunas') { ?>
                                    <input type="checkbox" class="check-item" value="<?=$s->id?>">
                                <?php } else { ?>
                                    -
                                <?php } ?>
                            </td>
                            <td><?=$no++?></td>
                            <td><?=str_replace("SPP - ", "", $s->jenis_tagihan)?></td>
                            <td><?=$s->tahun_ajaran ? $s->tahun_ajaran : '-'?></td>
                            <td>Rp. <?=number_format($s->nominal,0,',','.')?></td>
                            <td>
                                <?=!empty($s->tenggat_waktu) ? date('d M Y', strtotime($s->tenggat_waktu)) : '-'?>
                            </td>
                            <td>
                                <?php if($s->status == 'Lunas') { ?>
                                    <span class="label label-success">Lunas</span>
                                <?php } else { ?>
                                    <span class="label label-danger">Belum Lunas</span>
                                <?php } ?>
                            </td>
                            <td><?=$s->waktu_bayar ? date('d-m-Y H:i', strtotime($s->waktu_bayar)) : '-'?></td>
                            <td><?=$s->status == 'Lunas' ? 'Loket' : '-'?></td>
                            <td style="white-space: nowrap;">
                                <?php if($s->status == 'Belum Lunas') { ?>
                                    <button class="btn btn-primary btn-xs btn-bayar" data-id="<?=$s->id?>">
                                        <i class="fa fa-money"></i> Bayar
                                    </button>
                                <?php } else { ?>
                                    <a target="_blank" href="<?=base_url('Tagihan/Cetak_Bukti/'.$s->id)?>" class="btn btn-abu-tua btn-xs">
                                        <i class="fa fa-print"></i> Cetak Bukti
                                    </a>
                                <?php } ?>
                                <button class="btn btn-danger btn-xs btn-hapus" data-id="<?=$s->id?>">
                                     <i class="fa fa-trash"></i> Hapus
                                </button>
                            </td>
                        </tr>
                        <?php } ?>
                        <?php if (count($tagihan_spp) == 0): ?>
                        <tr><td colspan="10" class="text-center">Belum ada tagihan SPP. Tambahkan manual di bawah jika perlu.</td></tr>
                        <?php endif; ?>
                    </tbody>
                </table>
                </div>
            </div>
        </div>

        <!-- TABEL LAINNYA -->
        <div class="box box-warning">
            <div class="box-header with-border">
                <i class="fa fa-list"></i>
                <h3 class="box-title">Tagihan</h3>
            </div>
            <div class="box-body">
                <div class="table-responsive">
                <table class="table table-bordered table-striped">
                    <thead>
                        <tr>
                            <th width="30"><input type="checkbox" class="check-all"></th>
                            <th width="30">No</th>
                            <th>Jenis Tagihan</th>
                            <th>Tahun Ajaran</th>
                            <th>Tenggat Waktu</th>
                            <th>Nominal</th>
                            <th>Status</th>
                            <th>Waktu Bayar</th>
                            <th>Tempat Bayar</th>
                            <th style="white-space: nowrap; width: 180px;">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php 
                        $no2 = 1;
                        foreach($tagihan_lainnya as $l) { 
                        ?>
                        <tr>
                            <td style="white-space: nowrap;">
                                <?php if($l->status == 'Belum Lunas') { ?>
                                    <input type="checkbox" class="check-item" value="<?=$l->id?>">
                                <?php } else { ?>
                                    -
                                <?php } ?>
                            </td>
                            <td><?=$no2++?></td>
                            <td><?=$l->jenis_tagihan?></td>
                            <td><?=$l->tahun_ajaran ? $l->tahun_ajaran : '-'?></td>
                            <td><?=!empty($l->tenggat_waktu) ? date('d-m-Y', strtotime($l->tenggat_waktu)) : '-'?></td>
                            <td>Rp. <?=number_format($l->nominal,0,',','.')?></td>
                            <td>
                                <?php if($l->status == 'Lunas') { ?>
                                    <span class="label label-success">Lunas</span>
                                <?php } else { ?>
                                    <span class="label label-danger">Belum Lunas</span>
                                <?php } ?>
                            </td>
                            <td><?=$l->waktu_bayar ? date('d-m-Y H:i', strtotime($l->waktu_bayar)) : '-'?></td>
                            <td><?=$l->status == 'Lunas' ? 'Loket' : '-'?></td>
                            <td style="white-space: nowrap;">
                                <?php if($l->status == 'Belum Lunas') { ?>
                                    <button class="btn btn-primary btn-xs btn-bayar" data-id="<?=$l->id?>">
                                        <i class="fa fa-money"></i> Bayar
                                    </button>
                                <?php } else { ?>
                                    <a target="_blank" href="<?=base_url('Tagihan/Cetak_Bukti/'.$l->id)?>" class="btn btn-abu-tua btn-xs">
                                        <i class="fa fa-print"></i> Cetak Bukti
                                    </a>
                                <?php } ?>
                                <button class="btn btn-danger btn-xs btn-hapus" data-id="<?=$l->id?>">
                                     <i class="fa fa-trash"></i> Hapus
                                </button>
                            </td>
                        </tr>
                        <?php } ?>
                        <?php if (count($tagihan_lainnya) == 0): ?>
                        <tr><td colspan="9" class="text-center">Belum ada tagihan lainnya.</td></tr>
                        <?php endif; ?>
                    </tbody>
                </table>
                </div>
            </div>
        </div>

	</div>
</div>

<!-- Modal Tambah Tagihan -->
<div class="modal fade" id="modal-tagihan">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                  <span aria-hidden="true">&times;</span>
                </button>
                <h4 class="modal-title">Tambah Tagihan</h4>
            </div>
            <form id="form-tagihan">
            <div class="modal-body">
                <input type="hidden" name="id_siswa" value="<?=$id_siswa?>">
                <div class="form-group">
                    <label class="control-label">Jenis Tagihan</label>
                    <select name="kode_tagihan" class="form-control" required>
                        <option value="">--Pilih Jenis Tagihan--</option>
                        <?php foreach($jenis_transaksi as $t) { ?>
                            <option value="<?=$t->id?>"><?=$t->nama?> (<?=$t->tahun_ajaran?>)</option>
                        <?php } ?>
                    </select>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-default" data-dismiss="modal">Batal</button>
                <button type="submit" id="btn-simpan-tagihan" class="btn btn-warning">Simpan</button>
            </div>
            </form>
        </div>
    </div>
</div>

<script>
function TambahTagihanLainnya() {
    $('#form-tagihan')[0].reset();
    $('#modal-tagihan').modal('show');
}

function updateBayarBtn() {
    var checked = $('.check-item:checked').length;
    $('#btn-bayar-terpilih').attr('disabled', checked === 0);
    $('#btn-hapus-terpilih').attr('disabled', checked === 0);
}

$(document).ready(function() {

    // Checkbox logic
    $('.check-all').on('change', function() {
        var isChecked = $(this).is(':checked');
        $(this).closest('table').find('.check-item').prop('checked', isChecked);
        updateBayarBtn();
    });

    $('.check-item').on('change', function() {
        updateBayarBtn();
    });

    // Multi Bayar
    $('#btn-bayar-terpilih').on('click', function() {
        var ids = [];
        $('.check-item:checked').each(function() {
            ids.push($(this).val());
        });

        if(ids.length > 0) {
            Swal({
                title: 'Konfirmasi Pembayaran',
                text: 'Apakah Anda yakin ' + ids.length + ' tagihan terpilih ini sudah dibayar?',
                type: 'question',
                showCancelButton: true,
                confirmButtonColor: '#3085d6',
                cancelButtonColor: '#d33',
                confirmButtonText: 'Ya, Lunas!',
                cancelButtonText: 'Batal'
            }).then((result) => {
                if(result.value) {
                    $.ajax({
                        url: "<?=base_url('Tagihan/Bayar_Multi')?>",
                        type: "POST",
                        data: { ids: ids },
                        dataType: "JSON",
                        success: function(data) {
                            if(data.status) {
                                Swal({
                                    title: 'Lunas!',
                                    text: 'Pembayaran berhasil dikonfirmasi',
                                    type: 'success',
                                    showCancelButton: true,
                                    confirmButtonText: '<i class="fa fa-print"></i> Cetak Bukti',
                                    cancelButtonText: 'Tutup',
                                    confirmButtonColor: '#28a745'
                                }).then((result) => {
                                    if(result.value) {
                                        window.open("<?=base_url('Tagihan/Cetak_Bukti/')?>" + data.ids, '_blank');
                                    }
                                    location.reload();
                                });
                            }
                        },
                        error: function() {
                            Swal('Error', 'Terjadi kesalahan sistem', 'error');
                        }
                    });
                }
            });
        }
    });

    // Multi Hapus
    $('#btn-hapus-terpilih').on('click', function() {
        var ids = [];
        $('.check-item:checked').each(function() {
            ids.push($(this).val());
        });

        if(ids.length > 0) {
            Swal({
                title: 'Hapus ' + ids.length + ' Tagihan?',
                text: 'Semua tagihan terpilih ini akan dihapus permanen!',
                type: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#d33',
                cancelButtonColor: '#3085d6',
                confirmButtonText: 'Ya, Hapus!',
                cancelButtonText: 'Batal'
            }).then((result) => {
                if(result.value) {
                    $.ajax({
                        url: "<?=base_url('Tagihan/Hapus_Multi')?>",
                        type: "POST",
                        data: { ids: ids },
                        dataType: "JSON",
                        success: function(data) {
                            if(data.status) {
                                Swal('Terhapus!', ids.length + ' tagihan berhasil dihapus.', 'success').then(() => {
                                    location.reload();
                                });
                            }
                        },
                        error: function() {
                            Swal('Error', 'Terjadi kesalahan sistem', 'error');
                        }
                    });
                }
            });
        }
    });

    $('#form-tagihan').on('submit', function(e) {
        e.preventDefault();
        $('#btn-simpan-tagihan').attr('disabled', true).text('Menyimpan...');
        $.ajax({
            url: "<?=base_url('Tagihan/Simpan_Manual')?>",
            type: "POST",
            data: $(this).serialize(),
            dataType: "JSON",
            success: function(data) {
                if(data.status) {
                    Swal({
                        title: 'Sukses',
                        text: 'Tagihan berhasil ditambahkan',
                        type: 'success'
                    }).then((result) => {
                        location.reload();
                    });
                }
            },
            error: function() {
                Swal('Error', 'Terjadi kesalahan sistem', 'error');
                $('#btn-simpan-tagihan').attr('disabled', false).text('Simpan');
            }
        });
    });

    $('.btn-bayar').on('click', function() {
        var id_tagihan = $(this).data('id');
        Swal({
            title: 'Konfirmasi Pembayaran',
            text: 'Apakah Anda yakin tagihan ini sudah dibayar?',
            type: 'question',
            showCancelButton: true,
            confirmButtonColor: '#3085d6',
            cancelButtonColor: '#d33',
            confirmButtonText: 'Ya, Lunas!',
            cancelButtonText: 'Batal'
        }).then((result) => {
            if(result.value) {
                $.ajax({
                    url: "<?=base_url('Tagihan/Bayar/')?>" + id_tagihan,
                    type: "POST",
                    dataType: "JSON",
                    success: function(data) {
                        if(data.status) {
                            Swal({
                                title: 'Lunas!',
                                text: 'Pembayaran berhasil dikonfirmasi',
                                type: 'success',
                                showCancelButton: true,
                                confirmButtonText: '<i class="fa fa-print"></i> Cetak Bukti',
                                cancelButtonText: 'Tutup',
                                confirmButtonColor: '#28a745'
                            }).then((result) => {
                                if(result.value) {
                                    window.open("<?=base_url('Tagihan/Cetak_Bukti/')?>" + id_tagihan, '_blank');
                                }
                                location.reload();
                            });
                        }
                    },
                    error: function() {
                        Swal('Error', 'Terjadi kesalahan sistem', 'error');
                    }
                });
            }
        });
    });
    $('.btn-hapus').on('click', function() {
        var id_tagihan = $(this).data('id');
        Swal({
            title: 'Hapus Tagihan?',
            text: 'Data tagihan ini akan dihapus secara permanen!',
            type: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#d33',
            cancelButtonColor: '#3085d6',
            confirmButtonText: 'Ya, Hapus!',
            cancelButtonText: 'Batal'
        }).then((result) => {
            if(result.value) {
                $.ajax({
                    url: "<?=base_url('Tagihan/Hapus/')?>" + id_tagihan,
                    type: "POST",
                    dataType: "JSON",
                    success: function(data) {
                        if(data.status) {
                            Swal('Terhapus!', 'Satu tagihan berhasil dihapus.', 'success').then(() => {
                                location.reload();
                            });
                        }
                    }
                });
            }
        });
    });

});
</script>
