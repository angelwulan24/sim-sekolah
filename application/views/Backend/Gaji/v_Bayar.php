<div class="row">
    <div class="col-md-12">
        <div class="box box-success">
            <div class="box-header with-border">
                <h3 class="box-title">Form Pembayaran Gaji Guru</h3>
            </div>
            <?= form_open('','role="form" id="form-bayar"') ?>
                <div class="box-body">

                    <div class="alert alert-info alert-dismissible" style="background-color: #e8f4fd !important; border-color: #b3d7f5 !important; color: #31708f !important;">
                        <button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button>
                        <h4><i class="icon fa fa-info-circle"></i> Catatan Penting:</h4>
                        <p>Pengaturan gaji guru hanya dapat dilakukan satu kali dalam setiap bulan dan berlaku secara serentak untuk seluruh data guru.</p>
                        <p>Mohon pastikan seluruh data dan nominal telah diperiksa dengan teliti sebelum proses penyimpanan dilakukan.</p>
                    </div>

                    <!-- Pengaturan Gaji Global -->
                    <div class="row" style="margin-bottom: 25px; background: #fbfbfb; padding: 15px; border-radius: 5px; border: 1px solid #efefef;">
                        <div class="col-md-6">
                            <label>Pilih Gaji pada Bulan</label>
                            <?php 
                               $t = Date('Y'); 
                               $b = array('Juli','Agustus','September','Oktober','November','Desember','Januari','Februari','Maret','April','Mei','Juni');
                            ?>
                            <select name="bulan" required class="form-control" data-placeholder="-- Pilih Bulan --">
                                <option value="">-- Pilih Bulan --</option>
                                <?php 
                                    $current_month_index = date('n') - 1;
                                    // foreach ($b as $key => $bulan_nama) {
                                        
                                    //     // Handle specific Y/Y transition
                                    //     $next_year = $t - 2;
                                    //     if ($key >= 6) { // Jika saat ini semester genap (Jan-Juni), cek apakah sudah semester genap tahun ajaran berikutnya
                                    //         $next_year = $t-1;
                                    //     }

                                    //     $periode = $bulan_nama.'-'.$next_year;

                                    //     if (!in_array($periode, $paid_months)) {
                                    //         echo '<option value="'.$periode.'">'.$periode.'</option>';
                                    //     }

                                    //     // if ($key <= $current_month_index) { // Hanya tampilkan bulan berjalan dan sebelumnya
                                    //     //     $periode = $bulan_nama.'-'.$t;
                                    //     //     if (!in_array($periode, $paid_months)) {
                                    //     //         echo '<option value="'.$periode.'">'.$periode.'</option>';
                                    //     //     }
                                    //     // }
                                    // }
                                    foreach ($b as $key => $bulan_nama) {
                                        
                                        // Handle specific Y/Y transition
                                        $next_year = $t - 1;
                                        if ($key >= 6) { // Jika saat ini semester genap (Jan-Juni), cek apakah sudah semester genap tahun ajaran berikutnya
                                            $next_year = $t;
                                        }

                                        $periode = $bulan_nama.'-'.$next_year;

                                        if (!in_array($periode, $paid_months)) {
                                            echo '<option value="'.$periode.'">'.$periode.'</option>';
                                        }

                                        // if ($key <= $current_month_index) { // Hanya tampilkan bulan berjalan dan sebelumnya
                                        //     $periode = $bulan_nama.'-'.$t;
                                        //     if (!in_array($periode, $paid_months)) {
                                        //         echo '<option value="'.$periode.'">'.$periode.'</option>';
                                        //     }
                                        // }
                                    }
                                ?>
                            </select>
                        </div>
                        <div class="col-md-6">
                            <label>Tarif per Jam (Rp)</label>
                            <input type="number" name="tarif" id="tarif" class="form-control" value="<?=$tarif_per_jam?>" required>
                            <small class="text-muted">*Ubah nilai ini jika ada kenaikan/penurunan tarif jam mengajar guru. Perubahan akan merubah nominal pembayaran default untuk selanjutnya.</small>
                        </div>
                    </div>

                    <h4 style="margin-bottom: 20px; font-weight: bold; border-left: 4px solid #00a65a; padding-left: 10px;">Daftar Guru</h4>

                    <?php foreach ($guru as $i => $g): ?>
                        <?php $is_berhenti = ($g->status == 'Berhenti'); ?>
                        <div class="row" style="margin-bottom: 20px; border-bottom: 1px solid #f4f4f4; padding-bottom: 20px;">
                            <div class="col-md-5">
                                <div class="media">
                                    <div class="media-left">
                                        <?php $foto = !empty($g->foto) ? base_url('assets/images/guru/'.$g->foto) : base_url('assets/images/no-image.png'); ?>
                                        <img src="<?=$foto?>" class="media-object img-circle" style="width: 70px; height: 70px; object-fit: cover; border: 2px solid #ddd;">
                                    </div>
                                    <div class="media-body" style="padding-left: 10px;">
                                        <h4 class="media-heading" style="margin-top: 5px; font-weight: 600;"><?=$g->name?></h4>
                                        <p style="margin: 0; font-size: 13px;" class="text-muted">NIP: <?=$g->nip?></p>
                                        <p style="margin: 0; font-size: 13px;">
                                            <?=$g->bidang?> | <?=$g->sex?>
                                            <div style="margin-top: 3px;">
                                                <?php 
                                                    if($g->status == 'Berhenti') echo '<span class="label label-danger">Berhenti</span>';
                                                    else if($g->status == 'Cuti') echo '<span class="label label-warning">Cuti</span>';
                                                    else echo '<span class="label label-success">Aktif</span>';
                                                ?>
                                            </div>
                                        </p>
                                    </div>
                                </div>
                            </div>
                            
                            <div class="col-md-7">
                                <div class="row" style="margin-top: 15px;">
                                    <div class="col-md-6 form-group-jam">
                                        <?php if(!$is_berhenti): ?>
                                            <input type="hidden" name="id_guru[]" value="<?=$g->id?>">
                                            <label style="font-size: 13px;">Jumlah Jam</label>
                                            <input type="number" name="jam[]" class="form-control jam-input" data-index="<?=$i?>" placeholder="Cth: 16">
                                        <?php else: ?>
                                            <label style="font-size: 13px;">Jumlah Jam</label>
                                            <input type="text" class="form-control" disabled placeholder="Guru Berhenti">
                                        <?php endif; ?>
                                    </div>
                                    <div class="col-md-6">
                                        <label style="font-size: 13px;">Total Gaji (Rp)</label>
                                        <input type="text" id="total_<?=$i?>" class="form-control" readonly <?=$is_berhenti ? 'disabled' : ''?> placeholder="0">
                                    </div>
                                </div>
                            </div>
                        </div>
                    <?php endforeach; ?>

                </div>
                <div class="box-footer text-right">
                    <button type="button" onclick="history.back()" class="btn btn-default" style="margin-right: 5px;">Kembali</button>
                    <button type="submit" id="btn-simpan" class="btn btn-primary">Bayarkan Gaji</button>
                </div>
            <?= form_close() ?>
        </div>
    </div>
