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
          <h4 class="box-title">Detail Lahan</h4>
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
              <label for="inputEmail3" class="control-label">No PBB Lahan</label>
              <input class="form-control" disabled="" maxlength="16" value="<?=$lahannya[0]->no_pbb?>" id="no_pbb">
            </div>
            <div class="form-group">
              <label for="inputEmail3" class="control-label">Luas Lahan</label>
              <input class="form-control" disabled="" id="luas_lahannya" >
            </div>
            <div class="form-group">
              <label for="inputEmail3" class="control-label">Status Lahan</label>
              <textarea class="form-control" disabled="" id="status"></textarea>
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

    <div class="row small-spacing">

      <div class="col-lg-12 col-xs-12">
        <div class="box-content card">
          <h4 class="box-title">History Status Lahan</h4>
          <div class="card-content">
            <?php if (count($status_lahannya) > 0): ?>
              <table id="table1" class="table table-striped table-bordered" width="100%">
                <thead>
                  <tr>
                    <th>No</th>
                    <th>Tanggal Update</th>
                    <th>Status</th>
                    <th>Produksi</th>
                  </tr>
                </thead>
                <tbody>
                  <?php $i = 1; foreach (json_decode($status_lahannya[0]->detail) as $key => $value): ?>
                    <tr>
                      <td><?=$i;$i++?></td>
                      <td><?=$value->tanggal?></td>
                      <td><?=$value->status?></td>
                      <?php if ($value->status == 'Panen'): ?>
                        <td><?=$value->produksi?> kg</td>
                      <?php else: ?>
                        <td> - </td>
                      <?php endif ?>
                    </tr>
                  <?php endforeach ?>
                </tbody>
              </table>
            <?php else: ?>
              <center><h4><b><i>"Status Lahan Belum Pernah Diupdate Oleh Petani Sebelumnya"</i></b></h4></center>
            <?php endif ?>
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
      data: {id : <?=$this->uri->segment(4)?>, proses : 'cari_lahan_petani', idnya : 'detail', nik : <?=$this->uri->segment(3)?>},
      async : false
    });

    console.log(peta_lahan.responseText);
    $("#sini_lahannya").html(peta_lahan.responseText);

    $('#table1').DataTable();
    
	</script>


	<script src="<?=base_url()?>assets/scripts/main.min.js"></script>
</body>


</html>