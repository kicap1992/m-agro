<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Admin extends CI_Controller {

	public function __construct()
	{
		parent::__construct();
		// $this->load->helper('form');
		// $this->load->library('form_validation');

		// $this->load->model('mlogin');
		// if ($this->uri->segment(2)!="hahaha") {
		// 	redirect('/home');
		// }

		$this->load->model('madmin');
		$this->load->model('m_tabel_ss');

		$admin = $this->session->userdata('admin');	
	}
	
	function index()
	{	
		$main['header'] = 'Halaman Utama Admin';
		$cari_data_lahan = $this->madmin->custom_query('SELECT * FROM `tb_lahan` a join tb_petani b on a.nik_petani = b.nik')->result();
		$jumlah_lahan = count($cari_data_lahan);
		$jumlah_pembibitan = 0;
		$jumlah_penanaman = 0;
		$jumlah_panen = 0;
		$jumlah_gagal_panen = 0;
		$jumlah_belum_update = 0;
		if ($jumlah_lahan > 0) {
			foreach ($cari_data_lahan as $key => $value) {
				$cek_data = $this->madmin->tampil_data_where('tb_lahan_detail',array('id_lahan' => $value->id_lahan))->result();
				if (count($cek_data) > 0) {
					$detail = json_decode($cek_data[0]->detail);
					if ($detail[0]->status == 'Pembibitan') {
						$jumlah_pembibitan = $jumlah_pembibitan + 1;
					}
					if ($detail[0]->status == 'Penanaman') {
						$jumlah_penanaman = $jumlah_penanaman + 1;
					}
					if ($detail[0]->status == 'Panen') {
						$jumlah_panen = $jumlah_panen + 1;
					}
					if ($detail[0]->status == 'Gagal Panen') {
						$jumlah_gagal_panen = $jumlah_gagal_panen + 1;
					}
				}
				else
				{
					$jumlah_belum_update  += 1;
				}
					
			}
		}
		$main['jumlahnya'] = array('lahan' => $jumlah_lahan, 'pembibitan' => $jumlah_pembibitan, 'penanaman' => $jumlah_penanaman, 'panen' => $jumlah_panen, 'gagal_panen' => $jumlah_gagal_panen, 'belum_update' => $jumlah_belum_update);

		if ($this->input->post('proses') == 'ambil_peta') {
			$kecamatan = $this->madmin->tampil_data_keseluruhan('tb_kecamatan')->result();
			

			foreach ($cari_data_lahan as $key => $value) {
				$status_lahan[$value->id_lahan] = "Belum Pernah Diupdate Oleh Petani";
				$color[$value->id_lahan] = "black";
				$cek_data = $this->madmin->tampil_data_where('tb_lahan_detail',array('id_lahan' => $value->id_lahan))->result();
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

	        <?php foreach ($kecamatan as $key => $value): ?>
	        	var polygon_<?=$value->id_kecamatan?> = new google.maps.Polygon({
	      			map: map,
	      			path: [<?=$value->kordinat?>],
	      			strokeColor: "#000000",
							strokeOpacity: 2,
							strokeWeight: 3,
							// fillColor: "#0D0822",
							// fillOpacity: 0.4,
	      		});
	        <?php endforeach ?>
	        	
	        
	        <?php foreach ($cari_data_lahan as $key => $value): ?>
      			var lahan_<?=$value->id_lahan?> = new google.maps.Polygon({
	      			map: map,
	      			path: [<?=$value->point?>],
	      			strokeColor: "#000000",
							strokeOpacity: 2,
							strokeWeight: 1,
							fillColor: "<?=$color[$value->id_lahan]?>",
							fillOpacity: 0.4,
	      		});
      		<?php endforeach ?>    

		      <?php foreach ($kecamatan as $key => $value): ?>
		      	google.maps.event.addListener(polygon_<?=$value->id_kecamatan?>, 'click', function(event) {
							var vertices = this.getPath();
							var luas = google.maps.geometry.spherical.computeArea(polygon_<?=$value->id_kecamatan?>.getPath()) / 10000;
							luas = numberWithCommas(luas.toFixed(2));
							var contentString ="<div class='form-group' >"+
							                    "<h5>Kecamatan : <?=$value->kecamatan?></h5>"+
							                    "<h5>Luas : "+luas + " Ha"+"</h5>"+
							                    "</div>";

							infowindow.setContent(contentString);
							infowindow.setPosition(event.latLng);
							infowindow.open(map);
						});
		      <?php endforeach ?>
		                                         
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
								                    '<center><a href="<?=base_url()?>admin/petani/<?=$value->nik_petani?>/<?=$value->id_lahan?>" class="btn btn-info btn-sm waves-effect waves-light">Detail</a></center>'+
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
		      	<?php foreach ($kecamatan as $key => $value): ?>
		      		for (var i = 0; i < polygon_<?=$value->id_kecamatan?>.getPath().getLength(); i++) {
		            bounds.extend(polygon_<?=$value->id_kecamatan?>.getPath().getAt(i));
		          }	
		      	<?php endforeach ?>
		                                      	
         	<?php endif ?>                                 
		        
		        


	       	
	       
	                
	        map.fitBounds(bounds);

	      }
	    </script>
	    
	    <div id="map_canvas" style="height: 600px;width: 100%"></div>
			<?php
		}
		else
		{
			$this->load->view('admin/index',$main);		
		}
		
	}

	function penyuluh()
	{
		$main['header'] = 'Halaman Penyuluh';
		// if ($this->input->post('proses') == "tables_penyuluh") {
		if ($this->input->post('proses') == "tables_penyuluh") {
			$list = $this->m_tabel_ss->get_datatables(array('a.nik','a.nama'),array(null, 'a.nik','a.nama','b.kecamatan',null),array('b.id_kecamatan' => 'asc'),"tb_penyuluh a",array('table' => 'tb_kecamatan b', 'join' => ' a.kecamatan = b.id_kecamatan'),null);
	    $data = array();
	    $no = $_POST['start'];
	    foreach ($list as $field) {
	      $no++;
	      $row = array();
	      // $ket = str_replace("\r\n",'+', $field->ket);
	      $row[] = $no;
	      $row[] = $field->nik;
	      $row[] = $field->nama;
	      $row[] = $field->kecamatan;
	      // $row[] = $field->waktu;
	      // $row[] = '<center><a href="'.base_url().'admin/petani/'.$field->nik.'"><button type="button" title="Tampilkan Petani"  class="lihat_informasi btn btn-primary btn-circle btn-sm waves-effect waves-light"><i class="ico fa fa-edit"></i></button></a> </center>';
	      $row[] = '<center><a href="#"><button type="button" title="Tampilkan Penyuluh"  class="lihat_informasi btn btn-primary btn-circle btn-sm waves-effect waves-light"><i class="ico fa fa-edit"></i></button></a> </center>';
	      $data[] = $row;
		  }

	    $output = array(
	      "draw" => $_POST['draw'],
	      "recordsTotal" => $this->m_tabel_ss->count_all("tb_penyuluh a",array('table' => 'tb_kecamatan b', 'join' => ' a.kecamatan = b.id_kecamatan'),null),
	      "recordsFiltered" => $this->m_tabel_ss->count_filtered(array('a.nik','a.nama'),array(null, 'a.nik','a.nama','b.kecamatan',null),array('b.id_kecamatan' => 'asc'),"tb_penyuluh a",array('table' => 'tb_kecamatan b', 'join' => ' a.kecamatan = b.id_kecamatan'),null),
	      "data" => $data,
	    );
	    //output dalam format JSON
	    echo json_encode($output);
		}
		else{
			$this->load->view('admin/menu/penyuluh',$main);
		}
		
	}

	function petani()
	{
		// print_r("sini petani");
		$main['header'] = 'Halaman Petani';
		$main['petani'] = $this->madmin->tampil_data_keseluruhan('tb_petani')->result();
		$cari_data_lahan = $this->madmin->custom_query('SELECT * FROM `tb_lahan` a join tb_petani b on a.nik_petani = b.nik')->result();
		$jumlah_lahan = count($cari_data_lahan);
		$jumlah_pembibitan = 0;
		$jumlah_penanaman = 0;
		$jumlah_panen = 0;
		$jumlah_gagal_panen = 0;
		$jumlah_belum_update = 0;
		if ($jumlah_lahan > 0) {
			foreach ($cari_data_lahan as $key => $value) {
				$cek_data = $this->madmin->tampil_data_where('tb_lahan_detail',array('id_lahan' => $value->id_lahan))->result();
				if (count($cek_data) > 0) {
					$detail = json_decode($cek_data[0]->detail);
					if ($detail[0]->status == 'Pembibitan') {
						$jumlah_pembibitan = $jumlah_pembibitan + 1;
					}
					if ($detail[0]->status == 'Penanaman') {
						$jumlah_penanaman = $jumlah_penanaman + 1;
					}
					if ($detail[0]->status == 'Panen') {
						$jumlah_panen = $jumlah_panen + 1;
					}
					if ($detail[0]->status == 'Gagal Panen') {
						$jumlah_gagal_panen = $jumlah_gagal_panen + 1;
					}
				}
				else
				{
					$jumlah_belum_update  += 1;
				}
					
			}
		}
		$main['jumlahnya'] = array('lahan' => $jumlah_lahan, 'pembibitan' => $jumlah_pembibitan, 'penanaman' => $jumlah_penanaman, 'panen' => $jumlah_panen, 'gagal_panen' => $jumlah_gagal_panen, 'belum_update' => $jumlah_belum_update);

		$nik_petani = $this->uri->segment(3);
		$id_lahan = $this->uri->segment(4);
		$cek_data_petani = $this->madmin->tampil_data_where('tb_petani', array('nik' => $nik_petani))->result();
		$cek_data_petani_lahan = $this->madmin->tampil_data_where('tb_lahan', array('nik_petani' => $nik_petani,'id_lahan' => $id_lahan))->result();
		if (count($cek_data_petani_lahan) > 0) {
			$main['cek_data'] = $cek_data_petani;
			$main['lahannya'] = $cek_data_petani_lahan;
			$main['status_lahannya'] =  $this->madmin->tampil_data_where('tb_lahan_detail',array('id_lahan' => $id_lahan))->result();
			// $main['count_lahan'] = count($this->madmin->tampil_data_where('tb_lahan',array('nik_petani' =>$nik_petani))->result());
			$this->load->view('admin/menu/petani_detail_lahan',$main);	
		}
		elseif ($this->input->post('proses') == 'cari_lahan_petani') {
			$id = $this->input->post('id');
			if ($this->input->post('idnya') == 'biasa') {
				$cari_data = $this->madmin->tampil_data_where('tb_lahan',array('nik_petani' => $id))->result();
				$cari_data_lahan =  $this->madmin->custom_query('SELECT * FROM `tb_lahan` a join tb_petani b on a.nik_petani = b.nik where a.nik_petani = '.$id)->result();
			}
			elseif ($this->input->post('idnya') == 'detail') {
				$cari_data = $this->madmin->tampil_data_where('tb_lahan',array('nik_petani' => $this->input->post('nik'),'id_lahan' => $id))->result();
				$cari_data_lahan =  $this->madmin->custom_query('SELECT * FROM `tb_lahan` a join tb_petani b on a.nik_petani = b.nik where a.nik_petani = '.$this->input->post('nik').' and a.id_lahan = '.$id)->result();
			}

			foreach ($cari_data_lahan as $key => $value) {
				$status_lahan[$value->id_lahan] = "Belum Pernah Diupdate Oleh Petani";
				$color[$value->id_lahan] = "black";
				$cek_data = $this->madmin->tampil_data_where('tb_lahan_detail',array('id_lahan' => $value->id_lahan))->result();
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
			
			if (count($cari_data) > 0) {
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

	        	
		    		<?php foreach ($cari_data as $key => $value): ?>
		    			var lahan_<?=$value->id_lahan?> = new google.maps.Polygon({
		      			map: map,
		      			path: [<?=$value->point?>],
		      			strokeColor: "#000000",
								strokeOpacity: 2,
								strokeWeight: 1,
								fillColor: "<?=$color[$value->id_lahan]?>",
								fillOpacity: 0.4,
		      		});	
	    			<?php endforeach ?>	

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
								                    '<label for="inputEmail3" class="control-label">Status Lahan Sekarang</label>'+
								                    '<textarea style="resize : none" class="form-control" disabled=""><?=$status_lahan[$value->id_lahan]?></textarea>'+
								                    "</div>"+
								                    <?php if ($this->input->post('idnya') == 'biasa'): ?>
								                    	'<div class="form-group">'+
									                    '<center><a href="<?=base_url()?>admin/petani/<?=$value->nik_petani?>/<?=$value->id_lahan?>" class="btn btn-info btn-sm waves-effect waves-light">Detail</a></center>'+
									                    "</div>"+
								                    <?php endif ?>
									                    
								                    "</div>"+
								                    "</div>";
								infowindow.setContent(contentString);
								infowindow.setPosition(event.latLng);
								infowindow.open(map);
							});
	    			<?php endforeach ?>

	    			<?php foreach ($cari_data as $key => $value): ?>
	    				var luas = google.maps.geometry.spherical.computeArea(lahan_<?=$value->id_lahan?>.getPath()) / 10000;
							luas = numberWithCommas(luas.toFixed(2));
	    				for (var i = 0; i < lahan_<?=$value->id_lahan?>.getPath().getLength(); i++) {
		            bounds.extend(lahan_<?=$value->id_lahan?>.getPath().getAt(i));
		          }
		          $("#status").html('<?=$status_lahan[$value->id_lahan]?>');
		          $("#luas_lahannya").val(luas+" Ha")
	    			<?php endforeach ?>
		      		
		                
		        map.fitBounds(bounds);



		      }

		    </script>

		    <div id="map_canvas" style="height: 482px;width: 100%"></div>
				<?php
			}else{
				?>
				<center><h4><i>"Belum ada lahan yang diinput oleh penyuluh desa pada petani ini"</i></h4></center>
				<?php
			}
			
		}
		elseif (count($cek_data_petani) > 0) {
			$main['cek_data'] = $cek_data_petani;
			$main['count_lahan'] = count($this->madmin->tampil_data_where('tb_lahan',array('nik_petani' =>$nik_petani))->result());
			$this->load->view('admin/menu/petani_detail',$main);	
		}
		elseif ($this->input->post('proses') == "tables_petani") {
			$list = $this->m_tabel_ss->get_datatables(array('a.nik','a.nama'),array(null, 'a.nik','a.nama',null),array('a.nik' => 'asc'),"tb_petani",null,null);
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
	      $row[] = '<center><a href="'.base_url().'admin/petani/'.$field->nik.'"><button type="button" title="Tampilkan Petani"  class="lihat_informasi btn btn-primary btn-circle btn-sm waves-effect waves-light"><i class="ico fa fa-edit"></i></button></a> </center>';
	      $data[] = $row;
		  }

	    $output = array(
	      "draw" => $_POST['draw'],
	      "recordsTotal" => $this->m_tabel_ss->count_all("tb_petani",null,null),
	      "recordsFiltered" => $this->m_tabel_ss->count_filtered(array('a.nik','a.nama'),array(null, 'a.nik','a.nama',null),array('a.nik' => 'asc'),"tb_petani",null,null),
	      "data" => $data,
	    );
	    //output dalam format JSON
	    echo json_encode($output);
		}
		else
		{
			$this->load->view('admin/menu/petani',$main);	
		}
		

	}


	function logout()
	{
		$this->session->unset_userdata('admin');
		// $this->session->unset_userdata(array('nama','nik','level'));
		$this->session->set_flashdata('success', '<b>Anda Berhasil Logout</b><br>Terima Kasih Telah Menggunakan Sistem Ini');
		redirect('/home');
	}


}
?>