</div>

<script type="text/javascript">
    $(document).ready(function() {
        // Otomatis hitung total saat jam atau tarif diketik
        $(document).on('keyup input change', '.jam-input, #tarif', function() {
            var tarif = $('#tarif').val() || 0;
            
            // Loop semua input jam untuk memperbarui total
            $('.jam-input').each(function() {
                var jam = $(this).val() || 0;
                var total = jam * tarif;
                var index = $(this).data('index');
                $('#total_' + index).val(total);
            });

            // Clear validation error when user types/changes input
            if ($(this).hasClass('jam-input')) {
                var val = $(this).val();
                if (val !== '' && val !== null) {
                    $(this).closest('.form-group-jam').removeClass('has-error');
                    $(this).parent().find('.help-block-jam').remove();
                }
            }
        });

        $('#form-bayar').submit(function(e){
            e.preventDefault();
            var form = $(this);

            var empty_jam = false;
            $('.jam-input').each(function() {
                var val = $(this).val();
                if (val === '' || val === null || val === undefined) {
                    empty_jam = true;
                    $(this).closest('.form-group-jam').addClass('has-error');
                    if ($(this).parent().find('.help-block-jam').length === 0) {
                        $(this).after('<span class="help-block help-block-jam" style="color: #dd4b39; display: block; margin-top: 5px;">Jam kerja harus diisi</span>');
                    }
                } else {
                    $(this).closest('.form-group-jam').removeClass('has-error');
                    $(this).parent().find('.help-block-jam').remove();
                }
            });

            if (empty_jam) {
                Swal({
                    title: 'Peringatan',
                    text: 'Ada jam kerja guru yang belum diisi. Harap isi seluruh jam kerja guru aktif.',
                    type: 'warning'
                });
                return false;
            }
            
            Swal({
                title: 'Konfirmasi Pembayaran Gaji',
                text: 'Apakah Anda yakin ingin menyimpan dan membayarkan gaji guru untuk periode ini?',
                type: 'question',
                showCancelButton: true,
                confirmButtonColor: '#3085d6',
                cancelButtonColor: '#d33',
                confirmButtonText: 'Ya, Bayarkan',
                cancelButtonText: 'Batal'
            }).then((result) => {
                if (result.value) {
                    $('#btn-simpan').text('Menyimpan...');
                    $('#btn-simpan').attr('disabled',true);
                    
                    var isi = form.serialize();
                    $.ajax({
                        url: "<?=base_url($this->uri->segment(1).'/Simpan')?>",
                        type: "POST",
                        data: isi,
                        dataType: "JSON",
                        success: function(data){
                            if(data.status){
                                Swal({
                                    title: 'Sukses',
                                    text: 'Pembayaran Gaji Guru Berhasil! Semua data yang diisi telah disimpan.',
                                    type: 'success',
                                    showConfirmButton: false,
                                    timer: 2000
                                });
                                setTimeout(function(){
                                    window.location.href = "<?=base_url('Pengeluaran')?>";
                                }, 2000);
                            }else{
                                $('#btn-simpan').text('Bayarkan Gaji');
                                $('#btn-simpan').attr('disabled',false);
                                Swal({
                                    title: 'Gagal',
                                    text: 'Gagal menyimpan / tidak ada jam yang diinput.',
                                    type: 'error'
                                });
                            }
                        },
                        error: function(){
                            $('#btn-simpan').text('Bayarkan Gaji');
                            $('#btn-simpan').attr('disabled',false);
                            Swal({
                                title: 'Gagal',
                                text: 'Terjadi kesalahan pada server.',
                                type: 'error'
                            });
                        }
                    });
                }
            });
        });
    });
</script>
