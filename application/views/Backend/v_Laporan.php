<div class="col-xs-12">
	<div class="box box-primary">
        <div class="box-header">
            <div class="pull-right">
                <a href="#" data-toggle="modal" data-target="#modal-print" class="btn btn-info"><i class="fa fa-print"></i> Cetak</a>
            </div>
        </div>
	    <div class="box-body">
            <div class="row" style="margin-bottom: 15px;">
                <div class="col-md-3">
                    <select id="jenis_filter" class="form-control">
                        <option value="">-- Pilih Jenis Filter --</option>
                        <option value="bulan">Bulanan</option>
                        <option value="tahun">Tahunan</option>
                    </select>
                </div>
                
                <div class="col-md-3" id="wrap-filter-bulan" style="display:none;">
                    <input type="month" id="filter_bulan" class="form-control">
                </div>
                
                <div class="col-md-3" id="wrap-filter-tahun" style="display:none;">
                    <select id="filter_tahun" class="form-control">
                        <option value="">-- Pilih Tahun --</option>
                        <?php 
                            for($i = date('Y'); $i >= date('Y')-5; $i--) {
                                echo "<option value='$i'>$i</option>";
                            }
                        ?>
                    </select>
                </div>
                <div class="col-md-3">
                    <button id="btn-filter" class="btn btn-success btn-sm"><i class="fa fa-filter"></i> Filter</button>
                    <button id="btn-reset" class="btn btn-default btn-sm"><i class="fa fa-refresh"></i> Reset</button>
                </div>
            </div>
	    	<div class="table-responsive">    	
		        <table id="list-data" class="table table-bordered table-hover">
		            <thead>
			            <tr>
                      <th style="width: 10px;">No</th>
                      <th>Tanggal</th>
                      <th>Saldo Awal</th>
                      <th>Kas Masuk</th>
                      <th>Kas Keluar</th>
                      <th>Saldo Akhir</th>
                      <th width="100">Aksi</th>
			            </tr>
		            </thead>
		            <tbody>
		              
		            </tbody>
		        </table>
	       	</div>
	    </div>
    </div>
</div>

<div class="modal fade" id="modal-print">
    <div class="modal-dialog">
<?= form_open('Laporan/Cetak','class="modal-content" id="form-print" target="_blank"')?>
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                  <span aria-hidden="true">&times;</span>
                </button>
                <h4 class="modal-title">Cetak Laporan</h4>
            </div>
            <div class="modal-body">
                <div class="form-group">
                    <label class="control-label">Jenis Cetak</label>
                    <select name="jenis_cetak" id="jenis_cetak" class="form-control">
                        <option value="rentang">Rentang Tanggal</option>
                        <option value="bulan">Bulanan</option>
                        <option value="tahun">Tahunan</option>
                    </select>
                </div>
                
                <div id="print-rentang-fields">
                    <div class="form-group">
                        <label class="control-label"> Tanggal Awal</label>
                        <div><input type="text" autocomplete="off" placeholder="tanggal awal" class="form-control datepicker" name="awal"></div>
                    </div>
                    <div class="form-group">
                        <label class="control-label"> Tanggal Akhir</label>
                        <div><input type="text" autocomplete="off"  placeholder="tanggal akhir" class="form-control datepicker" name="akhir"></div>
                    </div>
                </div>

                <div id="print-bulan-fields" style="display: none;">
                    <div class="form-group">
                        <label class="control-label">Pilih Bulan</label>
                        <div><input type="month" class="form-control" name="print_bulan"></div>
                    </div>
                </div>

                <div id="print-tahun-fields" style="display: none;">
                    <div class="form-group">
                        <label class="control-label">Pilih Tahun</label>
                        <select name="print_tahun" class="form-control">
                            <?php 
                                for($i = date('Y'); $i >= date('Y')-5; $i--) {
                                    echo "<option value='$i'>$i</option>";
                                }
                            ?>
                        </select>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-default" data-dismiss="modal">Batal</button>
                <button type="submit" id="cetak" class="btn btn-primary">Cetak</button>
            </div>
<?= form_close()?>
    </div>
