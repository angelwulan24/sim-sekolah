<div class="col-xs-12">
    <ul class="nav nav-tabs" style="margin-bottom: 15px; font-weight: bold;">
        <li class="active"><a data-toggle="tab" href="#tab-tagihan"><i class="fa fa-list"></i> Kelola Tagihan Siswa</a></li>
        <li><a data-toggle="tab" href="#tab-riwayat"><i class="fa fa-history"></i> Riwayat Pembayaran</a></li>
    </ul>

    <div class="tab-content">
        <!-- TAB 1: KELOLA TAGIHAN -->
        <div id="tab-tagihan" class="tab-pane fade in active">
            <div class="box box-primary">
                <div class="box-header">
                    <div class="col-sm-2">
                    <?php $kls= $this->db->query("SELECT id_kelas AS id, nama_kelas AS nama FROM kelas")->result() ?>
                        <select name="kelas" id="kelas" data-placeholder="--Pilih kelas--" class="form-control select2">
                            <option value=""></option>
                        <?php foreach ($kls as $key) {?>    
                            <option value="<?=$key->id?>"><?=$key->nama?></option>
                        <?php } ?>
                        </select>
                    </div>
                </div>
                <div class="box-body">
                    <div class="table-responsive">    	
                        <table id="list-data" class="table table-bordered table-hover" style="width: 100%;">
                            <thead>
                                <tr>
                                  <th style="width: 10px;">No</th>
                                  <th>Foto</th>
                                  <th>Nama Siswa</th>
                                  <th>NIS</th>
                                  <th>Jenis Kelamin</th>
                                  <th>Tempat Lahir</th>
                                  <th>Tanggal Lahir</th>
                                  <th>Orangtua / Wali</th>
                                  <th>No. Telpon</th>
                                  <th>Alamat</th>
                                  <th>Tanggal Masuk</th>
                                  <th>Tahun Ajaran</th>
                                  <th>Kelas</th>
                                  <th>Status</th>
                                  <th width="150">Aksi</th>
                                </tr>
                            </thead>
                            <tbody>
                              
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>

        <!-- TAB 2: RIWAYAT PEMBAYARAN -->
        <div id="tab-riwayat" class="tab-pane fade">
            <div class="box box-success">
                <div class="box-header">
                    <h3 class="box-title">Riwayat Pembayaran Siswa</h3>
                </div>
                <div class="box-body">
                    <!-- FILTER DATA RIWAYAT -->
                    <div class="row" style="margin-bottom: 15px;">
                        <div class="col-md-3">
                            <select id="riwayat_jenis_filter" class="form-control">
                                <option value="">-- Pilih Jenis Filter --</option>
                                <option value="hari">Harian</option>
                                <option value="bulan">Bulanan</option>
                                <option value="tahun">Tahunan</option>
                            </select>
                        </div>
                        
                        <div class="col-md-3" id="riwayat_wrap-filter-hari" style="display:none;">
                            <input type="date" id="riwayat_filter_hari" class="form-control" placeholder="Pilih Hari">
                        </div>
                        
                        <div class="col-md-3" id="riwayat_wrap-filter-bulan" style="display:none;">
                            <input type="month" id="riwayat_filter_bulan" class="form-control">
                        </div>
                        
                        <div class="col-md-3" id="riwayat_wrap-filter-tahun" style="display:none;">
                            <select id="riwayat_filter_tahun" class="form-control">
                                <option value="">-- Pilih Tahun --</option>
                                <?php 
                                    for($i = date('Y'); $i >= date('Y')-5; $i--) {
                                        echo "<option value='$i'>$i</option>";
                                    }
                                ?>
                            </select>
                        </div>
                        <div class="col-md-3">
                            <button id="riwayat_btn-filter" class="btn btn-success btn-sm"><i class="fa fa-filter"></i> Filter</button>
                            <button id="riwayat_btn-reset" class="btn btn-default btn-sm"><i class="fa fa-refresh"></i> Reset</button>
                        </div>
                    </div>
                    <div class="table-responsive">
                        <table id="list-riwayat" class="table table-bordered table-hover" style="width: 100%;">
                            <thead>
                                <tr>
                                    <th style="width: 10px;">No</th>
                                    <th>Waktu Pembayaran</th>
                                    <th>Nama Siswa</th>
                                    <th>NIS</th>
                                    <th>Kelas</th>
                                    <th>Jenis Tagihan</th>
                                    <th>Nominal</th>
                                    <th>Tempat Bayar</th>
                                </tr>
                            </thead>
                            <tbody>
                              
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script type="text/javascript">
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

        load_data ();

        var table_riwayat = $("#list-riwayat").DataTable({
            initComplete: function() {
                var api = this.api();
                $('#list-riwayat input')
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
                "url": "<?= base_url('Tagihan/getRiwayatData') ?>",
                "type": "POST",
                "data": function (d) {
                    d.jenis = $('#riwayat_jenis_filter').val();
                    if(d.jenis === 'hari'){
                        d.tanggal = $('#riwayat_filter_hari').val();
                    } else if(d.jenis === 'bulan'){
                        d.tanggal = $('#riwayat_filter_bulan').val();
                    } else if(d.jenis === 'tahun'){
                        d.tanggal = $('#riwayat_filter_tahun').val();
                    }
                }
            },
            columns: [
                {
                    "data": "id",
                    "orderable": false,
                    "searchable": false
                },
                {"data": "waktu_bayar"},
                {"data": "nama"},
                {"data": "nis"},
                {"data": "kelas"},
                {"data": "jenis_tagihan"},
                {
                    "data": "nominal",
                    "render": function(data, type, row) {
                        return "Rp. " + parseInt(data).toLocaleString('id-ID');
                    }
                },
                {
                    "data": "id_pemasukan",
                    "render": function(data, type, row) {
                        if (data) {
                            return '<span class="label label-success">Loket</span>';
                        } else {
                            return '<span class="label label-primary">Online</span>';
                        }
                    }
                }
            ],
            order: [[1, 'desc']],
            rowCallback: function(row, data, iDisplayIndex) {
                var info = this.fnPagingInfo();
                var page = info.iPage;
                var length = info.iLength;
                var index = page * length + (iDisplayIndex + 1);
                $('td:eq(0)', row).html(index);
            }
        });

    function load_data(is_kelas){

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
                "type": "POST",
                "data":{is_kelas : is_kelas }
            },
            columns: [
                {
                    "data": "id",
                    "orderable": false,
                    "searchable": false
                },
                {
                    "data": "foto",
                    "render": function(data, type, row) {
                        if(data) return '<img src="<?=base_url("assets/images/siswa/")?>' + data + '" width="50" height="50" style="object-fit:cover; border-radius:5px;">';
                        return '<img src="https://via.placeholder.com/50" width="50" height="50" style="object-fit:cover; border-radius:5px;">';
                    }
                },
                {"data": "name"},
                {"data": "nis"},
                {"data": "sex"},
                {"data": "tempat"},
                {"data": "tanggal"},
                {"data": "orangtua_wali"},
                {"data": "telpon"},
                {"data": "alamat"},
                {"data": "tanggal_masuk"},
                {"data": "tahun_ajaran"},
                {"data": "nama_kelas"},
                {
                    "data": "status",
                    "render": function(data, type, row) {
                        var stat = "success";
                        if(data == "Berhenti") stat = "danger";
                        return '<span class="label label-'+stat+' control-label">'+data+'</span>';
                    }
                },
                {
                    "data": "view",
                    "orderable": false,
                    "searchable": false
                }
            ],
            order: [[1, 'asc']],
            rowId: function(a){
                return a;
            },
            rowCallback: function(row, data, iDisplayIndex) {
                var info = this.fnPagingInfo();
                var page = info.iPage;
                var length = info.iLength;
                var index = page * length + (iDisplayIndex + 1);
                $('td:eq(0)', row).html(index);
            }
        });
    }

        $('#kelas').on('change',function(){

            var kelas = $(this).val();
            $('#list-data').DataTable().destroy();

            if (kelas != ''){
                load_data(kelas);
            }
            else{
                load_data();
            }
       });

    $('#riwayat_jenis_filter').change(function(){
        $('#riwayat_wrap-filter-hari').hide();
        $('#riwayat_wrap-filter-bulan').hide();
        $('#riwayat_wrap-filter-tahun').hide();
        
        $('#riwayat_filter_hari').val('');
        $('#riwayat_filter_bulan').val('');
        $('#riwayat_filter_tahun').val('');
        
        var jenis = $(this).val();
        if(jenis === 'hari') {
            $('#riwayat_wrap-filter-hari').show();
        } else if(jenis === 'bulan') {
            $('#riwayat_wrap-filter-bulan').show();
        } else if(jenis === 'tahun') {
            $('#riwayat_wrap-filter-tahun').show();
        }
    });

    $('#riwayat_btn-filter').click(function(){
        table_riwayat.ajax.reload();
    });

    $('#riwayat_btn-reset').click(function(){
        $('#riwayat_jenis_filter').val('').trigger('change');
        table_riwayat.ajax.reload();
    });

	});

    function reload(){
        table.ajax.reload(null,false);
    }
</script>
