<!DOCTYPE html>
<html lang="en">


<?php $this->load->view("admin/head"); ?>

<body>
<?php  $this->load->view('admin/main_menu'); ?>

<?php $this->load->view("admin/fixed_navbar") ; ?>

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
          <h4 class="box-title">Lahan Petani</h4>
          <div class="card-content">
            <div class="form-group" id="sini_lahannya">
              
            </div>
          </div>
        </div>
      </div>
  	</div>

    
		<?php $this->load->view("admin/footer"); ?>
	</div>
</div>
	
	<?php $this->load->view("admin/script"); ?>
  <script src="<?=base_url()?>sweet-alert/block/jquery.blockUI.js"></script> 
	<script type="text/javascript">
    var peta_lahan = $.ajax({
      url: "<?=base_url()?>admin/petani",
      type: 'post',
      data: {id : <?=$this->uri->segment(3)?>, proses : 'cari_lahan_petani' ,idnya : 'biasa'},
      async : false
    });

    console.log(peta_lahan.responseText);
    $("#sini_lahannya").html(peta_lahan.responseText);

    
	</script>

 
	<script src="<?=base_url()?>assets/scripts/main.min.js"></script>
</body>


</html>