
<title><?php echo HTML_TITLE ?></title>
	<meta name="description" content="<?php echo HTML_DESCRIPTION ?>" />
	<meta name="keywords" content="<?php echo HTML_KEYWORDS ?>" />

	<?php if(ONAIR){  ?>
	<link rel="stylesheet" type="text/css" href="<?php echo 'http://'.DOMSTATIC.'/media/ui/dist/style-'.PKG_VERSION.'.css' ?>" />
	<?php }else{ ?>
	<link rel="stylesheet" type="text/css" href="http://<?php echo DOMSTATIC ?>/media/ui/css/__style.css" />
	<?php } ?>

	<meta name="language" content="<?php echo LOC ?>" />
	<meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
	<meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1, user-scalable=no">
	<meta name="robots" content="index,follow,all" />
	<link rel="icon" type="image/png" href="http://<?php echo DOMSTATIC ?>/media/ui/img/favicon/cal.png" />

	<script src="/media/ui/vendor/modernizr/modernizr.custom.79120.js"></script>

	<?php if(ONAIR){ ?>
	<script>
		(function(i,s,o,g,r,a,m){i['GoogleAnalyticsObject']=r;i[r]=i[r]||function(){
			(i[r].q=i[r].q||[]).push(arguments)},i[r].l=1*new Date();a=s.createElement(o),
			m=s.getElementsByTagName(o)[0];a.async=1;a.src=g;m.parentNode.insertBefore(a,m)
		})(window,document,'script','//www.google-analytics.com/analytics.js','ga');

		ga('create', 'UA-3345628-24', 'supercalendrier.com');
		ga('send', 'pageview');
	</script>
	<?php } ?>

	<?php if(preg_match('/(?i)msie/',$_SERVER['HTTP_USER_AGENT'])){ ?>
	<!--[if lt IE 9]>
	<meta http-equiv="X-UA-Compatible" content="IE=8" />
	<script src="http://html5shim.googlecode.com/svn/trunk/html5.js"></script>
	<![endif]-->
	<?php } ?>

