<div class="main-menu">
	<header class="header">
		<a href="<?=base_url()?>/petani" class="logo"><img src="<?=base_url()?>logo.png" width="25" height="25"> SIDRAP</a>
		<!-- <button type="button" class="button-close fa fa-times js__menu_close"></button> -->
		<div class="user">
			<a href="#" class="avatar"><img src="<?=base_url()?>logo.png" alt="" width="50" height="50"></a>
			<h4><a href="#">Petani</a></h4>
			<h5 class="position"><?=$datanya[0]->nama?></h5>
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


				<li <?php if ($this->uri->segment(2) == '' or $this->uri->segment(2) == 'detail_lahan') { echo 'class="current"'; } ?>>
					<a class="waves-effect" href="<?=base_url()?>petani"><i class="menu-icon mdi mdi-view-dashboard"></i><span>Halaman Utama</span></a>
				</li>

				<li <?php if ($this->uri->segment(2) == 'profil' ) { echo 'class="current"'; } ?>>
					<a class="waves-effect" href="<?=base_url()?>petani/profil"><i class="menu-icon mdi mdi-view-dashboard"></i><span>Halaman Profil</span></a>
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