<!DOCTYPE html>
<html lang="en">


<?php $this->load->view("penyuluh/head"); ?>

<body>
<?php  $this->load->view('penyuluh/main_menu'); ?>

<?php $this->load->view("penyuluh/fixed_navbar") ; ?>

<div id="wrapper">
	<div class="main-content">
		<div class="row small-spacing">
			<div class="col-lg-6 col-xs-12">
        <div class="box-content card">
          <h4 class="box-title">Form Penambahan Petani</h4>
          <div class="card-content">
            <form id="sini_form">
              <div class="form-group">
                <label for="inputEmail3" class="control-label">NIK Petani</label>
                <input type="text" name="nik" id="nik" class="form-control" placeholder="Masukkan NIK Petani" maxlength="16">
              </div>
              <div id="sini_js" style="display: none"></div>
              <div class="form-group">
                <label for="inputEmail3" class="control-label">Nama Petani</label>
                <input type="text" name="nama" id="nama" class="form-control" placeholder="Masukkan Nama Petani">
              </div>
              
            </form>
            <div class="form-group">
              <center><button type="submit" class="btn btn-info btn-sm waves-effect waves-light" onclick="tambah()">Tambah Petani</button></center>
            </div>
          </div>
        </div>
      </div>

      <div class="col-lg-6 col-xs-12">
        <div class="box-content card">
          <h4 class="box-title">List Petani</h4>
          <div class="card-content">
            <table id="table1" class="table table-striped table-bordered" width="100%">
              <thead>
                <tr>
                  <!-- <th>No</th> -->
                  <th>No</th>
                  <th>NIK</th>
                  <th>Nama</th>
                  <th>Aksi</th>
                </tr>
              </thead>
              <tbody>
              </tbody>
            </table>
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
    function tambah() {
      var nik = $("#nik");
      var nama = $("#nama");
      if (nik.val() == '' || nik.val() == null) {
        toastnya('nik','NIK tidak boleh kosong')
      }
      else if (nik.val().length < 16) {
        toastnya('nik','Panjang NIK harus 16 karakter')
      }
      else if (nama.val() == '' || nama.val() == null) {
        toastnya('nama','Nama tidak boleh kosong')
      }
      else
      {
        var data = $('#sini_form').serializeArray();
        $.ajax({
          url: "<?=base_url()?>penyuluh/petani",
          type: 'post',
          data: {data : data, proses : 'tambah_petani'},
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
    setInputFilter(document.getElementById("nik"), function(value) {
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
	<script src="<?=base_url()?>assets/scripts/main.min.js"></script>
</body>


</html>