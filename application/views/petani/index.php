<!DOCTYPE html>
<html lang="en">


<?php $this->load->view("petani/head"); ?>

<body>
<?php  $this->load->view('petani/main_menu'); ?>

<?php $this->load->view("petani/fixed_navbar") ; ?>

<div id="wrapper">
	<div class="main-content">
    <div class="row small-spacing">
      <div class="col-lg-4 col-md-4 col-xs-12">
        <div class="box-content bg-success text-white" style="background :grey">
          <p><?=$jumlahnya['lahan']?> <br> Jumlah Lahan</p>
        </div>
        <!-- /.box-content -->
      </div>
      <!-- /.col-lg-3 col-md-6 col-xs-12 -->
      <div class="col-lg-4 col-md-4 col-xs-12">
        <div class="box-content " style="background-color: #F1EEED">
          <p><?=$jumlahnya['pembibitan']?> <br> Pembibitan</p>
        </div>
        <!-- /.box-content -->
      </div>
      <!-- /.col-lg-3 col-md-6 col-xs-12 -->
      <div class="col-lg-4 col-md-4 col-xs-12">
        <div class="box-content" style="background-color: yellow">
          <p><?=$jumlahnya['penanaman']?> <br> Penanaman</p>
        </div>
        <!-- /.box-content -->
      </div>
      <!-- /.col-lg-3 col-md-6 col-xs-12 -->
      
    </div>
		<div class="row small-spacing">
			<div class="col-xs-12">
				<div class="box-content card">
					<h4 class="box-title">Lokasi Lahan Petani <i><?=$datanya[0]->nama?></i></h4>
					<div class="card-content" id="sini_petanya">
						
					</div>
				</div>
			</div>
		</div>
    <div class="row small-spacing">
      <div class="col-lg-4 col-md-4 col-xs-12">
        <div class="box-content bg-primary text-white">
         <p><?=$jumlahnya['panen']?> <br> Panen</p>
        </div>
        <!-- /.box-content -->
      </div>
      <!-- /.col-lg-3 col-md-6 col-xs-12 -->
      <div class="col-lg-4 col-md-4 col-xs-12">
        <div class="box-content bg-danger text-white" >
          <p><?=$jumlahnya['gagal_panen']?> <br> Gagal Panen</p>
        </div>
        <!-- /.box-content -->
      </div>
      <!-- /.col-lg-3 col-md-6 col-xs-12 -->
      <div class="col-lg-4 col-md-4 col-xs-12">
        <div class="box-content text-white" style="background-color: #1B1A1A">
          <p><?=$jumlahnya['belum_update']?> <br> Belum Update</p>
        </div>
        <!-- /.box-content -->
      </div>
      <!-- /.col-lg-3 col-md-6 col-xs-12 -->
    </div>

	
		<?php $this->load->view("penyuluh/footer"); ?>
	</div>
</div>
	
	<?php $this->load->view("petani/script"); ?>
  <script src="<?=base_url()?>sweet-alert/block/jquery.blockUI.js"></script> 
	<script type="text/javascript">
		$.ajax({
      url: "<?=base_url()?>petani/",
      type: 'post',
      data: {proses : 'ambil_peta'},
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
        // $(".sini_petanya").html("Peta Error");
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
	</script>
  <script type="text/javascript">
    function changeStatus(status,id) {
      if (status == 'Panen') {
        $("#produksi_div"+id).css({opacity: 0, display: "block"}).animate({opacity: 1}, 'slow');
      }
      else
      {
        $("#produksi_div"+id).css({opacity: 0, display: "none"}).animate({opacity: 1}, 'slow')
      }
    }

    function update_status(id) {
      const status = $("#status").val();
      const produksi = $("#produksi").val();

      if (status == '' || status == null) {
        toastnya("status","Status lahan harus terpilih")
      }
      else if (status == 'Panen' && (produksi == '' || produksi == null)) {
        toastnya("produksi","Produksi lahan harus terisi")
      }
      else
      {
        swal({
          title: "Update Status Lahan?",
          text: "Status lahan akan diupdate ke '"+status+"'",
          icon: "info",
          buttons: true,
          dangerMode: true,
        })
        .then((logout) => {
          if (logout) {
            go_status(id,status,produksi)
          } 
        });
      }
        


    }

    function go_status(id,status,produksi){
      $.ajax({
        url: "<?=base_url()?>petani/",
        type: 'post',
        data: {id: id, status : status, produksi : produksi,proses : 'update_status'},
        // dataType: 'json',
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
          // console.log(response);
          $.unblockUI();
          location.reload()
          // $("#sini_petanya").html(response);

        },
        error: function(XMLHttpRequest, textStatus, errorThrown) { 
          // $(".sini_petanya").html("Peta Error");
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