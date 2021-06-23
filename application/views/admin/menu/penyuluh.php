<!DOCTYPE html>
<html lang="en">


<?php $this->load->view("admin/head"); ?>

<body>
<?php  $this->load->view('admin/main_menu'); ?>

<?php $this->load->view("admin/fixed_navbar") ; ?>

<div id="wrapper">
	<div class="main-content">
		<div class="row small-spacing">
      <div class="col-lg-12 col-xs-12">
        <div class="box-content card">
          <h4 class="box-title">List Penyuluh</h4>
          <div class="card-content" style="overflow-x: auto">
            <table id="table1" class="table table-striped table-bordered" width="100%">
              <thead>
                <tr>
                  <!-- <th>No</th> -->
                  <th>No</th>
                  <th>NIK</th>
                  <th>Nama</th>
                  <th>Kecamatan</th>
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
        "lengthMenu": [ [5, 10, 15, -1], [5, 10, 15, "All"] ],
        "pageLength": 15,
        "ordering": true,
        "processing": true, 
        "serverSide": true, 
        "order": [], 
         
        "ajax": {
          "url": "<?php echo base_url('admin/penyuluh/')?>",
          "type": "POST",
          data : {proses : 'tables_penyuluh'}
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