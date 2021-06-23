<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Petani extends CI_Controller {

	public function __construct()
	{
		parent::__construct();
		// $this->load->helper('form');
		// $this->load->library('form_validation');

		$this->load->model('mpetani');
		$this->load->model('m_tabel_ss');
		date_default_timezone_set("Asia/Kuala_Lumpur");


		$petani = $this->session->userdata('petani');
		$cek_data_dulu = $this->mpetani->tampil_data_where('tb_petani',array('nik'=>$petani['nik']));

		if ($petani != '' and $petani != null) {
			if (count($cek_data_dulu->result()) > 0) {
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
		// print_r('sini petani page');
		// $main['datanya'] = $this->mpetani->custom_query("SELECT * FROM tb_lahan a join tb_petani b on a.nik_petani = b.nik where b.nik =".$this->session->userdata('petani')['nik'])->result();
		$main['datanya'] = $this->mpetani->tampil_data_where('tb_petani',array('nik' =>$this->session->userdata('petani')['nik']))->result();
		$main['header'] = 'Halaman Utama Petani';
		// print_r(count($main['datanya']));
		if ($this->input->post('proses') == 'update_status') {
			// print_r('sini update status');
			$id = $this->input->post('id');
			$status = $this->input->post('status');
			$produksi = $this->input->post('produksi');
			// print_r($produksi);
			if ($status == 'Panen') {
				$array_baru = array(
												array(
													'tanggal' => date('d-m-Y')." | ".date('H:i:s'),
													'status' => $status,
													'produksi' => $produksi
												)
											);
			}
			else
			{
				$array_baru = array(
												array(
													'tanggal' => date('d-m-Y')." | ".date('H:i:s'),
													'status' => $status
												)
											);
			}
			$cek_data = $this->mpetani->tampil_data_where('tb_lahan_detail',array('id_lahan' => $id))->result();
			if (count($cek_data) > 0) {
				$detail_array = json_decode($cek_data[0]->detail);
				$array = json_encode(array_merge($array_baru,$detail_array));
				// print_r($array);
				$this->mpetani->update('tb_lahan_detail',array('id_lahan' => $id),array('detail' => $array));
				$this->session->set_flashdata('success', '<center>Status Lahan Berhasil Diupdate</center>');
			}
			else{
				$this->mpetani->insert('tb_lahan_detail',array('id_lahan' => $id, 'detail' => json_encode($array_baru)));
				// print_r($array_baru);
				$this->session->set_flashdata('success', '<center>Status Lahan Berhasil Diupdate</center>');
			}
		}
		elseif ($this->input->post('proses') == 'ambil_peta_detail') {
			$cari_data_lahan = $this->mpetani->tampil_data_where('tb_lahan',array('nik_petani' => $this->session->userdata('petani')['nik'], 'id_lahan' => $this->input->post('id')))->result();

			foreach ($cari_data_lahan as $key => $value) {
				$status_lahan[$value->id_lahan] = "Belum Pernah Diupdate Oleh Anda";
				$color[$value->id_lahan] = "#111010";
				$cek_data = $this->mpetani->tampil_data_where('tb_lahan_detail',array('id_lahan' => $value->id_lahan))->result();
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


			if (count($cari_data_lahan) > 0) {
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
								                    '<div class="form-group">'+
								                    '<label for="inputEmail3" class="control-label">Status Lahan Sekarang</label>'+
								                    '<textarea style="resize : none" class="form-control" disabled=""><?=$status_lahan[$value->id_lahan]?></textarea>'+
								                    "</div>"+
								                    "<hr>"+
								                    '<div class="form-group">'+
								                    '<label for="inputEmail3" class="control-label">Ubah Status Lahan</label>'+
								                    '<select class="form-control" name="status" id="status" onchange="changeStatus(value,<?=$value->id_lahan?>)">'+
								                    '<option selected="" disabled="">-Pilih Status Lahan</option>'+
								                    '<option value="Pembibitan">Pembibitan</option>'+
								                    '<option value="Penanaman">Penanaman</option>'+
								                    '<option value="Panen">Panen</option>'+
								                    '<option value="Gagal Panen">Gagal Panen</option>'+
								                    '</select>'+
								                    "</div>"+
								                    '<div class="form-group" style="display : none" id="produksi_div<?=$value->id_lahan?>">'+
								                    '<label for="inputEmail3" class="control-label">Produksi</label>'+
								                    '<input type="text" class="form-control" id="produksi" placeholder="Jumlah Produksi (kg)" maxlength="5" onkeypress="return isNumberKey(event)">'+
								                    "</div>"+
								                    '<div class="form-group">'+
									                    '<center><button class="btn btn-success btn-sm waves-effect waves-light" onclick="update_status(<?=$value->id_lahan?>)">Update Status</button> </center>'+
									                    "</div>"+
								                    "</div>"+
								                    "</div>";
								infowindow.setContent(contentString);
								infowindow.setPosition(event.latLng);
								infowindow.open(map);
							});
		      	<?php endforeach ?>

		        <?php foreach ($cari_data_lahan as $key => $value): ?>
		        	for (var i = 0; i < lahan_<?=$value->id_lahan?>.getPath().getLength(); i++) {
		            bounds.extend(lahan_<?=$value->id_lahan?>.getPath().getAt(i));
		          }
		        <?php endforeach ?>  
		        map.fitBounds(bounds);

		      }
		    </script>

		    <script type="text/javascript">
		    	function isNumberKey(evt)
		      {
	         var charCode = (evt.which) ? evt.which : event.keyCode
	         if (charCode > 31 && (charCode < 48 || charCode > 57))
            return false;

	         	return true;
		      }
		    </script>
		   
		    <div id="map_canvas" style="height: 600px;width: 100%"></div>
				<?php
			}
			else
			{
				echo "<center><h4>Belum ada lahan yang ditambahkan oleh penyuluh desa anda</h4></center>";
			}	
				
		}
		elseif ($this->input->post('proses') == 'ambil_peta') {
			$cari_data_lahan = $this->mpetani->tampil_data_where('tb_lahan',array('nik_petani' => $this->session->userdata('petani')['nik']))->result();

			foreach ($cari_data_lahan as $key => $value) {
				$status_lahan[$value->id_lahan] = "Belum Pernah Diupdate Oleh Anda";
				$color[$value->id_lahan] = "#111010";
				$cek_data = $this->mpetani->tampil_data_where('tb_lahan_detail',array('id_lahan' => $value->id_lahan))->result();
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


			if (count($cari_data_lahan) > 0) {
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
								                    '<div class="form-group">'+
								                    '<label for="inputEmail3" class="control-label">Status Lahan Sekarang</label>'+
								                    '<textarea style="resize : none" class="form-control" disabled=""><?=$status_lahan[$value->id_lahan]?></textarea>'+
								                    "</div>"+
								                    "<hr>"+
								                    '<div class="form-group">'+
								                    '<label for="inputEmail3" class="control-label">Ubah Status Lahan</label>'+
								                    '<select class="form-control" name="status" id="status" onchange="changeStatus(value,<?=$value->id_lahan?>)">'+
								                    '<option selected="" disabled="">-Pilih Status Lahan</option>'+
								                    '<option value="Pembibitan">Pembibitan</option>'+
								                    '<option value="Penanaman">Penanaman</option>'+
								                    '<option value="Panen">Panen</option>'+
								                    '<option value="Gagal Panen">Gagal Panen</option>'+
								                    '</select>'+
								                    "</div>"+
								                    '<div class="form-group" style="display : none" id="produksi_div<?=$value->id_lahan?>">'+
								                    '<label for="inputEmail3" class="control-label">Produksi</label>'+
								                    '<input type="text" class="form-control" id="produksi" placeholder="Jumlah Produksi (kg)" maxlength="5" onkeypress="return isNumberKey(event)">'+
								                    "</div>"+
								                    '<div class="form-group">'+
									                    '<center><button class="btn btn-success btn-sm waves-effect waves-light" onclick="update_status(<?=$value->id_lahan?>)">Update Status</button> &nbsp <a href="<?=base_url()?>petani/detail_lahan/<?=$value->id_lahan?>" class="btn btn-info btn-sm waves-effect waves-light">Detail</a></center>'+
									                    "</div>"+
								                    "</div>"+
								                    "</div>";
								infowindow.setContent(contentString);
								infowindow.setPosition(event.latLng);
								infowindow.open(map);
							});
		      	<?php endforeach ?>

		        <?php foreach ($cari_data_lahan as $key => $value): ?>
		        	for (var i = 0; i < lahan_<?=$value->id_lahan?>.getPath().getLength(); i++) {
		            bounds.extend(lahan_<?=$value->id_lahan?>.getPath().getAt(i));
		          }
		        <?php endforeach ?>  
		        map.fitBounds(bounds);

		      }
		    </script>

		    <script type="text/javascript">
		    	function isNumberKey(evt)
		      {
	         var charCode = (evt.which) ? evt.which : event.keyCode
	         if (charCode > 31 && (charCode < 48 || charCode > 57))
            return false;

	         	return true;
		      }
		    </script>
		   
		    <div id="map_canvas" style="height: 600px;width: 100%"></div>
				<?php
			}
			else
			{
				echo "<center><h4>Belum ada lahan yang ditambahkan oleh penyuluh desa anda</h4></center>";
			}	
				
		}
		else
		{
			$cari_data_lahan = $this->mpetani->tampil_data_where('tb_lahan',array('nik_petani' => $this->session->userdata('petani')['nik']))->result();
			$jumlah_lahan = count($cari_data_lahan);
			$jumlah_pembibitan = 0;
			$jumlah_penanaman = 0;
			$jumlah_panen = 0;
			$jumlah_gagal_panen = 0;
			$jumlah_belum_update = 0;
			if ($jumlah_lahan > 0) {
				foreach ($cari_data_lahan as $key => $value) {
					$cek_data = $this->mpetani->tampil_data_where('tb_lahan_detail',array('id_lahan' => $value->id_lahan))->result();
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
			$this->load->view('petani/index',$main);
		}	
	}


	
	function detail_lahan(){
		// $array = array(
		// 					array(
		// 						'tanggal' => '17-08-2020',
		// 						'status' => 'Penanaman'
		// 					)
		// 				);
		// $array1 = array(
		// 					array(
		// 						'tanggal' => '18-10-2020',
		// 						'status' => 'Panen',
		// 						'produksi' => '3200'
		// 					)
		// 				);
		// // print_r(json_encode(array_merge($array1,$array)));
		// print_r(date('d-m-Y H:i:s'));
		$main['datanya'] = $this->mpetani->tampil_data_where('tb_petani',array('nik' =>$this->session->userdata('petani')['nik']))->result();
		$main['header'] = 'Halaman Detail Lahan Petani';
		$id_lahan = $this->uri->segment(3);
		// print_r($id_lahan);
		$cek_data = $this->mpetani->tampil_data_where('tb_lahan',array('id_lahan' => $id_lahan, 'nik_petani' => $this->session->userdata('petani')['nik']))->result();
		if (count($cek_data) > 0) {
			$main['lahannya'] = $cek_data;
			$main['status_lahannya'] =  $this->mpetani->tampil_data_where('tb_lahan_detail',array('id_lahan' => $id_lahan))->result();
			$this->load->view('petani/menu/detail_lahan',$main);
			// print_r('sini tampilkan detail lahan');
		}
		else
		{
			redirect('/petani');
		}

	}

	function profil() {
		// print_r('sini ganti profil / username dan password');
		$main['datanya'] = $this->mpetani->custom_query('SELECT * FROM tb_petani a join tb_user b on a.nik = b.nik_user WHERE a.nik ='.$this->session->userdata('petani')['nik'])->result();
		$main['count_lahan'] = count($this->mpetani->tampil_data_where('tb_lahan',array('nik_petani' =>$this->session->userdata('petani')['nik']))->result());
		$main['header'] = "Halaman Profil";
		if ($this->input->post('proses') == 'ubah_detail') {
			// print_r("sini ubah password");
			$data = $this->mpetani->serialize($this->input->post('data'));
			// print_r($data);
			$this->mpetani->update('tb_user',array('nik_user' => $this->session->userdata('petani')['nik']),$data);
			$this->session->set_flashdata('success', 'Username dan Password berhasil diupdate.');
		}
		else
		{
			$this->load->view('petani/menu/profil',$main);
		}
		
	}
	


	function logout()
	{
		$this->session->unset_userdata('petani');
		// $this->session->unset_userdata(array('nama','nik','level'));
		$this->session->set_flashdata('success', '<b>Anda Berhasil Logout</b><br>Terima Kasih Telah Menggunakan Sistem Ini');
		redirect('/home');
	}
	

}
?>