<!DOCTYPE html>
<html lang="en">


<?php $this->load->view("penyuluh/head"); ?>

<body>
<?php  $this->load->view('penyuluh/main_menu'); ?>

<?php $this->load->view("penyuluh/fixed_navbar") ; ?>

<div id="wrapper">
	<div class="main-content">
		<div class="row small-spacing">
			<div class="col-xs-12">
				<div class="box-content card">
					<h4 class="box-title">Peta <?=$datanya->nama_kecamatan?></h4>
					<div class="card-content" id="sini_petanya">
						hehehe	
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
		$.ajax({
      url: "<?=base_url()?>penyuluh/",
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
	<script src="<?=base_url()?>assets/scripts/main.min.js"></script>
</body>


</html>