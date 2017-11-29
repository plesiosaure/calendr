<!DOCTYPE html>
<html lang="<?php echo LOC ?>">
<head>
    <title></title>
    <meta name="description" content="<?php echo HTML_DESCRIPTION ?>" />
    <meta name="keywords" content="<?php echo HTML_KEYWORDS ?>" />

	<?php include(MYTHEME . '/ui/html-head.php'); ?>
</head>
<body>

<header>
    header
</header>


<div id="main" class="clearfix">

	<div class="left">
		@LEFT@
	</div>

	<div class="right">
		@RIGHT@
	</div>

</div>

<footer>
    footer
</footer>




<?php
#include(MYTHEME.'/ui/html-end.php');
?>

</body></html>