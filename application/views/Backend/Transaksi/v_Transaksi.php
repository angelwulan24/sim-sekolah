<div class="col-xs-12">
    <div class="box box-primary">
        <div class="box-header">
            <?php if ($this->session->userdata('role') == 1) { ?>
            <button class="btn btn-danger" id="btn-hapus-terpilih" disabled onclick="HapusTerpilih()">
                <i class="fa fa-trash"></i> Hapus Terpilih
            </button>
            <?php } ?>
            <div class="pull-right">
                <?php if ($this->session->userdata('role') == 1) { ?>
                <a href="#" onclick="Tambah()" class="btn btn-primary">Tambah Data </a>
                <?php } ?>
            </div>

        </div>
        <div class="box-body">
            <div class="table-responsive">      
                <table id="list-data" class="table table-bordered table-hover">
                    <thead>
                        <tr>
                            <th width="30"><input type="checkbox" class="check-all"></th>
                            <th style="width: 10px;">No</th>
                            <th>Kode Tagihan</th>
                            <th>Jenis Tagihan</th>
                            <th>Tahun Ajaran</th>
                            <th>Kelas</th>
                            <th>Nominal</th>
                            <th>Tenggat Waktu</th>
                            <th width="200">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                      
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<div class="modal fade" id="modal-form">
    <div class="modal-dialog">
<?= form_open('','class="modal-content" role = "form" id = "form"')?>
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                  <span aria-hidden="true">&times;</span>
                </button>
                <h4 class="modal-title"></h4>
            </div>
            <div class="modal-body">
                <input type="hidden" name="id" value="">
                <!-- Tipe transaksi dihilangkan, otomatis KM di controller -->
                <div class="form-group">
                    <label class="control-label"> Kode Tagihan</label>
                    <div><input type="text" readonly="" value="" placeholder="Kode Tagihan" autocomplete="off" name="kode" class="form-control"></div>
                </div>
                <div class="form-group">
                    <label class="control-label"> Jenis Tagihan</label>
                    <div><input type="text" id="nama" value="" required="" placeholder="Jenis Tagihan (Cth: SPP)" autocomplete="off" name="nama" class="form-control"></div>
                </div>
                <div class="form-group">
                    <label class="control-label"> Tahun Ajaran</label>
                    <div>
                        <select name="tahun_ajaran" required="" class="form-control">
                            <?php
                            $current_sy = current_school_year();
                            $years = explode('/', $current_sy);
                            $start_year = (int)$years[0];
                            for ($i = 0; $i < 5; $i++) {
                                $y = $start_year + $i;
                                $val = $y . '/' . ($y + 1);
                                echo '<option value="' . $val . '">' . $val . '</option>';
                            }
                            ?>
                        </select>
                    </div>
                </div>
                <div class="form-group">
                    <label class="control-label"> Kelas</label>
                    <?php $kls= $this->db->query("SELECT id_kelas AS id, nama_kelas AS nama FROM kelas")->result() ?>
                    <select name="kelas" required="" class="form-control">
                        <option value="">-- Pilih Kelas --</option>
                        <option value="Semua">Semua Kelas</option>
                        <?php foreach ($kls as $key) {?>    
                            <option value="<?=$key->id?>"><?=$key->nama?></option>
                        <?php } ?>
                    </select>
                </div>
                <div class="form-group">
                    <label class="control-label"> Nominal</label>
                    <div><input type="text" value="" required="" onkeypress="return Angka(this)" placeholder="Nominal" autocomplete="off" name="nominal" class="form-control"></div>
                </div>
                <div class="form-group" id="row-bulan-spp" style="display: none;">
                    <div class="row">
                        <div class="col-md-6">
                            <label class="control-label">Bulan Awal</label>
                            <select name="bulan_awal" class="form-control">
                                <option value="Januari">Januari</option>
                                <option value="Februari">Februari</option>
                                <option value="Maret">Maret</option>
                                <option value="April">April</option>
                                <option value="Mei">Mei</option>
                                <option value="Juni">Juni</option>
                                <option value="Juli" selected>Juli</option>
                                <option value="Agustus">Agustus</option>
                                <option value="September">September</option>
                                <option value="Oktober">Oktober</option>
                                <option value="November">November</option>
                                <option value="Desember">Desember</option>
                            </select>
                        </div>
                        <div class="col-md-6">
                            <label class="control-label">Bulan Akhir</label>
                            <select name="bulan_akhir" class="form-control">
                                <option value="Januari">Januari</option>
                                <option value="Februari">Februari</option>
                                <option value="Maret">Maret</option>
                                <option value="April">April</option>
                                <option value="Mei">Mei</option>
                                <option value="Juni" selected>Juni</option>
                                <option value="Juli">Juli</option>
                                <option value="Agustus">Agustus</option>
                                <option value="September">September</option>
                                <option value="Oktober">Oktober</option>
                                <option value="November">November</option>
                                <option value="Desember">Desember</option>
                            </select>
                        </div>
                    </div>
                </div>
                <div class="form-group">
                    <label class="control-label"> Tenggat Waktu </label>
                    <div><input type="date" id="input-tenggat" value="" autocomplete="off" name="tenggat_waktu" class="form-control"></div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-default" data-dismiss="modal">Batal</button>
                <button type="submit" id="simpan"  class="btn btn-primary">Simpan</button>
            </div>
