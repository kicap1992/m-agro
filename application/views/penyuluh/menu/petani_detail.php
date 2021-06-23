<!DOCTYPE html>
<html lang="en">


<?php $this->load->view("penyuluh/head"); ?>

<body>
<?php  $this->load->view('penyuluh/main_menu'); ?>

<?php $this->load->view("penyuluh/fixed_navbar") ; ?>

<div id="wrapper">
	<div class="main-content">
  	<div class="row small-spacing">
  		<div class="col-lg-4 col-xs-12">
        <div class="box-content card">
          <h4 class="box-title">Detail Petani</h4>
          <div class="card-content">
            <div class="form-group">
              <label for="inputEmail3" class="control-label">NIK Petani</label>
              <input class="form-control" disabled="" value="<?=$cek_data[0]->nik?>" >
            </div>
            <div id="sini_js" style="display: none"></div>
            <div class="form-group">
              <label for="inputEmail3" class="control-label">Nama Petani</label>
              <input class="form-control" disabled="" value="<?=$cek_data[0]->nama?>" >
            </div>
            <div class="form-group">
              <label for="inputEmail3" class="control-label">Jumlah Lahan</label>
              <input class="form-control" disabled="" value="<?=$count_lahan?>" >
            </div>
              
            
          </div>
        </div>
      </div>

      <div class="col-lg-8 col-xs-12">
        <div class="box-content card">
          <h4 class="box-title">Penambahan Lahan</h4>
          <div class="card-content">
            <form id="sini_form">
              <div class="form-group">
                <label for="inputEmail3" class="control-label">Nomor PBB</label>
                <input type="text" name="no_pbb" id="no_pbb" class="form-control" placeholder="Masukkan No PBB Lahan" maxlength="16">
              </div>

              <div class="form-group">
                <label for="inputEmail3" class="control-label">Kelurahan</label>
                <select class="form-control"  name="kelurahan" id="kelurahan" onchange="changeKelurahan(value)">
                  <option selected="" disabled="">-Pilih Kelurahan</option>

                  <?php foreach ($kelurahan as $key => $value): ?>
                    <option value="<?=$value->id_kelurahan?>"><?=$value->kelurahan?></option>
                  <?php endforeach ?>

                </select>
              </div>

              <div class="form-group">
                <label for="inputEmail3" class="control-label">Luas Lahan</label>
                <input  class="form-control" placeholder="Otomatis" id="luas_lahan" disabled="">
                <input type="hidden" name="point" id="point">
              </div>

              <div class="form-group" id="sini_petanya"></div>
            </form>
            <div class="form-group">
              <center><button type="submit" class="btn btn-info btn-sm waves-effect waves-light" onclick="tambah()">Tambah Lahan</button></center>
            </div>
          </div>
        </div>
      </div>
  	</div>

    <div class="row small-spacing">
      <div class="col-lg-12 col-xs-12">
        <div class="box-content card">
          <h4 class="box-title">Lahan Petani</h4>
          <div class="card-content">
            <div class="form-group" id="sini_lahannya">
              
            </div>
          </div>
        </div>
      </div>

    </div>	 
		<?php $this->load->view("penyuluh/footer"); ?>
	</div>
