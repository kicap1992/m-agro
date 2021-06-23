<!DOCTYPE html>
<html lang="en">


<?php $this->load->view("home/head"); ?>

<body>
<?php  $this->load->view('home/main_menu'); ?>

<?php $this->load->view("home/fixed_navbar") ; ?>

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
					<h4 class="box-title">Peta Sidenreng Rappang</h4>
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
	
		<?php $this->load->view("home/footer"); ?>
	</div>
</div>
	
	<?php $this->load->view("home/script"); ?>
  <script src="<?=base_url()?>sweet-alert/block/jquery.blockUI.js"></script> 
	<script type="text/javascript">
		var response = $.ajax({
      url: "<?=base_url()?>home/",
      type: 'post',
      data: {proses : 'ambil_peta'},
      async : false,
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
    $("#sini_petanya").html(response.responseText);
	</script>
	<script src="<?=base_url()?>assets/scripts/main.min.js"></script>
	<script src="<?=base_url()?>assets/scripts/main.min.js"></script>
</body>


</html>