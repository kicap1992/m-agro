<div class="main-menu">
	<header class="header">
		<a href="<?=base_url()?>" class="logo"><img src="<?=base_url()?>logo.png" width="25" height="25"> SIDRAP</a>
		<!-- <button type="button" class="button-close fa fa-times js__menu_close"></button> -->
		<div class="user">
			<a href="#" class="avatar"><img src="<?=base_url()?>logo.png" alt="" width="50" height="50"></a>
			<h4><a href="#"><?=$datanya->nama_kecamatan?></a></h4>
			<h5 class="position">Penyuluh Desa</h5>
			<!-- /.name -->
			
			<!-- /.control-wrap -->
		</div>
		<!-- /.user -->
	</header>
	<!-- /.header -->
	<div class="content">

		<div class="navigation">
			<h5 class="title">Menu</h5>
			<!-- /.title -->
			<ul class="menu js__accordion">


				<li <?php if ($this->uri->segment(2) == '' ) { echo 'class="current"'; } ?>>
					<a class="waves-effect" href="<?=base_url()?>penyuluh"><i class="menu-icon mdi mdi-view-dashboard"></i><span>Halaman Utama</span></a>
				</li>

				<li <?php if ($this->uri->segment(2) == 'petani' or $this->uri->segment(2) == 'petani_detail') { echo 'class="current"'; } ?>>
					<a class="waves-effect" href="<?=base_url()?>penyuluh/petani"><i class="menu-icon mdi mdi-view-dashboard"></i><span>Halaman Petani</span></a>
				</li>

				<li>
					<a class="waves-effect" style="cursor: pointer;" onclick="logout()"><i class="menu-icon mdi mdi-logout"></i><span>Logout</span></a>
				</li>

			</ul>
			
		</div>
		<!-- /.navigation -->
	</div>
	<!-- /.content -->
</div>