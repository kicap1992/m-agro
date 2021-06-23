<!DOCTYPE html>
<html lang="en">


<?php $this->load->view("penyuluh/head"); ?>

<body>
<?php  $this->load->view('penyuluh/main_menu'); ?>

<?php $this->load->view("penyuluh/fixed_navbar") ; ?>

<div id="wrapper">
	<div class="main-content">
  	

    <div class="row small-spacing">

      <div class="col-lg-4 col-xs-12">
        <div class="box-content card">
          <h4 class="box-title">Detail Lahan</h4>
          <div class="card-content">
            <form id="sini_form">
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
                <input class="form-control" disabled="" id="luas" >
              </div>
              <div class="form-group">
                <label for="inputEmail3" class="control-label">Status Lahan</label>
                <input class="form-control" disabled="" value="sini status" >
              </div>
            </form>
            <div class="form-group">
              <center><button id="edit_button" class="btn btn-info btn-sm waves-effect waves-light" onclick="edit(0)">Edit No PBB</button> &nbsp <button type="submit" class="btn btn-danger btn-sm waves-effect waves-light" onclick="hapus(<?=$lahannya[0]->id_lahan?>)">Hapus Lahan</button></center>
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
            <div class="form-group">
              sini history status lahan
            </div>
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
    var peta_lahan = $.ajax({
      url: "<?=base_url()?>penyuluh/petani",
      type: 'post',
      data: {id : <?=$this->uri->segment(4)?>, proses : 'cari_lahan_petani_detail'},
      async : false
    });

    // console.log(peta_lahan.responseText);
    $("#sini_lahannya").html(peta_lahan.responseText);

    function edit(e) {
      const no_pbb = $("#no_pbb");
      if (e == 0) {

        no_pbb.removeAttr("disabled");
        no_pbb.focus();
        $("#edit_button").attr({
          'class' : "btn btn-success btn-sm waves-effect waves-light",
          'onclick' : "edit(1)"
        })
        $("#edit_button").html("Submit")
      }
      else if (e == 1) {
        if (no_pbb.val() == <?=$lahannya[0]->no_pbb?>) {
          toastnya('no_pbb','No PBB belum ada perubahan dari sebelumnya')
        }
        else if (no_pbb.val() == '' || no_pbb.val() == null) {
          toastnya('no_pbb','No PBB tidak boleh kosong')
        }
        else if (no_pbb.val().length < 16) {
          toastnya('no_pbb','Panjang No PBB harus 16 karakter')
        }
        else
        {
          $.ajax({
            url: "<?=base_url()?>penyuluh/petani",
            type: 'post',
            data: {id : <?=$lahannya[0]->id_lahan?>, no_pbb : no_pbb.val(), proses : 'ubah_no_pbb'},
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
              location.reload();
              $.unblockUI();
              // $("#sini_petanya").html(response);

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
      
    }

    function hapus(e) {
      swal({
        title: "Yakin Ingin Hapus Lahan Ini?",
        text: "Lahan akan terhapus permanen dari sistem",
        icon: "error",
        buttons: true,
        dangerMode: true,
      })
      .then((logout) => {
        if (logout) {
          $.ajax({
            url: "<?=base_url()?>penyuluh/petani",
            type: "post",
            data: {id: e , proses : "hapus_lahan"},
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
              console.log(response);
              // location.reload();
              $.unblockUI();
              window.location.replace("<?=base_url()?>penyuluh/petani_detail/<?=$this->uri->segment(3)?>")
              // $("#sini_petanya").html(response);

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
    setInputFilter(document.getElementById("no_pbb"), function(value) {
      return /^-?\d*$/.test(value); });
	</script>


	<script src="<?=base_url()?>assets/scripts/main.min.js"></script>
</body>


</html>