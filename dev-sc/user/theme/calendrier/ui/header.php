<header class="clearfix">

	<div id="logo">
		<a href="/">
			<img src="/media/ui/img/logo/logo.jpg" height="129" width="457" alt="Super Calendrier">
		</a>
	</div>

	<?php if(true){ #if(ISDEMO){
		$pub = $this->apiLoad('pub')->pubCalendrier(2);
		if($pub) echo '<div id="top-pub" class="no-mobile">'.$pub.'</div>';
	}else if(false){ ?>

		<div id="loginTop">

			<?php if($me['id_user'] != NULL){ ?>
				<p>Bienvenue <?php echo $me['userMail'] ?></p>
				<a href="/compte/" class="btn btn-mini">Mon compte</a>
				<a href="/?logout" class="btn btn-mini">Deconnexion</a>
			<?php }else{ ?>
				<a href="/user/login" class="btn btn-mini">Se connecter</a>
				<a href="/user/new" class="btn btn-mini">Creer un compte</a>
			<?php } ?>

		</div>
	<?php } ?>

</header>
