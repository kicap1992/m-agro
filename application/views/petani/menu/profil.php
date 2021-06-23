<!DOCTYPE html>
<html lang="en">


<?php $this->load->view("petani/head"); ?>

<body>
<?php  $this->load->view('petani/main_menu'); ?>

<?php $this->load->view("petani/fixed_navbar") ; ?>

<div id="wrapper">
	<div class="main-content">
    
		<div class="row small-spacing">
			<div class="col-lg-5 col-xs-12">
				<div class="box-content card">
					<h4 class="box-title">Profil Petani</i></h4>
					<div class="card-content">
            <div class="form-group">
              <label for="inputEmail3" class="control-label">NIK Petani</label>
              <input type="text" class="form-control" disabled="" value="<?=$datanya[0]->nik?>">
            </div>
            <div id="sini_js" style="display: none"></div>
            <div class="form-group">
              <label for="inputEmail3" class="control-label">Nama Petani</label>
              <input type="text" class="form-control" disabled="" value="<?=$datanya[0]->nama?>">
            </div>
            <div class="form-group">
              <label for="inputEmail3" class="control-label">Jumlah Lahan</label>
              <input type="text" class="form-control" disabled="" value="<?=$count_lahan?>">
            </div>
          </div>
				</div>
			</div>

      <div class="col-lg-7 col-xs-12">
        <div class="box-content card">
          <h4 class="box-title">Ubah Username Dan Password</h4>
          <form class="card-content" id="sini_form">
            <div class="form-group">
              <label for="inputEmail3" class="control-label">Username</label>
              <input type="text" class="form-control" id="username" name="username" minlength="8" maxlength="20" value="<?=$datanya[0]->username?>">
            </div>
            <div class="form-group">
              <label for="inputEmail3" class="control-label">Password Baru</label>
              <input type="password" class="form-control" id="password_baru" name="password" minlength="8" maxlength="20">
            </div>
            <div class="form-group">
              <label for="inputEmail3" class="control-label">Konfirmasi Password Baru</label>
              <input type="password" class="form-control" id="konfirm_password" minlength="8" maxlength="20">
            </div>
            <div class="form-group">
              <center><button type="button" class="btn btn-info btn-sm waves-effect waves-light" onclick="ubah()">Ubah Username & Password</button></center>
            </div>
          </form>
        </div>
      </div>
		</div>
    

	
		<?php $this->load->view("petani/footer"); ?>
	</div>
</div>
	
	<?php $this->load->view("petani/script"); ?>
  <script src="<?=base_url()?>sweet-alert/block/jquery.blockUI.js"></script> 
	<script type="text/javascript">
		function ubah() {
      var username = $("#username");
      var password_baru = $("#password_baru");
      var konfirm_password = $("#konfirm_password");
      if (username.val() == '' || username.val() == null) {
        toastnya('username','Username tidak boleh kosong')
      }
      else if (username.val().length < 8) {
        toastnya('username','Panjang Username minimal 8 karakter')
      }
      else if (password_baru.val() == '' || password_baru.val() == null) {
        toastnya('password_baru','Password Baru tidak boleh kosong')
      }
      else if (password_baru.val().length < 8) {
        toastnya('password_baru','Panjang Password minimal 8 karakter')
      }
      else if (konfirm_password.val() == '' || konfirm_password.val() == null) {
        toastnya('konfirm_password','Konfirmasi Password Baru tidak boleh kosong')
      }
      else if (konfirm_password.val() != password_baru.val() ) {
        toastnya('password_baru','Password dan Konfirmasi Password tidak cocok')
      }
      else
      {
        var data = $('#sini_form').serializeArray();
        console.log(data);
        $.ajax({
          url: "<?=base_url()?>petani/profil",
          type: 'post',
          data: {data : data, proses : 'ubah_detail'},
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
            // $("#sini_js").html(response)
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
	</script>
  
	<script src="<?=base_url()?>assets/scripts/main.min.js"></script>
</body>


</html>