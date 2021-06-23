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
              <label for="inputEmail3" class="control-label">Jumlah Petani</label>
              <input type="text" class="form-control" disabled="" value="<?=count($petani)?>" >
            </div>
            <div id="sini_js" style="display: none"></div>
            <div class="form-group">
              <label for="inputEmail3" class="control-label">Jumlah Lahan</label>
              <input type="text" class="form-control" disabled="" value="<?=$jumlahnya['lahan']?>" >
            </div>
            <div class="form-group">
              <label for="inputEmail3" class="control-label">Jumlah Lahan Pembibitan</label>
              <input type="text" class="form-control" disabled="" value="<?=$jumlahnya['pembibitan']?>" >
            </div>  
            <div class="form-group">
              <label for="inputEmail3" class="control-label">Jumlah Lahan Penanaman</label>
              <input type="text" class="form-control" disabled="" value="<?=$jumlahnya['penanaman']?>" >
            </div>  
            <div class="form-group">
              <label for="inputEmail3" class="control-label">Jumlah Lahan Panen</label>
              <input type="text" class="form-control" disabled="" value="<?=$jumlahnya['panen']?>" >
            </div>  
            <div class="form-group">
              <label for="inputEmail3" class="control-label">Jumlah Lahan Gagal Panen</label>
              <input type="text" class="form-control" disabled="" value="<?=$jumlahnya['gagal_panen']?>" >
            </div>  
            <div class="form-group">
              <label for="inputEmail3" class="control-label">Jumlah Lahan Belum Update</label>
              <input type="text" class="form-control" disabled="" value="<?=$jumlahnya['belum_update']?>" >
            </div>  
          </div>
        </div>
      </div>

      <div class="col-lg-8 col-xs-12">
        <div class="box-content card">
          <h4 class="box-title">List Petani</h4>
          <div class="card-content" style="overflow-x: auto">
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

	
		<?php $this->load->view("admin/footer"); ?>
	</div>
</div>
	
	<?php $this->load->view("admin/script"); ?>
  <script src="<?=base_url()?>sweet-alert/block/jquery.blockUI.js"></script> 
	
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
          "url": "<?php echo base_url('admin/petani/')?>",
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