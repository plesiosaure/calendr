<header class="clearfix">

	<div class="left">
		<a href="/"><img src="http://placehold.it/300x130&text=LOGO"></a>
	</div>

	<div id="loginTop">
		<?php if($me['id_user'] != NULL){ ?>

		    <p>Bienvenue <?php echo $me['userMail'] ?></p>
		    <a href="/compte/" class="btn btn-mini">Mon compte</a>
		    <a href="/?logout" class="btn btn-mini">Deconnexion</a>

		<?php }else{ ?>

		    <form method="post">
		        <span>Espace Privé</span>
		        <input type="hidden" name="log" value="login"  />
		        <input type="text" name="login" class="input-small" />
		        <input type="password" name="password" autocomplete="off" class="input-small" />
		        <button class="btn btn-mini" type="submit">se connecter</button>
		    </form>
		    <a href="/user/new" class="btn btn-mini">Creer un compte</a>
		    <a href="/user/lost" class="btn btn-mini">Mot de passe perdu</a>

		<?php } ?>
	</div>

</header>
