<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Home extends CI_Controller {

	// var $table = $this->mhome->;
	public function __construct()
	{
		parent::__construct();
		// $this->load->helper('form');
		// $this->load->library('form_validation');

		$this->load->model('mhome');
		
	}
	
	function index()
	{	
		$main['header'] = 'Halaman Utama Admin';
		$cari_data_lahan = $this->mhome->custom_query('SELECT * FROM `tb_lahan` a join tb_petani b on a.nik_petani = b.nik')->result();
		$jumlah_lahan = count($cari_data_lahan);
		$jumlah_pembibitan = 0;
		$jumlah_penanaman = 0;
		$jumlah_panen = 0;
		$jumlah_gagal_panen = 0;
		$jumlah_belum_update = 0;
		if ($jumlah_lahan > 0) {
			foreach ($cari_data_lahan as $key => $value) {
				$cek_data = $this->mhome->tampil_data_where('tb_lahan_detail',array('id_lahan' => $value->id_lahan))->result();
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
			$kecamatan = $this->mhome->tampil_data_keseluruhan('tb_kecamatan')->result();
			

			foreach ($cari_data_lahan as $key => $value) {
				$status_lahan[$value->id_lahan] = "Belum Pernah Diupdate Oleh Petani";
				$color[$value->id_lahan] = "black";
				$cek_data = $this->mhome->tampil_data_where('tb_lahan_detail',array('id_lahan' => $value->id_lahan))->result();
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
			$this->load->view('home/index',$main);		
		}
		
	}

	


	function login()
	{
		if ($this->input->post('login')) {
			$username = $this->input->post('username');
			$password = $this->input->post('password');

			$cek_data = $this->mhome->tampil_data_where('tb_user',array('username' => $username,'password' => $password));

			if (count($cek_data->result()) > 0) {
				// echo "username ada";
				foreach ($cek_data->result() as $key => $value);
				if ($value->level == 'admin') {
					$cek_data_admin = $this->mhome->tampil_data_where('tb_admin',array('nik_admin' => $value->nik_admin));
					foreach ($cek_data_admin->result() as $key2 => $value2);
					$nik_admin = $value2->nik_admin;
					$nama_admin = $value2->nama;
					$jabatan_admin = $value2->jabatan;
					$this->session->set_userdata('admin',array('nik' => $nik_admin,'nama'=>$nama_admin,'jabatan'=>$jabatan_admin,'level'=>'Admin'));
					$this->session->set_flashdata('success', '<b>SELAMAT DATANG</b><br>Admin '.$nama_admin.' telah berhasil login');
					redirect('/admin');
				}elseif ($value->level == 'petani') {
					$cek_data_petambak = $this->mhome->tampil_data_where('tb_petani',array('nik' => $value->nik_user));
					foreach ($cek_data_petambak->result() as $key2 => $value2);
					$nik_petambak = $value2->nik;
					$nama_petambak = $value2->nama;
					$this->session->set_userdata('petani',array('nik' => $nik_petambak,'nama'=>$nama_petambak,'level'=>'Petani'));
					$this->session->set_flashdata('success', '<b>SELAMAT DATANG</b><br>PEtani '.$nama_petambak.' telah berhasil login');
					redirect('/petani');
				}elseif($value->level == 'penyuluh') {
					$cek_data_penyuluh = $this->mhome->tampil_data_where('tb_penyuluh',array('nik' => $value->nik_penyuluh));
					// print_r(count($cek_data_penyuluh->result()));
					foreach ($cek_data_penyuluh->result() as $key2 => $value2);
					$nik_penyuluh = $value2->nik;
					$nama_penyuluh = $value2->nama;

					// echo $nama_peyuluh;
					$this->session->set_userdata('penyuluh', array('nik' => $nik_penyuluh,'nama'=>$nama_penyuluh,'kecamatan'=> $value2->kecamatan,'level'=>'Petambak'));
					$this->session->set_flashdata('success', '<b>SELAMAT DATANG</b><br>Penyuluh '.$nama_penyuluh.' telah berhasil login');
					redirect('/penyuluh');
				}
				// $this->session->set_userdata('nik',)
			}else{
				$this->session->set_flashdata('warning', '<b>Error</b><br>Username dan Password Yang Dimasukkan Salah');
				redirect('/home/login');			
			}
		}else{
			$main['header']='Halaman Pendaftaran';
			$this->load->view('home/login',$main);
		}
		
	}

	

	function destroy_segala()
	{
		// $this->session->sess_destroy();
		$this->session->set_userdata('nik',1234);
		$this->session->set_userdata('nama','asdasdas');
		$this->session->set_userdata('level','Petambak');
	}



	// function petanya() {
	// 	$peta = '';

	// 	$peta = json_decode($peta);
	// 	print_r($peta[0]->kordinat);
	// }
	

	

}
?>