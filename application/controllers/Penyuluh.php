<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Penyuluh extends CI_Controller {

	public function __construct()
	{
		parent::__construct();
		// $this->load->helper('form');
		// $this->load->library('form_validation');

		$this->load->model('mpenyuluh');
		$this->load->model('m_tabel_ss');


		$penyuluh = $this->session->userdata('penyuluh');
		$cek_data_dulu = $this->mpenyuluh->tampil_data_where('tb_penyuluh',array('nik'=>$penyuluh['nik'], 'nama' => $penyuluh['nama']));

		if ($penyuluh != '' and $penyuluh != null) {
			if (count($cek_data_dulu->result()) > 0) {
				foreach ($cek_data_dulu->result() as $key => $value) ;
				$kecamatan = $value->kecamatan;
				// redirect('/petambak');
				// echo "<script>console.log('heheheh')</script>";
			}else{
				$this->session->set_flashdata('error', '<b>Error</b><br>Halaman Yang Diakses Tiada Dalam Sistem');
				redirect('/home');
			}
		}else{
			$this->session->set_flashdata('error', '<b>Error</b><br>Halaman Yang Diakses Tiada Dalam Sistem');
			redirect('/home');
		}
	}
	
	// var $nik_penyuluh = $this->session->userdata('penyuluh')['nik'];


	function index()
	{	
		$main['datanya'] = $this->mpenyuluh->custom_query("SELECT *,b.kecamatan AS nama_kecamatan FROM `tb_penyuluh` a join tb_kecamatan b ON a.kecamatan = b.id_kecamatan where a.nik = ".$this->session->userdata('penyuluh')['nik']." and a.nama = '".$this->session->userdata('penyuluh')['nama']."'")->result()[0];
		$main['header'] = "Halaman Utama";

		if ($this->input->post('proses') == 'ambil_peta') {
			$kecamatan = $this->mpenyuluh->tampil_data_where('tb_kecamatan',array('id_kecamatan' => $main['datanya']->id_kecamatan))->result()[0];
			$cari_data_lahan = $this->mpenyuluh->custom_query('SELECT * FROM `tb_lahan` a join tb_petani b on a.nik_petani = b.nik where kecamatan ='.$main['datanya']->id_kecamatan)->result();
			foreach ($cari_data_lahan as $key => $value) {
				$status_lahan[$value->id_lahan] = "Belum Pernah Diupdate Oleh Petani";
				$color[$value->id_lahan] = "black";
				$cek_data = $this->mpenyuluh->tampil_data_where('tb_lahan_detail',array('id_lahan' => $value->id_lahan))->result();
				if (count($cek_data) > 0) {
					// $status_lahan[$value->id_lahan] = "Ada Statusnya";
					$keterangan = $cek_data[0]->detail;
					$keterangan = json_decode($keterangan);
					$status_lahan[$value->id_lahan] = "Updated : ".$keterangan[0]->tanggal;
					if ($keterangan[0]->status == 'Panen') {
						$status_lahan[$value->id_lahan] .= '\rStatus : '.$keterangan[0]->status;
						$status_lahan[$value->id_lahan] .= '\rProduksi : '.$keterangan[0]->produksi."kg";
						$color[$value->id_lahan] = "blue";
					}
					else
					{
						$status_lahan[$value->id_lahan] .= '\rStatus : '.$keterangan[0]->status;
						if ($keterangan[0]->status == 'Pembibitan') {
							$color[$value->id_lahan] = "#F1EEED";
						}
						elseif ($keterangan[0]->status == 'Penanaman') {
							$color[$value->id_lahan] = "yellow";
						}
						elseif ($keterangan[0]->status == 'Gagal Panen') {
							$color[$value->id_lahan] = "red";
						}
					}
				}
			}
			?>
			<script type="text/javascript" src="https://maps.googleapis.com/maps/api/js?key=AIzaSyBw6bnAk0C2jIDDbz_dVRso9gUEnHLTH68&libraries=drawing,places,geometry&callback=initialize"></script>

	    <script type="text/javascript" >
	      
	      var geocoder;
	      

	      function numberWithCommas(x) {
	        return x.toString().replace(/\B(?=(\d{3})+(?!\d))/g, ",");
	      }

	      function initialize() {
	        var geolib = google.maps.geometry.spherical;
	        var infowindow = new google.maps.InfoWindow({
		        size: new google.maps.Size(150, 50)
		      });

	        var myOptions = {
	          zoom: 13,
	          center: new google.maps.LatLng(-4.015210, 119.658241),
	          mapTypeControl: false,
	          // mapTypeControlOptions: {
	          // style: google.maps.MapTypeControlStyle.DROPDOWN_MENU
	          // },
	          streetViewControl: true,
	          navigationControl: true,
	          mapTypeId: 'hybrid'
	        }
	        map = new google.maps.Map(document.getElementById("map_canvas"),myOptions);

	        google.maps.event.addListener(map, 'click', function() {
	          infowindow.close();
	        });

	        bounds = new google.maps.LatLngBounds();

        	var polygon_<?=$kecamatan->id_kecamatan?> = new google.maps.Polygon({
      			map: map,
      			path: [<?=$kecamatan->kordinat?>],
      			strokeColor: "#000000",
						strokeOpacity: 2,
						strokeWeight: 1,
						// fillColor: "#0D0822",
						// fillOpacity: 0.4,
      		});
	        
	        <?php foreach ($cari_data_lahan as $key => $value): ?>
      			var lahan_<?=$value->id_lahan?> = new google.maps.Polygon({
	      			map: map,
	      			path: [<?=$value->point?>],
	      			strokeColor: "#000000",
							strokeOpacity: 2,
							strokeWeight: 1,
							fillColor: "<?=$color[$value->id_lahan]?>",
							fillOpacity: 0.3,
	      		});
      		<?php endforeach ?>    

	        google.maps.event.addListener(polygon_<?=$kecamatan->id_kecamatan?>, 'click', function(event) {
						var vertices = this.getPath();
						var luas = google.maps.geometry.spherical.computeArea(polygon_<?=$kecamatan->id_kecamatan?>.getPath()) / 10000;
						luas = numberWithCommas(luas.toFixed(2));
						var contentString ="<div class='form-group' >"+
						                    "<h5>Kecamatan : <?=$kecamatan->kecamatan?></h5>"+
						                    "<h5>Luas : "+luas + " Ha"+"</h5>"+
						                    "</div>";

						infowindow.setContent(contentString);
						infowindow.setPosition(event.latLng);
						infowindow.open(map);
					});
		                                         
	        <?php foreach ($cari_data_lahan as $key => $value): ?>
	      		google.maps.event.addListener(lahan_<?=$value->id_lahan?>, 'click', function(event) {
							var vertices = this.getPath();
							var luas = google.maps.geometry.spherical.computeArea(lahan_<?=$value->id_lahan?>.getPath()) / 10000;
							luas = numberWithCommas(luas.toFixed(2));
							var contentString ='<div class="row small-spacing" >'+
							                    '<div class="card-content">'+
							                    '<div class="form-group">'+
							                    '<label for="inputEmail3" class="control-label">Pemilik</label>'+
							                    '<input class="form-control" disabled="" value="<?=$value->nama?>" >'+
							                    "</div>"+
							                    '<div class="form-group">'+
							                    '<label for="inputEmail3" class="control-label">No PBB</label>'+
							                    '<input class="form-control" disabled="" value="<?=$value->no_pbb?>" >'+
							                    "</div>"+
							                    '<div class="form-group">'+
							                    '<label for="inputEmail3" class="control-label">Luas Lahan</label>'+
							                    '<input class="form-control" disabled="" value="'+luas+' Ha" >'+
							                    "</div>"+
							                    '<div class="form-group">'+
							                    '<label for="inputEmail3" class="control-label">Status Lahan Sekarang</label>'+
							                    '<textarea style="resize : none" class="form-control" disabled=""><?=$status_lahan[$value->id_lahan]?></textarea>'+
							                    "</div>"+
							                    '<div class="form-group">'+
								                    '<center><a href="<?=base_url()?>penyuluh/petani_detail/<?=$value->nik_petani?>/<?=$value->id_lahan?>" class="btn btn-info btn-sm waves-effect waves-light">Detail</a></center>'+
								                    "</div>"+
							                    "</div>"+
							                    "</div>";
							infowindow.setContent(contentString);
							infowindow.setPosition(event.latLng);
							infowindow.open(map);
						});
	      	<?php endforeach ?>

	        <?php if (count($cari_data_lahan) > 0): ?>
	      		<?php foreach ($cari_data_lahan as $key => $value): ?>
		        	for (var i = 0; i < lahan_<?=$value->id_lahan?>.getPath().getLength(); i++) {
		            bounds.extend(lahan_<?=$value->id_lahan?>.getPath().getAt(i));
		          }
		        <?php endforeach ?>  
		      <?php else: ?>
		        for (var i = 0; i < polygon_<?=$kecamatan->id_kecamatan?>.getPath().getLength(); i++) {
	            bounds.extend(polygon_<?=$kecamatan->id_kecamatan?>.getPath().getAt(i));
	          }	                              	
         	<?php endif ?>                                 
		        
		        


	       	
	       
	                
	        map.fitBounds(bounds);

	      }
	    </script>
	    
	    <div id="map_canvas" style="height: 600px;width: 100%"></div>
			<?php
		}
		else
		{
			// print_r($this->session->userdata('penyuluh'));
			
			$this->load->view('penyuluh/index',$main);		
			// $this->load->view('penyuluh/index');		
		}
			
	}


	function petani()
	{
		// print_r("sini petani");
		$main['datanya'] = $this->mpenyuluh->custom_query("SELECT *,b.kecamatan AS nama_kecamatan FROM `tb_penyuluh` a join tb_kecamatan b ON a.kecamatan = b.id_kecamatan where a.nik = ".$this->session->userdata('penyuluh')['nik']." and a.nama = '".$this->session->userdata('penyuluh')['nama']."'")->result()[0];
		$main['header'] = "Halaman Petani";

		if ($this->input->post('proses') == 'hapus_lahan') {
			// print_r('sini hapus lahan');
			$id =$this->input->post('id');
			print_r($id);
			$this->mpenyuluh->delete('tb_lahan',array('id_lahan' => $id));
			$this->session->set_flashdata('success', '<center>Lahan Berhasil Dihapus Dari Sistem</center>');
		}

		elseif ($this->input->post('proses') == 'ubah_no_pbb') {
			$id = $this->input->post('id');
			$no_pbb = $this->input->post('no_pbb');
			$this->mpenyuluh->update('tb_lahan',array('id_lahan' => $id), array('no_pbb' => $no_pbb));
			$this->session->set_flashdata('success', '<center>No PBB lahan telah berhasil diubah</center>');
		}
		elseif ($this->input->post('proses') == 'edit_kordinat_lahan') {
			$kordinat = $this->input->post('kordinat');
			$id = $this->input->post('id');
			// print_r($id);
			$this->mpenyuluh->update('tb_lahan',array('id_lahan' => $id),array('point' => $kordinat));
			?>

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

	      toastr.success("<center>Kordinat Lahan Berhasil Terupdate</center>");
			</script>
			<?php
		}
		elseif ($this->input->post('proses') == 'cari_lahan_petani_detail') {
			$id = $this->input->post('id');
			$cari_data = $this->mpenyuluh->tampil_data_where('tb_lahan',array('id_lahan' => $id))->result();

			$kecamatan = $this->mpenyuluh->tampil_data_where('tb_kecamatan',array('id_kecamatan' => $main['datanya']->id_kecamatan))->result();
			?>
			<script type="text/javascript" src="https://maps.googleapis.com/maps/api/js?key=AIzaSyBw6bnAk0C2jIDDbz_dVRso9gUEnHLTH68&libraries=drawing,places,geometry&callback=initialize"></script>

	    <script type="text/javascript" >
	      $('#luas_lahan').val(null)
	      var geocoder;
	      var all_overlays = [];
	      function numberWithCommas(x) {
		      return x.toString().replace(/\B(?=(\d{3})+(?!\d))/g, ",");
		    }
	      
	      function initialize() {
	        var geolib = google.maps.geometry.spherical;
	        var infowindow = new google.maps.InfoWindow({
		        size: new google.maps.Size(150, 50)
		      });

	        var myOptions = {
	          zoom: 13,
	          center: new google.maps.LatLng(-4.015210, 119.658241),
	          mapTypeControl: false,
	          // mapTypeControlOptions: {
	          // style: google.maps.MapTypeControlStyle.DROPDOWN_MENU
	          // },
	          streetViewControl: true,
	          navigationControl: true,
	          mapTypeId: 'hybrid'
	        }
	        map = new google.maps.Map(document.getElementById("map_canvas"),myOptions);

	        google.maps.event.addListener(map, 'click', function() {
	          infowindow.close();
	        });

	        bounds = new google.maps.LatLngBounds();

        	var polygon_<?=$kecamatan[0]->id_kecamatan?> = new google.maps.Polygon({
      			map: map,
      			path: [<?=$kecamatan[0]->kordinat?>],
      			strokeColor: "#000000",
						strokeOpacity: 2,
						strokeWeight: 1,
						fillColor: "#0D0822",
						fillOpacity: 0.4,
      		});

    			var lahan_<?=$cari_data[0]->id_lahan?> = new google.maps.Polygon({
      			map: map,
      			path: [<?=$cari_data[0]->point?>],
      			strokeColor: "#000000",
						strokeOpacity: 2,
						strokeWeight: 1,
						fillColor: "#D2DFDF",
						fillOpacity: 0.3,
      		});

  		   	var area = google.maps.geometry.spherical.computeArea(lahan_<?=$cari_data[0]->id_lahan?>.getPath());
			    area = area/10000;
			    area = numberWithCommas(area.toFixed(2))+" Ha";
			    $("#luas").val(area);
          
          lahan_<?=$cari_data[0]->id_lahan?>.addListener('click', function(event) {
				    lahan_<?=$cari_data[0]->id_lahan?>.setEditable(!lahan_<?=$cari_data[0]->id_lahan?>.getEditable());
				    
				  });


          //var polyOptions = {
				  //   strokeWeight: 0,
				  //   fillOpacity: 0.45,
				  //   editable: true,
				  //   fillColor: '#FF1493'
				  // };

				  // var selectedShape;

				  // var drawingManager = new google.maps.drawing.DrawingManager({
				  //   drawingMode: google.maps.drawing.OverlayType.POLYGON,
				  //   drawingControl: false,
				  //   markerOptions: {
				  //     draggable: true
				  //   },
				  //   polygonOptions: polyOptions
				  // });

				  // $('#enablePolygon').click(function() {
				  //   drawingManager.setMap(map);
				  //   drawingManager.setDrawingMode(google.maps.drawing.OverlayType.POLYGON);
				  //   	$('#enablePolygon').hide();
				  // });

				  // $('#resetPolygon').click(function() {
				  //   if (selectedShape) {
				  //     selectedShape.setMap(null);
				  //   }
				  //   drawingManager.setMap(null);
				  //   $('#showonPolygon').hide();
				  //   $('#resetPolygon').hide();
				  //   $('#enablePolygon').show();
				  //   $('#luas_lahan').val(null)
				  //   $('#point').val(null)
				  // });

				  // google.maps.event.addListener(drawingManager, 'polygoncomplete', function(polygon) {
			   //   	var area = google.maps.geometry.spherical.computeArea(selectedShape.getPath());
			   //   	var area1 = google.maps.geometry.spherical.computeArea(selectedShape.getPath());
			   //   	// $('#areaPolygon').html(area.toFixed(2)+' Sq meters');
				  //   area = area/10000;
				  //   area1 = area1/10000;
				  //   area = numberWithCommas(area.toFixed(2))+" Ha";
				  //   area1 = area1.toFixed(3);
				  //   document.getElementById("luas_lahan").value = area;
				  //   // document.getElementById("luas_lahan1").value = area1;
				  //   var coordStr = "";
				  //   for (var i = 0; i < polygon.getPath().getLength(); i++) {
				  //     coordStr +="{lat: "+ polygon.getPath().getAt(i).lat() + ",  lng: "+
				  //     polygon.getPath().getAt(i).lng()+"},\n"
				  //     ;
				  //   }
				  //   document.getElementById("point").value = coordStr;
				  //   //console.log(coordStr);


				  //   $('#resetPolygon').show();

				  // });

				  // function clearSelection() {
				  //   if (selectedShape) {
				  //     selectedShape.setEditable(false);
				  //     selectedShape = null;
				  //   }
				  // }

				  // function setSelection(shape) {
				  //   clearSelection();
				  //   selectedShape = shape;
				  //   shape.setEditable(false);
				  // }

				  // google.maps.event.addListener(drawingManager, 'overlaycomplete', function(e) {
				  //   all_overlays.push(e);
				  //   if (e.type != google.maps.drawing.OverlayType.MARKER) {
				  //     // Switch back to non-drawing mode after drawing a shape.
				  //     drawingManager.setDrawingMode(null);

				  //     // Add an event listener that selects the newly-drawn shape when the user
				  //     // mouses down on it.
				  //     var newShape = e.overlay;
				  //     newShape.type = e.type;
				  //     google.maps.event.addListener(newShape, 'click', function() {
				  //       setSelection(newShape);
				  //     });

				      
				  //     setSelection(newShape);
				  //   }
				  // });

				  google.maps.event.addListener(lahan_<?=$cari_data[0]->id_lahan?>,"mouseover",function(event){
						// this.setOptions({fillColor: "#00FF00"});
						const contentString = 'Klik untuk mengubah saiz lahan'
						infowindow.setContent(contentString);
						infowindow.setPosition(event.latLng);
						infowindow.open(map);
						// console.log('heheh')
					}); 

					google.maps.event.addListener(lahan_<?=$cari_data[0]->id_lahan?>,"mouseout",function(event){
						// this.setOptions({fillColor: "#00FF00"});
						infowindow.close()
					});

				  google.maps.event.addListener(lahan_<?=$cari_data[0]->id_lahan?>.getPath(), "insert_at", getPolygonCoords);
	        google.maps.event.addListener(lahan_<?=$cari_data[0]->id_lahan?>.getPath(), "remove_at", getPolygonCoords);
	        google.maps.event.addListener(lahan_<?=$cari_data[0]->id_lahan?>.getPath(), "set_at", getPolygonCoords);

				  function getPolygonCoords() {
           var coordinates_poly = lahan_<?=$cari_data[0]->id_lahan?>.getPath().getArray();
           //var newCoordinates_poly = [];
           var coordStr = "";
           for (var i = 0; i < coordinates_poly.length; i++){
             lat_poly = coordinates_poly[i].lat();
             lng_poly = coordinates_poly[i].lng();
             // console.log(lat_poly)
             
             // latlng_poly = [lat_poly, lng_poly];
             // newCoordinates_poly.push(latlng_poly);
             
             coordStr +="{lat: "+ lat_poly + ",  lng: "+
				     lng_poly+"},\n"
				     ;
           }
          
           // var str_coordinates_poly = JSON.stringify(newCoordinates_poly);
           // console.log(coordStr)
		        $.ajax({
		          url: "<?=base_url()?>penyuluh/petani",
		          type: 'post',
		          data: {kordinat : coordStr,id : <?=$cari_data[0]->id_lahan?>, proses : 'edit_kordinat_lahan'},
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
		            $("#sini_js").html(response)
		            // location.reload();

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
				 
                                         
         	
        	// for (var i = 0; i < polygon_<?=$kecamatan[0]->id_kecamatan?>.getPath().getLength(); i++) {
         //    bounds.extend(polygon_<?=$kecamatan[0]->id_kecamatan?>.getPath().getAt(i));
         //  }
       		

      		// google.maps.event.addListener(lahan_<?=$cari_data[0]->id_lahan?>, 'click', function(event) {
					// 	var vertices = this.getPath();
					// 	var luas = google.maps.geometry.spherical.computeArea(lahan_<?=$cari_data[0]->id_lahan?>.getPath()) / 10000;
					// 	luas = numberWithCommas(luas.toFixed(2));
					// 	var contentString ='<div class="row small-spacing" >'+
					// 	                    '<div class="card-content">'+
					// 	                    '<div class="form-group">'+
					// 	                    '<label for="inputEmail3" class="control-label">No PBB</label>'+
					// 	                    '<input class="form-control" disabled="" value="<?=$cari_data[0]->no_pbb?>" >'+
					// 	                    "</div>"+
					// 	                    '<div class="form-group">'+
					// 	                    '<label for="inputEmail3" class="control-label">Luas Lahan</label>'+
					// 	                    '<input class="form-control" disabled="" value="'+luas+' Ha" >'+
					// 	                    "</div>"+
					// 	                    '<div class="form-group">'+
					// 	                    '<label for="inputEmail3" class="control-label">Status Lahan</label>'+
					// 	                    '<input class="form-control" disabled="" value="" >'+
					// 	                    "</div>"+
						                    
					// 	                    "</div>"+
					// 	                    "</div>";
					// 	infowindow.setContent(contentString);
					// 	infowindow.setPosition(event.latLng);
					// 	infowindow.open(map);
					// });
		      	

      		for (var i = 0; i < lahan_<?=$cari_data[0]->id_lahan?>.getPath().getLength(); i++) {
            bounds.extend(lahan_<?=$cari_data[0]->id_lahan?>.getPath().getAt(i));
          }
	                
	        map.fitBounds(bounds);

	      }

	    </script>

	    <div id="map_canvas" style="height: 482px;width: 100%"></div>
			<?php
			
		}
		elseif ($this->input->post('proses') == 'cari_lahan_petani') {
			$id = $this->input->post('id');
			$cari_data = $this->mpenyuluh->tampil_data_where('tb_lahan',array('nik_petani' => $id, 'kecamatan' => $main['datanya']->id_kecamatan))->result();
			if (count($cari_data) == 0) {
				?>
				<center><h4>Belum Ada Lahan Yang Diinput Untuk Petani Ini</h4></center>
				<?php
			}
			else
			{
				$kecamatan = $this->mpenyuluh->tampil_data_where('tb_kecamatan',array('id_kecamatan' => $main['datanya']->id_kecamatan))->result();
				foreach ($cari_data as $key => $value) {
   				$status[$value->id_lahan] = 'Belum Update Status';
   				$cek_status_lahan = $this->mpenyuluh->tampil_data_where('tb_lahan_detail',array('id_lahan' => $value->id_lahan))->result();
   				if (count($cek_status_lahan) > 0) {
   					$status['$value->id_lahan'] = $cek_status_lahan[0]->detail;
   				}
   			}
				?>
				<script type="text/javascript" src="https://maps.googleapis.com/maps/api/js?key=AIzaSyBw6bnAk0C2jIDDbz_dVRso9gUEnHLTH68&libraries=drawing,places,geometry&callback=initialize"></script>

		    <script type="text/javascript" >
		      $('#luas_lahan').val(null)
		      var geocoder;
		      var all_overlays = [];
		      function numberWithCommas(x) {
			      return x.toString().replace(/\B(?=(\d{3})+(?!\d))/g, ",");
			    }
		      
		      function initialize() {
		        var geolib = google.maps.geometry.spherical;
		        var infowindow = new google.maps.InfoWindow({
			        size: new google.maps.Size(150, 50)
			      });

		        var myOptions = {
		          zoom: 13,
		          center: new google.maps.LatLng(-4.015210, 119.658241),
		          mapTypeControl: false,
		          // mapTypeControlOptions: {
		          // style: google.maps.MapTypeControlStyle.DROPDOWN_MENU
		          // },
		          streetViewControl: true,
		          navigationControl: true,
		          mapTypeId: 'hybrid'
		        }
		        map = new google.maps.Map(document.getElementById("map_canvas"),myOptions);

		        google.maps.event.addListener(map, 'click', function() {
		          infowindow.close();
		        });

		        bounds = new google.maps.LatLngBounds();

	        	var polygon_<?=$kecamatan[0]->id_kecamatan?> = new google.maps.Polygon({
	      			map: map,
	      			path: [<?=$kecamatan[0]->kordinat?>],
	      			strokeColor: "#000000",
							strokeOpacity: 2,
							strokeWeight: 1,
							fillColor: "#0D0822",
							fillOpacity: 0.4,
	      		});

	      		<?php foreach ($cari_data as $key => $value): ?>
	      			var lahan_<?=$value->id_lahan?> = new google.maps.Polygon({
		      			map: map,
		      			path: [<?=$value->point?>],
		      			strokeColor: "#000000",
								strokeOpacity: 2,
								strokeWeight: 1,
								fillColor: "#D2DFDF",
								fillOpacity: 0.3,
		      		});
	      		<?php endforeach ?>

	      		// var polygon_2 = new google.maps.Polygon({
	      		//	map: map,
	      		//	path: [{lat: -3.8346030873808146,  lng: 119.81190872046881},
						//		{lat: -3.836487133095519,  lng: 119.81517028663092},
						//		{lat: -3.833489785688335,  lng: 119.81508445594244}],
	      		//	strokeColor: "#000000",
						//	strokeOpacity: 2,
						//	strokeWeight: 1,
						//	fillColor: "#0D0822",
						//	fillOpacity: 0.4,
	      		//});
	          
	          // polygon_2.addListener('click', function(event) {
					  //   polygon_2.setEditable(!polygon_2.getEditable());
					    
					  // });


	          //var polyOptions = {
					  //   strokeWeight: 0,
					  //   fillOpacity: 0.45,
					  //   editable: true,
					  //   fillColor: '#FF1493'
					  // };

					  // var selectedShape;

					  // var drawingManager = new google.maps.drawing.DrawingManager({
					  //   drawingMode: google.maps.drawing.OverlayType.POLYGON,
					  //   drawingControl: false,
					  //   markerOptions: {
					  //     draggable: true
					  //   },
					  //   polygonOptions: polyOptions
					  // });

					  // $('#enablePolygon').click(function() {
					  //   drawingManager.setMap(map);
					  //   drawingManager.setDrawingMode(google.maps.drawing.OverlayType.POLYGON);
					  //   	$('#enablePolygon').hide();
					  // });

					  // $('#resetPolygon').click(function() {
					  //   if (selectedShape) {
					  //     selectedShape.setMap(null);
					  //   }
					  //   drawingManager.setMap(null);
					  //   $('#showonPolygon').hide();
					  //   $('#resetPolygon').hide();
					  //   $('#enablePolygon').show();
					  //   $('#luas_lahan').val(null)
					  //   $('#point').val(null)
					  // });

					  // google.maps.event.addListener(drawingManager, 'polygoncomplete', function(polygon) {
				   //   	var area = google.maps.geometry.spherical.computeArea(selectedShape.getPath());
				   //   	var area1 = google.maps.geometry.spherical.computeArea(selectedShape.getPath());
				   //   	// $('#areaPolygon').html(area.toFixed(2)+' Sq meters');
					  //   area = area/10000;
					  //   area1 = area1/10000;
					  //   area = numberWithCommas(area.toFixed(2))+" Ha";
					  //   area1 = area1.toFixed(3);
					  //   document.getElementById("luas_lahan").value = area;
					  //   // document.getElementById("luas_lahan1").value = area1;
					  //   var coordStr = "";
					  //   for (var i = 0; i < polygon.getPath().getLength(); i++) {
					  //     coordStr +="{lat: "+ polygon.getPath().getAt(i).lat() + ",  lng: "+
					  //     polygon.getPath().getAt(i).lng()+"},\n"
					  //     ;
					  //   }
					  //   document.getElementById("point").value = coordStr;
					  //   //console.log(coordStr);


					  //   $('#resetPolygon').show();

					  // });

					  // function clearSelection() {
					  //   if (selectedShape) {
					  //     selectedShape.setEditable(false);
					  //     selectedShape = null;
					  //   }
					  // }

					  // function setSelection(shape) {
					  //   clearSelection();
					  //   selectedShape = shape;
					  //   shape.setEditable(false);
					  // }

					  // google.maps.event.addListener(drawingManager, 'overlaycomplete', function(e) {
					  //   all_overlays.push(e);
					  //   if (e.type != google.maps.drawing.OverlayType.MARKER) {
					  //     // Switch back to non-drawing mode after drawing a shape.
					  //     drawingManager.setDrawingMode(null);

					  //     // Add an event listener that selects the newly-drawn shape when the user
					  //     // mouses down on it.
					  //     var newShape = e.overlay;
					  //     newShape.type = e.type;
					  //     google.maps.event.addListener(newShape, 'click', function() {
					  //       setSelection(newShape);
					  //     });

					      
					  //     setSelection(newShape);
					  //   }
					  // });

					  //google.maps.event.addListener(polygon_2.getPath(), "insert_at", getPolygonCoords);
		        //google.maps.event.addListener(polygon.getPath(), "remove_at", getPolygonCoords);
		        //google.maps.event.addListener(polygon_2.getPath(), "set_at", getPolygonCoords);

					  //function getPolygonCoords() {
	          //  var coordinates_poly = polygon_2.getPath().getArray();
	          //  //var newCoordinates_poly = [];
	          //  var coordStr = "";
	          //  for (var i = 0; i < coordinates_poly.length; i++){
	          //    lat_poly = coordinates_poly[i].lat();
	          //    lng_poly = coordinates_poly[i].lng();
	          //    // console.log(lat_poly)
	          //    
	          //    // latlng_poly = [lat_poly, lng_poly];
	          //    // newCoordinates_poly.push(latlng_poly);
	          //    
	          //    coordStr +="{lat: "+ lat_poly + ",  lng: "+
					  //    lng_poly+"},\n"
					  //    ;
	          //  }
	          //
	          //  // var str_coordinates_poly = JSON.stringify(newCoordinates_poly);
	          //  console.log(coordStr)
	          //  
	        	//}
					 
	                                         
	         	
	        	// for (var i = 0; i < polygon_<?=$kecamatan[0]->id_kecamatan?>.getPath().getLength(); i++) {
	         //    bounds.extend(polygon_<?=$kecamatan[0]->id_kecamatan?>.getPath().getAt(i));
	         //  }
	       		

		      	<?php foreach ($cari_data as $key => $value): ?>
		      		google.maps.event.addListener(lahan_<?=$value->id_lahan?>, 'click', function(event) {
								var vertices = this.getPath();
								var luas = google.maps.geometry.spherical.computeArea(lahan_<?=$value->id_lahan?>.getPath()) / 10000;
								luas = numberWithCommas(luas.toFixed(2));
								var contentString ='<div class="row small-spacing" >'+
								                    '<div class="card-content">'+
								                    '<div class="form-group">'+
								                    '<label for="inputEmail3" class="control-label">No PBB</label>'+
								                    '<input class="form-control" disabled="" value="<?=$value->no_pbb?>" >'+
								                    "</div>"+
								                    '<div class="form-group">'+
								                    '<label for="inputEmail3" class="control-label">Luas Lahan</label>'+
								                    '<input class="form-control" disabled="" value="'+luas+' Ha" >'+
								                    "</div>"+
								                    '<div class="form-group">'+
								                    '<label for="inputEmail3" class="control-label">Status Lahan</label>'+
								                    '<input class="form-control" disabled="" value="<?=$status[$value->id_lahan]?>" >'+
								                    "</div>"+
								                    '<div class="form-group">'+
								                    '<center><a href="<?=base_url()?>penyuluh/petani_detail/<?=$id?>/<?=$value->id_lahan?>" class="btn btn-info btn-sm waves-effect waves-light">Detail</a> &nbsp <button class="btn btn-danger btn-sm waves-effect waves-light" onclick="hapus_lahan(<?=$value->id_lahan?>)">Hapus</button></center>'+
								                    "</div>"+
								                    "</div>"+
								                    "</div>";
								infowindow.setContent(contentString);
								infowindow.setPosition(event.latLng);
								infowindow.open(map);
							});
		      	<?php endforeach ?>
			      	

		      	<?php foreach ($cari_data as $key => $value): ?>
		      		for (var i = 0; i < lahan_<?=$value->id_lahan?>.getPath().getLength(); i++) {
		            bounds.extend(lahan_<?=$value->id_lahan?>.getPath().getAt(i));
		          }
		      	<?php endforeach ?>
		                
		        map.fitBounds(bounds);

		      }

		    </script>

		    <div id="map_canvas" style="height: 600px;width: 100%"></div>
				<?php
			}
		}
		elseif ($this->input->post('proses') == 'tambah_lahan') {
			$data = $this->mpenyuluh->serialize($this->input->post('data'));
			// print_r($data);
			$cek_data = $this->mpenyuluh->tampil_data_where('tb_lahan',array('no_pbb' => $data['no_pbb']))->result();
			if (count($cek_data) > 0 ) {
				$this->session->set_flashdata('error', '<center>Lahan dengan No PBB <b>'.$data['no_pbb'].'</b> telah terdaftar dalam sistem sebelumnya. <br> Sila input kembali data</center>');
			}
			else{
				$this->mpenyuluh->insert('tb_lahan',$data);
				$this->session->set_flashdata('success', '<center>Lahan dengan No PBB <b>'.$data['no_pbb'].'</b> telah ditambahkan ke dalam sistem</center>');
			}
		}
		elseif ($this->input->post('proses') == 'ambil_peta') {
			$cek_data = $this->mpenyuluh->tampil_data_where('tb_kelurahan',array('id_kelurahan' => $this->input->post('cek_peta')))->result();
			$cari_data_lahan = $this->mpenyuluh->tampil_data_where('tb_lahan',array('kecamatan' => $main['datanya']->id_kecamatan))->result();
			?>
			<script type="text/javascript" src="https://maps.googleapis.com/maps/api/js?key=AIzaSyBw6bnAk0C2jIDDbz_dVRso9gUEnHLTH68&libraries=drawing,places,geometry&callback=initialize"></script>

	    <script type="text/javascript" >
	      $('#luas_lahan').val(null)
	      var geocoder;
	      var all_overlays = [];
	      function numberWithCommas(x) {
		      return x.toString().replace(/\B(?=(\d{3})+(?!\d))/g, ",");
		    }
	      
	      function initialize() {
	        var geolib = google.maps.geometry.spherical;
	        var infowindow = new google.maps.InfoWindow({
		        size: new google.maps.Size(150, 50)
		      });

	        var myOptions = {
	          zoom: 13,
	          center: new google.maps.LatLng(-4.015210, 119.658241),
	          mapTypeControl: false,
	          // mapTypeControlOptions: {
	          // style: google.maps.MapTypeControlStyle.DROPDOWN_MENU
	          // },
	          streetViewControl: true,
	          navigationControl: true,
	          mapTypeId: 'hybrid'
	        }
	        map = new google.maps.Map(document.getElementById("map_canvas"),myOptions);

	        google.maps.event.addListener(map, 'click', function() {
	          infowindow.close();
	        });

	        bounds = new google.maps.LatLngBounds();

        	var polygon_<?=$cek_data[0]->id_kelurahan?> = new google.maps.Polygon({
      			map: map,
      			path: [<?=$cek_data[0]->kordinat?>],
      			strokeColor: "#000000",
						strokeOpacity: 2,
						strokeWeight: 1,
						fillColor: "#0D0822",
						fillOpacity: 0.4,
      		});

        	<?php foreach ($cari_data_lahan as $key => $value): ?>
      			var lahan_<?=$value->id_lahan?> = new google.maps.Polygon({
	      			map: map,
	      			path: [<?=$value->point?>],
	      			strokeColor: "#000000",
							strokeOpacity: 2,
							strokeWeight: 1,
							fillColor: "#D2DFDF",
							fillOpacity: 0.3,
	      		});
      		<?php endforeach ?>

      		// var polygon_2 = new google.maps.Polygon({
      		//	map: map,
      		//	path: [{lat: -3.8346030873808146,  lng: 119.81190872046881},
					//		{lat: -3.836487133095519,  lng: 119.81517028663092},
					//		{lat: -3.833489785688335,  lng: 119.81508445594244}],
      		//	strokeColor: "#000000",
					//	strokeOpacity: 2,
					//	strokeWeight: 1,
					//	fillColor: "#0D0822",
					//	fillOpacity: 0.4,
      		//});
          
          // polygon_2.addListener('click', function(event) {
				  //   polygon_2.setEditable(!polygon_2.getEditable());
				    
				  // });


          var polyOptions = {
				    strokeWeight: 0,
				    fillOpacity: 0.45,
				    editable: true,
				    fillColor: '#FF1493'
				  };

				  var selectedShape;

				  var drawingManager = new google.maps.drawing.DrawingManager({
				    drawingMode: google.maps.drawing.OverlayType.POLYGON,
				    drawingControl: false,
				    markerOptions: {
				      draggable: true
				    },
				    polygonOptions: polyOptions
				  });

				  $('#enablePolygon').click(function() {
				    drawingManager.setMap(map);
				    drawingManager.setDrawingMode(google.maps.drawing.OverlayType.POLYGON);
				    	$('#enablePolygon').hide();
				  });

				  $('#resetPolygon').click(function() {
				    if (selectedShape) {
				      selectedShape.setMap(null);
				    }
				    drawingManager.setMap(null);
				    $('#showonPolygon').hide();
				    $('#resetPolygon').hide();
				    $('#enablePolygon').show();
				    $('#luas_lahan').val(null)
				    $('#point').val(null)
				  });

				  google.maps.event.addListener(drawingManager, 'polygoncomplete', function(polygon) {
			     	var area = google.maps.geometry.spherical.computeArea(selectedShape.getPath());
			     	var area1 = google.maps.geometry.spherical.computeArea(selectedShape.getPath());
			     	// $('#areaPolygon').html(area.toFixed(2)+' Sq meters');
				    area = area/10000;
				    area1 = area1/10000;
				    area = numberWithCommas(area.toFixed(2))+" Ha";
				    area1 = area1.toFixed(3);
				    document.getElementById("luas_lahan").value = area;
				    // document.getElementById("luas_lahan1").value = area1;
				    var coordStr = "";
				    for (var i = 0; i < polygon.getPath().getLength(); i++) {
				      coordStr +="{lat: "+ polygon.getPath().getAt(i).lat() + ",  lng: "+
				      polygon.getPath().getAt(i).lng()+"},\n"
				      ;
				    }
				    document.getElementById("point").value = coordStr;
				    //console.log(coordStr);


				    $('#resetPolygon').show();

				  });

				  function clearSelection() {
				    if (selectedShape) {
				      selectedShape.setEditable(false);
				      selectedShape = null;
				    }
				  }

				  function setSelection(shape) {
				    clearSelection();
				    selectedShape = shape;
				    shape.setEditable(false);
				  }

				  google.maps.event.addListener(drawingManager, 'overlaycomplete', function(e) {
				    all_overlays.push(e);
				    if (e.type != google.maps.drawing.OverlayType.MARKER) {
				      // Switch back to non-drawing mode after drawing a shape.
				      drawingManager.setDrawingMode(null);

				      // Add an event listener that selects the newly-drawn shape when the user
				      // mouses down on it.
				      var newShape = e.overlay;
				      newShape.type = e.type;
				      google.maps.event.addListener(newShape, 'click', function() {
				        setSelection(newShape);
				      });

				      
				      setSelection(newShape);
				    }
				  });

				  //google.maps.event.addListener(polygon_2.getPath(), "insert_at", getPolygonCoords);
	        //google.maps.event.addListener(polygon.getPath(), "remove_at", getPolygonCoords);
	        //google.maps.event.addListener(polygon_2.getPath(), "set_at", getPolygonCoords);

				  //function getPolygonCoords() {
          //  var coordinates_poly = polygon_2.getPath().getArray();
          //  //var newCoordinates_poly = [];
          //  var coordStr = "";
          //  for (var i = 0; i < coordinates_poly.length; i++){
          //    lat_poly = coordinates_poly[i].lat();
          //    lng_poly = coordinates_poly[i].lng();
          //    // console.log(lat_poly)
          //    
          //    // latlng_poly = [lat_poly, lng_poly];
          //    // newCoordinates_poly.push(latlng_poly);
          //    
          //    coordStr +="{lat: "+ lat_poly + ",  lng: "+
				  //    lng_poly+"},\n"
				  //    ;
          //  }
          //
          //  // var str_coordinates_poly = JSON.stringify(newCoordinates_poly);
          //  console.log(coordStr)
          //  
        	//}
				 
      		<?php foreach ($cari_data_lahan as $key => $value): ?>
	      		google.maps.event.addListener(lahan_<?=$value->id_lahan?>, 'click', function(event) {
							var vertices = this.getPath();
							var luas = google.maps.geometry.spherical.computeArea(lahan_<?=$value->id_lahan?>.getPath()) / 10000;
							luas = numberWithCommas(luas.toFixed(2));
							var contentString ='<div class="row small-spacing" >'+
							                    '<div class="card-content">'+
							                    '<div class="form-group">'+
							                    '<label for="inputEmail3" class="control-label">No PBB</label>'+
							                    '<input class="form-control" disabled="" value="<?=$value->no_pbb?>" >'+
							                    "</div>"+
							                    '<div class="form-group">'+
							                    '<label for="inputEmail3" class="control-label">Luas Lahan</label>'+
							                    '<input class="form-control" disabled="" value="'+luas+' Ha" >'+
							                    "</div>"+
							                    "</div>"+
							                    "</div>";
							infowindow.setContent(contentString);
							infowindow.setPosition(event.latLng);
							infowindow.open(map);
						});
	      	<?php endforeach ?>
			      	                                 
         	
        	for (var i = 0; i < polygon_<?=$cek_data[0]->id_kelurahan?>.getPath().getLength(); i++) {
            bounds.extend(polygon_<?=$cek_data[0]->id_kelurahan?>.getPath().getAt(i));
          }
	       
	                
	        map.fitBounds(bounds);

	      }

	    </script>
	    <center><input type="button"  class="btn btn-info waves-effect waves-light" id="enablePolygon" value="Tanda Kordinat Lahan" name="enablePolygon" /><input type="button" class="btn btn-danger waves-effect waves-light" id="resetPolygon" value="Reset Kembali Kordinat" style="display: none;" /></center><br>
	    <div id="map_canvas" style="height: 500px;width: 100%"></div>

			<?php
		}
		elseif ($this->input->post('proses') == "tables_petani") {
			$list = $this->m_tabel_ss->get_datatables(array('a.nik','a.nama'),array(null, 'a.nik','a.nama',null),array('a.nik' => 'asc'),"tb_petani a",array('table' => 'tb_petani_kecamatan b', 'join' => 'a.nik = b.nik'),'b.kecamatan = '.$main['datanya']->id_kecamatan);
	    $data = array();
	    $no = $_POST['start'];
	    foreach ($list as $field) {
	      $no++;
	      $row = array();
	      // $ket = str_replace("\r\n",'+', $field->ket);
	      $row[] = $no;
	      $row[] = $field->nik;
	      $row[] = $field->nama;
	      // $row[] = $field->waktu;
	      $row[] = '<center><a href="'.base_url().'penyuluh/petani_detail/'.$field->nik.'"><button type="button" title="Tampilkan PE]etani" data-nik="'.$field->nik.'" data-nama="'.$field->nama.'" class="lihat_informasi btn btn-primary btn-circle btn-sm waves-effect waves-light"><i class="ico fa fa-edit"></i></button></a> </center>';
	      $data[] = $row;
		  }

	    $output = array(
	      "draw" => $_POST['draw'],
	      "recordsTotal" => $this->m_tabel_ss->count_all("tb_petani a",array('table' => 'tb_petani_kecamatan b', 'join' => 'a.nik = b.nik'),'b.kecamatan = '.$main['datanya']->id_kecamatan),
	      "recordsFiltered" => $this->m_tabel_ss->count_filtered(array('a.nik','a.nama'),array(null, 'a.nik','a.nama',null),array('a.nik' => 'asc'),"tb_petani a",array('table' => 'tb_petani_kecamatan b', 'join' => 'a.nik = b.nik'),'b.kecamatan = '.$main['datanya']->id_kecamatan),
	      "data" => $data,
	    );
	    //output dalam format JSON
	    echo json_encode($output);
		}
		elseif ($this->input->post('proses') == 'tambah_petani') {
			$data = $this->mpenyuluh->serialize($this->input->post('data'));
			$cek_data = $this->mpenyuluh->tampil_data_where('tb_petani_kecamatan',array('nik' => $data['nik'],'kecamatan' => $main['datanya']->id_kecamatan))->result();
			if (count($cek_data) > 0) {
				// print_r('ada');

				$this->session->set_flashdata('error', '<center>Petani dengan NIK <b>'.$data['nik'].'</b> telah terdaftar dalam kecamatan '.$main['datanya']->nama_kecamatan.' sebelumnya.<br> Sila cek list petani untuk lebih lanjut</center>');
				
			}
			else
			{
				$cek_data = $this->mpenyuluh->tampil_data_where('tb_petani',array('nik' => $data['nik']))->result();
				print_r('tiada');
				if (count($cek_data) == 0) {
					$this->mpenyuluh->insert('tb_petani',$data);
					$this->mpenyuluh->insert('tb_user',array('nik_user' => $data['nik'], 'username' => $data['nik'], 'password' => 12345678, 'level' => 'petani'));
				}
				
				$this->mpenyuluh->insert('tb_petani_kecamatan',array('nik' => $data['nik'], 'kecamatan' => $main['datanya']->id_kecamatan));
				$this->session->set_flashdata('success', '<center>Petani dengan NIK <b>'.$data['nik'].'</b> telah ditambah ke dalam kecamatan '.$main['datanya']->nama_kecamatan.'</center>');
			}
			
			// print_r($data);
		}
		else
		{
			$this->load->view('penyuluh/menu/petani',$main);		
		}
		
	}

	function petani_detail(){
		$main['datanya'] = $this->mpenyuluh->custom_query("SELECT *,b.kecamatan AS nama_kecamatan FROM `tb_penyuluh` a join tb_kecamatan b ON a.kecamatan = b.id_kecamatan where a.nik = ".$this->session->userdata('penyuluh')['nik']." and a.nama = '".$this->session->userdata('penyuluh')['nama']."'")->result()[0];
		$main['header'] = "Halaman Detail Petani";
		$idnya = $this->uri->segment(3);
		$main['cek_data'] = $this->mpenyuluh->custom_query("SELECT * FROM tb_petani a join tb_petani_kecamatan b on a.nik = b.nik where a.nik = ".$idnya." and b.kecamatan = ".$main['datanya']->id_kecamatan)->result();
		$main['count_lahan'] = count($this->mpenyuluh->tampil_data_where('tb_lahan',array('nik_petani' => $idnya, 'kecamatan' => $main['datanya']->id_kecamatan))->result());
		$main['kelurahan'] = $this->mpenyuluh->tampil_data_where('tb_kelurahan',array('id_kecamatan' => $main['datanya']->id_kecamatan))->result();
		
		if (count($main['cek_data']) > 0) {
			$id_lahan = $this->uri->segment(4);
			$cek_lahan = $this->mpenyuluh->tampil_data_where('tb_lahan',array('id_lahan' => $id_lahan, 'nik_petani' => $idnya, 'kecamatan' => $main['datanya']->id_kecamatan))->result();
			if (count($cek_lahan) > 0) {
				// print_r('sini detailnya');
				$main['lahannya'] = $cek_lahan;
				$this->load->view('penyuluh/menu/petani_detail_lahan',$main);	
			}
			else
			{
				$this->load->view('penyuluh/menu/petani_detail',$main);	
			}
		}
		
		else
		{
			redirect('/penyuluh/petani');
		}

		
	}
	
	


	function logout()
	{
		$this->session->unset_userdata('penyuluh');
		$this->session->unset_userdata(array('nama','nik','level'));
		$this->session->set_flashdata('success', '<b>Anda Berhasil Logout</b><br>Terima Kasih Telah Menggunakan Sistem Ini');
		redirect('/home');
	}
	

}
?>