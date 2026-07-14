<script type="text/javascript">
    $(document).on('keyup', '.jam-input', function() {
        var jam = $(this).val() || 0;
        var tarif = $('#gaji_per_jam').val() || 0;
        var index = $(this).data('index');
        $('#total_' + index).val(jam * tarif);
    });
</script>

<style>
    .btn-pastel-gaji { background-color: #A5D6A7 !important; color: #2D4A3E !important; border: 1px solid #94c396 !important; font-weight: bold; }
    .btn-pastel-gaji:hover { background-color: #94c396 !important; color: #2D4A3E !important; }
</style>

<div class="col-xs-12">
    <ul class="nav nav-tabs" style="margin-bottom: 15px; font-weight: bold;">
        <li class="active"><a data-toggle="tab" href="#tab-gaji"><i class="fa fa-calculator"></i> Kelola Gaji Guru</a></li>
        <li><a data-toggle="tab" href="#tab-riwayat-gaji"><i class="fa fa-history"></i> Riwayat Pembayaran Gaji</a></li>
    </ul>

    <div class="tab-content">
        <!-- TAB 1: KELOLA GAJI GURU -->
        <div id="tab-gaji" class="tab-pane fade in active">
            <div class="box box-primary">
                <div class="box-header">
                    <div class="pull-right">
                        <a href="<?=base_url('Gaji/Bayar')?>" class="btn btn-primary btn-sm">Form Pembayaran Gaji</a>
                    </div>
                </div>
                <div class="box-body">
                    <div class="table-responsive">    	
                        <table id="list-data" class="table table-bordered table-hover" style="width: 100%;">
                            <thead>
                                <tr>
                                    <th style="width: 10px;">No</th>
                                    <th>Nama Guru</th>
                                    <th>NIP</th>
                                    <th>Jenis Kelamin</th>
                                    <th>HP</th>
                                    <th>Aksi</th>
                                </tr>
                            </thead>
                            <tbody></tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>

        <!-- TAB 2: RIWAYAT PEMBAYARAN GAJI -->
        <div id="tab-riwayat-gaji" class="tab-pane fade">
            <div class="box box-success">
                <div class="box-header">
                    <h3 class="box-title">Riwayat Pembayaran Gaji Guru</h3>
                </div>
                <div class="box-body">
                    <!-- FILTER DATA RIWAYAT GAJI -->
                    <div class="row" style="margin-bottom: 15px;">
                        <div class="col-md-3">
                            <select id="riwayat_gaji_jenis_filter" class="form-control">
                                <option value="">-- Pilih Jenis Filter --</option>
                                <option value="hari">Harian</option>
                                <option value="bulan">Bulanan</option>
                                <option value="tahun">Tahunan</option>
                            </select>
                        </div>
                        
                        <div class="col-md-3" id="riwayat_gaji_wrap-filter-hari" style="display:none;">
                            <input type="date" id="riwayat_gaji_filter_hari" class="form-control" placeholder="Pilih Hari">
                        </div>
                        
                        <div class="col-md-3" id="riwayat_gaji_wrap-filter-bulan" style="display:none;">
                            <input type="month" id="riwayat_gaji_filter_bulan" class="form-control">
                        </div>
                        
                        <div class="col-md-3" id="riwayat_gaji_wrap-filter-tahun" style="display:none;">
                            <select id="riwayat_gaji_filter_tahun" class="form-control">
                                <option value="">-- Pilih Tahun --</option>
                                <?php 
                                    for($i = date('Y'); $i >= date('Y')-5; $i--) {
                                        echo "<option value='$i'>$i</option>";
                                    }
                                ?>
                            </select>
                        </div>
                        <div class="col-md-3">
                            <button id="riwayat_gaji_btn-filter" class="btn btn-success btn-sm"><i class="fa fa-filter"></i> Filter</button>
                            <button id="riwayat_gaji_btn-reset" class="btn btn-default btn-sm"><i class="fa fa-refresh"></i> Reset</button>
                        </div>
                    </div>

                    <div class="table-responsive">
                        <table id="list-riwayat-gaji" class="table table-bordered table-hover" style="width: 100%;">
                            <thead>
                                <tr>
                                    <th style="width: 10px;">No</th>
                                    <th>Waktu Pembayaran</th>
                                    <th>Nama Guru</th>
                                    <th>NUPTK / NIP</th>
                                    <th>Periode</th>
                                    <th>Jam Kerja</th>
                                    <th>Gaji / Jam</th>
                                    <th>Total Gaji</th>
                                    <th>Tempat Bayar</th>
                                </tr>
                            </thead>
                            <tbody></tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>



<!-- Modal Removed -->


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
                    "data": "id",
                    "orderable": false,
                    "searchable": false
                },
                {"data": "name"},
                {"data": "nip"},
                {"data": "sex"},
                {"data": "number"},
                 {
                    "data": "view",
                    "orderable": false,
                    "searchable": false
                }
            ],
            order: [[1, 'DESC']],
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

        var table_riwayat_gaji = $("#list-riwayat-gaji").DataTable({
            initComplete: function() {
                var api = this.api();
                $('#list-riwayat-gaji input')
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
                "url": "<?= base_url('Gaji/getRiwayatData') ?>",
                "type": "POST",
                "data": function (d) {
                    d.jenis = $('#riwayat_gaji_jenis_filter').val();
                    if(d.jenis === 'hari'){
                        d.tanggal = $('#riwayat_gaji_filter_hari').val();
                    } else if(d.jenis === 'bulan'){
                        d.tanggal = $('#riwayat_gaji_filter_bulan').val();
                    } else if(d.jenis === 'tahun'){
                        d.tanggal = $('#riwayat_gaji_filter_tahun').val();
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
                {"data": "nip"},
                {"data": "periode"},
                {"data": "jam"},
                {
                    "data": "nominal_gaji",
                    "render": function(data, type, row) {
                        return "Rp. " + parseInt(data).toLocaleString('id-ID');
                    }
                },
                {
                    "data": "total_gaji",
                    "render": function(data, type, row) {
                        return "Rp. " + parseInt(data).toLocaleString('id-ID');
                    }
                },
                {
                    "data": "view",
                    "orderable": false,
                    "searchable": false
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
						var isi = $('#form').serialize();
						$.ajax({
							url: url,
							type:"POST",
							data: isi,
							dataType:"JSON",
							success:function(data){
								$('#modal-form').modal('hide');
								$('#simpan').text('Simpan');
								$('#simpan').attr('disabled',false);
								if(data.status){
								reload();
								sweet('Sukses','Pembayaran Gaji Berhasil','success');
								}else{
									sweet('Gagal','Pembayaran Gaji Sudah dilakukan','error');
								}
							}
						});
					}
				});
			},
			invalidHandler: function (form) {}
		});

    $('#riwayat_gaji_jenis_filter').change(function(){
        $('#riwayat_gaji_wrap-filter-hari').hide();
        $('#riwayat_gaji_wrap-filter-bulan').hide();
        $('#riwayat_gaji_wrap-filter-tahun').hide();
        
        $('#riwayat_gaji_filter_hari').val('');
        $('#riwayat_gaji_filter_bulan').val('');
        $('#riwayat_gaji_filter_tahun').val('');
        
        var jenis = $(this).val();
        if(jenis === 'hari') {
            $('#riwayat_gaji_wrap-filter-hari').show();
        } else if(jenis === 'bulan') {
            $('#riwayat_gaji_wrap-filter-bulan').show();
        } else if(jenis === 'tahun') {
            $('#riwayat_gaji_wrap-filter-tahun').show();
        }
    });

    $('#riwayat_gaji_btn-filter').click(function(){
        table_riwayat_gaji.ajax.reload();
    });

    $('#riwayat_gaji_btn-reset').click(function(){
        $('#riwayat_gaji_jenis_filter').val('').trigger('change');
        table_riwayat_gaji.ajax.reload();
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
    }

    function Detail(id){
         document.location.href= "<?= base_url($this->uri->segment(1).'/Detail/')?>"+id;
    }

    function Bayar(id){
         document.location.href= "<?= base_url($this->uri->segment(1).'/Bayar/')?>"+id;
    }
</script>