</div>
<script type="text/javascript">
    // Diagnostic error handler to catch any unhandled JS exceptions on the page
    window.onerror = function(message, source, lineno, colno, error) {
        alert("JS Error: " + message + " at line " + lineno + " in " + source);
        return false;
    };

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
                "type": "POST",
                "data": function (d) {
                    d.jenis = $('#jenis_filter').val();
                    if(d.jenis === 'bulan'){
                        d.tanggal = $('#filter_bulan').val();
                    } else if(d.jenis === 'tahun'){
                        d.tanggal = $('#filter_tahun').val();
                    }
                }
            },
            columns: [
                {
                    "data": "id",
                    "orderable": false,
                    "searchable": false
                },
                {"data": "tanggal"},
                {"data": "saldo_awal",render: $.fn.dataTable.render.number('.',',','')},
                {"data": "kas_masuk",render: $.fn.dataTable.render.number('.',',','')},
                {"data": "kas_keluar",render: $.fn.dataTable.render.number('.',',','')},
                {"data": "saldo_akhir",render: $.fn.dataTable.render.number('.',',','')},
                {
                    "data": "view",
                    "orderable": false,
                    "searchable": false
                }
            ],
            order: [[0, 'DESC']],
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

        // Diagnostic error handler moved to the top of script tag

        // Change listener for jenis cetak inside modal-print
        $('#jenis_cetak').change(function(){
            var val = $(this).val();
            $('#print-rentang-fields').hide();
            $('#print-bulan-fields').hide();
            $('#print-tahun-fields').hide();
            
            if (val === 'rentang') {
                $('#print-rentang-fields').show();
            } else if (val === 'bulan') {
                $('#print-bulan-fields').show();
            } else if (val === 'tahun') {
                $('#print-tahun-fields').show();
            }
        }).trigger('change');

        // Automatically pre-populate print modal based on current table filter
        $('#modal-print').on('show.bs.modal', function () {
            var tableFilterJenis = $('#jenis_filter').val();
            if (tableFilterJenis === 'bulan') {
                $('#jenis_cetak').val('bulan').trigger('change');
                var tableBulanVal = $('#filter_bulan').val();
                if (tableBulanVal) {
                    $('[name="print_bulan"]').val(tableBulanVal);
                }
            } else if (tableFilterJenis === 'tahun') {
                $('#jenis_cetak').val('tahun').trigger('change');
                var tableTahunVal = $('#filter_tahun').val();
                if (tableTahunVal) {
                    $('[name="print_tahun"]').val(tableTahunVal);
                }
            } else {
                $('#jenis_cetak').val('rentang').trigger('change');
                $('[name="awal"]').val('');
                $('[name="akhir"]').val('');
            }
        });

        // Use custom Javascript validation before submitting to prevent blank/silent fails
        $('#cetak').click(function(e) {
            e.preventDefault(); // Stop default submit
            
            var val = $('#jenis_cetak').val();
            if (val === 'rentang') {
                var awal = $('[name="awal"]').val();
                var akhir = $('[name="akhir"]').val();
                if (!awal || !akhir) {
                    alert('Validasi Gagal: Tanggal awal dan akhir harus diisi!');
                    return false;
                }
            } else if (val === 'bulan') {
                var print_bulan = $('[name="print_bulan"]').val();
                if (!print_bulan) {
                    alert('Validasi Gagal: Bulan harus dipilih!');
                    return false;
                }
            }
            
            // Alert user that form is about to submit
            alert('Validasi sukses! Mengirim data laporan tipe: ' + val);
            
            // Programmatic native submit to bypass any library intercept
            var form = $('#form-print')[0];
            if (form) {
                form.submit();
            } else {
                alert('Error: Form #form-print tidak ditemukan!');
            }
            
            setTimeout(function() {
                $('#modal-print').modal('hide');
            }, 500);
        });

	});

    function reload(){
        table.ajax.reload(null,false);
    }

    $('#jenis_filter').change(function(){
        $('#wrap-filter-bulan').hide();
        $('#wrap-filter-tahun').hide();
        
        $('#filter_bulan').val('');
        $('#filter_tahun').val('');
        
        var jenis = $(this).val();
        if(jenis === 'bulan') {
            $('#wrap-filter-bulan').show();
        } else if(jenis === 'tahun') {
            $('#wrap-filter-tahun').show();
        }
    });

    $('#btn-filter').click(function(){
        table.ajax.reload();
    });

    $('#btn-reset').click(function(){
        $('#jenis_filter').val('').trigger('change');
        table.ajax.reload();
    });

    function Detail(id){

        var i = id.toString();
            document.location.href= "<?= base_url($this->uri->segment(1).'/Detail/')?>"+i;
    }

</script>