<?= form_close()?>
    </div>
</div>


<script type="text/javascript">

    var label;
    var table;
    $(document).ready(function(){
        $.fn.dataTableExt.oApi.fnPagingInfo = function(oSettings){
            return {
                "iStart": oSettings._iDisplayStart,
                "iEnd": oSettings.fnDisplayEnd(),
                "iLength": oSettings._iDisplayLength,
                "iTotal": oSettings.fnRecordsTotal(),
                "iFilteredTotal": oSettings.fnRecordsDisplay(),
                "iPage": Math.ceil(oSettings._iDisplayStart / oSettings._iDisplayLength),
                "iTotalPages": Math.ceil(oSettings.fnRecordsDisplay() / oSettings._iDisplayLength)
            };
        };

       table =  $("#list-data").DataTable({
            initComplete: function() {
                var api = this.api();
                $('#list-data input')
                    .off('.DT')
                    .on('keyup.DT', function(e) {
                        api.search(this.value).draw();
                    });
            },
            oLanguage: {
                sSearch       :"<i class='fa fa-search fa-fw'></i> Cari: ",
                sLengthMenu   :"Tampilkan _MENU_ data",
                sInfo         :"Menampilkan _START_ sampai _END_ dari _TOTAL_ data",
                sInfoFiltered :"(disaring dari _MAX_ total data)", 
                sZeroRecords  :"Oops..data kosong", 
                sEmptyTable   :"Data kosong.", 
                sInfoEmpty    :"Menampilkan 0 sampai 0 data",
                sProcessing   :"Sedang memproses...", 
                oPaginate: {
                    sPrevious :"Sebelumnya",
                    sNext     :"Selanjutnya",
                    sFirst    :"Pertama",
                    sLast     :"Terakhir"
                }
            },
            processing: true,
            serverSide: true,
            ajax: {
                "url": "<?= base_url().$this->uri->segment(1).'/getData'?>",
                "type": "POST"
            },
            columns: [
                {
                    "data": "kode_tagihan",
                    "orderable": false,
                    "searchable": false,
                    "render": function(data, type, row) {
                        return '<input type="checkbox" class="check-item" value="' + data + '">';
                    }
                },
                {
                    "data": null,
                    "orderable": false,
                    "searchable": false
                },
                {"data": "kode_tagihan"},
                {"data": "nama_tagihan"},
                {"data": "tahun_ajaran"},
                {"data": "kelas"},
                {"data": "nominal_tagihan",render: $.fn.dataTable.render.number('.',',','')},
                {"data": "tenggat_waktu"},
                {
                    "data": "view",
                    "orderable": false,
                    "searchable": false
                }
            ],
            order: [[2, 'asc']],
            rowId: function(a){
                return a.kode_tagihan;
            },
            rowCallback: function(row, data, iDisplayIndex) {
                var info = this.fnPagingInfo();
                var page = info.iPage;
                var length = info.iLength;
                var index = page * length + (iDisplayIndex + 1);
                $('td:eq(1)', row).html(index);
            }
        });

        $(document).on('change', '.check-all', function() {
            var isChecked = $(this).is(':checked');
            $('.check-item').prop('checked', isChecked);
            updateHapusBtn();
        });

        $(document).on('change', '.check-item', function() {
            updateHapusBtn();
        });

        table.on('draw', function() {
            $('.check-all').prop('checked', false);
            updateHapusBtn();
        });

        $('#form').validate({
            errorElement: 'div',
            errorClass: 'help-block',
            focusInvalid: false,
            ignore: "",
            highlight: function (e) {
                $(e).closest('.form-group').removeClass('has-info').addClass('has-error');
            },
            success: function (e) {
                $(e).closest('.form-group').removeClass('has-error');//.addClass('has-info');
                $(e).remove();
            },
            errorPlacement: function (error, element) {
                if(element.is('input[type=radio]')) {
                    var controls = element.closest('div[class*="ra"]');
                    if(controls.find(':radio').length > 0) controls.append(error);
                    else error.insertAfter(element.nextAll('.lbl:eq(0)').eq(0));
                }
                else if(element.is('.select2')) {
                    error.insertAfter(element.siblings('[class*="select2-container"]:eq(0)'));
                }
                else error.insertAfter(element.parent());
            },
            submitHandler: function (form) {
                var textConfirm = label == 'simpan' ? 'Apakah Anda yakin ingin menambah data ini?' : 'Apakah Anda yakin ingin mengubah data ini?';
                Swal({
                    title: 'Konfirmasi',
                    text: textConfirm,
                    type: 'question',
                    showCancelButton: true,
                    confirmButtonColor: '#3085d6',
                    cancelButtonColor: '#d33',
                    confirmButtonText: 'Ya, Simpan',
                    cancelButtonText: 'Batal'
                }).then((result) => {
                    if (result.value) {
                        $('#simpan').text('Menyimpan...');
                        $('#simpan').attr('disabled',true);
                        var url,method;
                        if (label == 'simpan'){
                            url = '<?=base_url($this->uri->segment(1).'/Simpan')?>';
                            method = 'Tambah';
                        }
                        else {
                            url = '<?=base_url($this->uri->segment(1).'/Ubah')?>';
                            method = 'Ubah';
                        }
                        var isi = new FormData($('#form')[0]);
                        $.ajax({
                            url: url,
                            type:"POST",
                            data: isi,
                            contentType:false,
                            processData:false,
                            dataType:"JSON",
                            success:function(data){
                                $('#modal-form').modal('hide');
                                $('#simpan').text('Simpan');
                                $('#simpan').attr('disabled',false);
                                if (data.status) {
                                    Swal({
                                        title: 'Berhasil',
                                        text: 'Data berhasil ' + (label == 'simpan' ? 'disimpan' : 'diubah'),
                                        type: 'success'
                                    }).then((result) => {
                                        location.reload();
                                    });
                                } else {
                                    Swal({
                                        title: 'Gagal',
                                        text: 'Gagal memproses data. Silahkan coba lagi.',
                                        type: 'error'
                                    });
                                }
                            },
                            error: function(jqXHR, textStatus, errorThrown) {
                                $('#modal-form').modal('hide');
                                $('#simpan').text('Simpan');
                                $('#simpan').attr('disabled',false);
                                Swal({
                                    title: 'Error',
                                    text: 'Terjadi kesalahan sistem: ' + textStatus,
                                    type: 'error'
                                });
                            }
                        });
                    }
                });
            },
            invalidHandler: function (form) {}
        });

        // Setup SPP Logic for Tenggat Waktu
        $('#nama').on('input', function() {
            var val = $(this).val().toUpperCase();
            if(val.includes('SPP')) {
                $('#input-tenggat').attr('type', 'text').val('Setiap Bulan').prop('readonly', true);
                $('#row-bulan-spp').show();
            } else {
                if($('#input-tenggat').attr('type') === 'text') {
                    $('#input-tenggat').attr('type', 'date').val('').prop('readonly', false);
                }
                $('#row-bulan-spp').hide();
            }
        });

    });

    function reload(){
        table.ajax.reload(null,false);
    }


    function sweet(judul,text,tipe){
        Swal({
            title: judul,
            text: text,
            type: tipe
        });
    };

    function Tambah(){
        label = 'simpan';
        $('#form')[0].reset();
        $('[name="tahun_ajaran"]').val('<?=current_school_year()?>');
        $('#input-tenggat').attr('type', 'date').prop('readonly', false);
        $('.form-group').removeClass('has-error');
        $('.help-block').empty();  
        
        $.ajax({
            url:"<?=base_url($this->uri->segment(1).'/buat_kode/')?>KM",
            type:"GET",
            dataType:"JSON",
            success:function(data){
                $('[name="kode"]').val(data);
            },
            error: function (jqXHR, textStatus, errorThrown){
                sweet('Oops...','Data tidak dapat diambil','error');
            }
        });

        $('#modal-form').appendTo("body").modal('show');
        $('#nama').attr('disabled',false);
        $('.modal-title').text('Tambah Jenis Tagihan');
    }

    function Hapus(id){
        Swal({
            title: 'Ingin menghapus data?',
            text: "Data yang dihapus tidak dapat dikembalikan!",
            type: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#3085d6',
            cancelButtonColor: '#d33',
            confirmButtonText: 'Ya, Hapus!'
        }).then((result) => {
            if (result.value) {
                $.ajax({
                    url: "<?=base_url($this->uri->segment(1).'/Hapus')?>",
                    type:"POST",
                    data: {id:id},
                    dataType:"JSON",
                    success:function(data){
                        reload();
                        sweet('Terhapus!','Data berhasil dihapus.','success');
                    },
                    error: function (jqXHR, textStatus, errorThrown){
                        sweet('Oops...','Data gagal dihapus','error');
                    }
                });
            }
        });
    }

    function Ubah(id){
        label = 'ubah';
        $('#form')[0].reset();
        $('.form-group').removeClass('has-error');
        $('.help-block').empty();

        $.ajax({
            url: "<?=base_url($this->uri->segment(1).'/edit/')?>"+id,
            type:"GET",
            dataType:"JSON",
            success:function(data){
                $('#nama').attr('disabled',true);
                $('[name="id"]').val(data.kode_tagihan);
                $('[name="nama"]').val(data.nama_tagihan);
                $('[name="kode"]').val(data.kode_tagihan);
                $('[name="nominal"]').val(data.nominal_tagihan);
                $('[name="tenggat_waktu"]').val(data.tenggat_waktu);
                $('[name="tahun_ajaran"]').val(data.tahun_ajaran);
                $('[name="kelas"]').val(data.id_kelas ? data.id_kelas : 'Semua');

                var nama_val = data.nama_tagihan.toUpperCase();
                if(nama_val.includes('SPP')) {
                    $('#input-tenggat').attr('type', 'text').val('Setiap Bulan').prop('readonly', true);
                    $('#row-bulan-spp').show();
                } else {
                    $('#input-tenggat').attr('type', 'date').val(data.tenggat_waktu).prop('readonly', false);
                    $('#row-bulan-spp').hide();
                }
                $('#modal-form').appendTo("body").modal('show');
                $('.modal-title').text('Ubah Data'); 
            },
            error: function (jqXHR, textStatus, errorThrown){
                sweet('Oops...','Data tidak dapat diambil','error');
            }
        });
    }

    function updateHapusBtn() {
        var checked = $('.check-item:checked').length;
        $('#btn-hapus-terpilih').attr('disabled', checked === 0);
    }

    function HapusTerpilih() {
        var ids = [];
        $('.check-item:checked').each(function() {
            ids.push($(this).val());
        });

        if (ids.length > 0) {
            Swal({
                title: 'Hapus ' + ids.length + ' Jenis Tagihan?',
                text: "Semua jenis tagihan terpilih ini akan dihapus permanen beserta relasinya!",
                type: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#d33',
                cancelButtonColor: '#3085d6',
                confirmButtonText: 'Ya, Hapus!',
                cancelButtonText: 'Batal'
            }).then((result) => {
                if (result.value) {
                    $.ajax({
                        url: "<?=base_url($this->uri->segment(1).'/Hapus_Multi')?>",
                        type: "POST",
                        data: {ids: ids},
                        dataType: "JSON",
                        success: function(data) {
                            if (data.status) {
                                reload();
                                sweet('Terhapus!', ids.length + ' jenis tagihan berhasil dihapus.', 'success');
                            } else {
                                sweet('Gagal', 'Terjadi kesalahan saat menghapus data.', 'error');
                            }
                        },
                        error: function() {
                            sweet('Oops...', 'Koneksi ke server gagal', 'error');
                        }
                    });
                }
            });
        }
    }
</script>