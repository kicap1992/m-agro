	<script src="<?=base_url()?>assets/scripts/jquery.min.js"></script>
	<script src="<?=base_url()?>assets/scripts/modernizr.min.js"></script>
	<script src="<?=base_url()?>assets/plugin/bootstrap/js/bootstrap.min.js"></script>
	<script src="<?=base_url()?>assets/plugin/nprogress/nprogress.js"></script>
	<!-- <script src="<?=base_url()?>assets/plugin/sweet-alert/sweetalert.min.js"></script> -->
	<script src="<?php echo base_url() ?>sweet-alert/sweetalert.js"></script>
	<script src="<?=base_url()?>assets/plugin/waves/waves.min.js"></script>

	<script src="<?=base_url()?>assets/plugin/datatables/media/js/jquery.dataTables.min.js"></script>
	<script src="<?=base_url()?>assets/plugin/datatables/media/js/dataTables.bootstrap.min.js"></script>

	
	<script src="<?=base_url()?>assets/plugin/toastr/toastr.min.js"></script>
	<link rel="stylesheet" type="text/css" href="<?=base_url()?>assets/plugin/toastr/toastr.css">

	
	<?php if ($this->session->flashdata('my404')): ?>
		<script type="text/javascript">
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

		    toastr.error("<?php echo  $this->session->flashdata('my404')?>");
	  	</script> 
	<?php endif ?>

	<?php if ($this->session->flashdata('success')): ?>
		<script type="text/javascript">
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

		    
		    toastr.success("<?php echo  $this->session->flashdata('success')?>");
		    
		    
	  	</script> 
	<?php endif ?>

	<?php if ($this->session->flashdata('error')): ?>
		<script type="text/javascript">
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

		    
		    toastr.error("<?php echo  $this->session->flashdata('error')?>");
		    
		    
	  	</script> 
	<?php endif ?>

	<script type="text/javascript">
    function logout() {
      swal({
        title: "Logout?",
        text: "Anda akan logout dari sistem",
        icon: "info",
        buttons: true,
        dangerMode: true,
      })
      .then((logout) => {
        if (logout) {
          $.ajax({
            url: "<?=base_url()?>admin/logout",
            type: "post",
            data: {info : "logout"},
            // dataType: "json",
            success: function (response) {
              window.location.replace('<?=base_url()?>');
            },
            error: function(XMLHttpRequest, textStatus, errorThrown) { 
              console.log("gagal");
            }  
          });
        } 
      });
    }
  </script>	

	

	

	