</div>
	
	<?php $this->load->view("penyuluh/script"); ?>
  <script src="<?=base_url()?>sweet-alert/block/jquery.blockUI.js"></script> 
	<script type="text/javascript">
    var peta_lahan = $.ajax({
      url: "<?=base_url()?>penyuluh/petani",
      type: 'post',
      data: {id : <?=$this->uri->segment(3)?>, proses : 'cari_lahan_petani'},
      async : false
    });

    // console.log(peta_lahan.responseText);
    $("#sini_lahannya").html(peta_lahan.responseText);

    function tambah() {
      var no_pbb = $("#no_pbb");
      var kelurahan = $("#kelurahan");
      var point = $("#point");
      if (no_pbb.val() == '' || no_pbb.val() == null) {
        toastnya('no_pbb','No PBB tidak boleh kosong')
      }
      else if (no_pbb.val().length < 16) {
        toastnya('no_pbb','Panjang No PBB harus 16 karakter')
      }
      else if (kelurahan.val() == '' || kelurahan.val() == null) {
        toastnya('kelurahan','Kelurahan harus dipilih')
      }
      else if (point.val() == '' || point.val() == null) {
        toastnya('point','Kordinat lahan harus ditandai')
      }
      else
      {
        var data = $('#sini_form').serializeArray();
        var ini_data_petani = [{"name" : "nik_petani" ,"value" : "<?=$this->uri->segment(3)?>"}];
        var ini_kecamatan = [{"name" : "kecamatan", "value" : "<?=$datanya->id_kecamatan?>"}];
        data = data.concat(ini_data_petani,ini_kecamatan)
        // console.log(data);
        $.ajax({
          url: "<?=base_url()?>penyuluh/petani",
          type: 'post',
          data: {data : data, proses : 'tambah_lahan'},
          // dataType: 'json',
          beforeSend: function(res) {                   
            $.blockUI({ 
              message: "Sedang Diproses", 
              css: { 
              border: 'none', 
              padding: '15px', 
              backgroundColor: '#000', 
              '-webkit-border-radius': '10px', 
              '-moz-border-radius': '10px', 
              opacity: .5, 
              color: '#fff' 
            } });
          },
          success: function (response) {
            $.unblockUI();
            console.log(response);
            $("#sini_js").html(response)
            location.reload();

          },
          error: function(XMLHttpRequest, textStatus, errorThrown) { 
            $.unblockUI();
            swal({
              // title: "Submit Keperluan ?",
              text: "Koneksi Internet Anda Mungkin Hilang Atau Terputus, Halaman Akan Terefresh Kembali",
              icon: "warning",
              buttons: {
                  cancel: false,
                  confirm: true,
                },
              // dangerMode: true,
            })
            .then((hehe) =>{
              location.reload();
            });
           
          } 
        });
      }
    }

    function hapus_lahan(e) {
      swal({
        title: "Yakin Ingin Hapus Lahan Ini?",
        text: "Lahan akan terhapus permanen dari sistem",
        icon: "error",
        buttons: true,
        dangerMode: true,
      })
      .then((logout) => {
        if (logout) {
          $.ajax({
            url: "<?=base_url()?>penyuluh/petani",
            type: "post",
            data: {id: e , proses : "hapus_lahan"},
            beforeSend: function(res) {                   
              $.blockUI({ 
                message: "Sedang Proses", 
                css: { 
                border: 'none', 
                padding: '15px', 
                backgroundColor: '#000', 
                '-webkit-border-radius': '10px', 
                '-moz-border-radius': '10px', 
                opacity: .5, 
                color: '#fff' 
              } });
            },
            success: function (response) {
              console.log(response);
              // location.reload();
              $.unblockUI();
              window.location.replace("<?=base_url()?>penyuluh/petani_detail/<?=$this->uri->segment(3)?>")
              // $("#sini_petanya").html(response);

            },
            error: function(XMLHttpRequest, textStatus, errorThrown) { 
              $.unblockUI();
              swal({
                // title: "Submit Keperluan ?",
                text: "Koneksi Internet Anda Mungkin Hilang Atau Terputus, Halaman Akan Terefresh Kembali",
                icon: "warning",
                buttons: {
                    cancel: false,
                    confirm: true,
                  },
                // dangerMode: true,
              })
              .then((hehe) =>{
                location.reload();
              });
             
            } 
          });
        } 
      });
    }

    function toastnya(id,mesej){
      toastr.options = {
        "closeButton": true,
        "debug": false,
        "progressBar": true,
        "positionClass": "toast-top-right",
        "showDuration": "300",
        "hideDuration": "1000",
        "timeOut": "5000",
        "extendedTimeOut": "1000",
        "showEasing": "swing",
        "hideEasing": "linear",
        "showMethod": "fadeIn",
        "hideMethod": "fadeOut"
      };

      toastr.error("<center>"+mesej+"</center>");
      $("#"+id).focus();
    }

		function setInputFilter(textbox, inputFilter) {
      ["input", "keydown", "keyup", "mousedown", "mouseup", "select", "contextmenu", "drop"].forEach(function(event) {
        textbox.addEventListener(event, function() {
          if (inputFilter(this.value)) {
            this.oldValue = this.value;
            this.oldSelectionStart = this.selectionStart;
            this.oldSelectionEnd = this.selectionEnd;
          } else if (this.hasOwnProperty("oldValue")) {
            this.value = this.oldValue;
            this.setSelectionRange(this.oldSelectionStart, this.oldSelectionEnd);
          } else {
            this.value = "";
          }
        });
      });
    }


    // Install input filters.
    setInputFilter(document.getElementById("no_pbb"), function(value) {
      return /^-?\d*$/.test(value); });
	</script>

  <script type="text/javascript">
    var table;
    $(document).ready(function() {
 
      //datatables
      table = $('#table1').DataTable({ 
        // "searching": false,
        "ordering": false,
        "processing": true, 
        "serverSide": true, 
        "order": [], 
         
        "ajax": {
          "url": "<?php echo base_url('penyuluh/petani/')?>",
          "type": "POST",
          data : {proses : 'tables_petani'}
        },

         
        "columnDefs": [
          { 
            "targets": [ 0 ], 
            "orderable": false, 
          },
        ],

      });

    });
   
  </script>

  <script type="text/javascript">
    function changeKelurahan(e){
      console.log(e);
      $.ajax({
        url: "<?=base_url()?>penyuluh/petani",
        type: 'post',
        data: {cek_peta : e, posisi : 'kelurahan',proses : 'ambil_peta'},
        // dataType: 'json',
        beforeSend: function(res) {                   
          $.blockUI({ 
            message: "Loading Peta", 
            css: { 
            border: 'none', 
            padding: '15px', 
            backgroundColor: '#000', 
            '-webkit-border-radius': '10px', 
            '-moz-border-radius': '10px', 
            opacity: .5, 
            color: '#fff' 
          } });
        },
        success: function (response) {
          // console.log(response);
          $.unblockUI();
          $("#sini_petanya").html(response);

        },
        error: function(XMLHttpRequest, textStatus, errorThrown) { 
          $.unblockUI();
          swal({
            // title: "Submit Keperluan ?",
            text: "Peta Gagal Ditampilkan, Koneksi Internet Anda Mungkin Hilang Atau Terputus, Halaman Akan Terefresh Kembali",
            icon: "warning",
            buttons: {
                cancel: false,
                confirm: true,
              },
            // dangerMode: true,
          })
          .then((hehe) =>{
            location.reload();
          });
         
        } 
      });
    }
  </script>
	<script src="<?=base_url()?>assets/scripts/main.min.js"></script>
</body>


</html>