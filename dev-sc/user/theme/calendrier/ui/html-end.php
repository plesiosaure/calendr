	<script src="/media/ui/vendor/jquery/jquery-1.10.2.min.js"></script>
<?php if(ONAIR){ ?>
	<script src="/media/ui/dist/sc-<?php echo PKG_VERSION ?>.js"></script>
<?php }else{ ?>

	<script src="/media/ui/vendor/bootstrap-datepicker-custom/js/bootstrap-datepicker.js"></script>
	<script src="/media/ui/vendor/bootstrap/js/bootstrap-tooltip.js"></script>

	<script src="/media/ui/vendor/kapmaps/kapmaps.js"></script>
	<script src="/media/ui/vendor/kapmaps/markerclusterer.js"></script>

	<script src="/user/module/calendrier/ui/js/dep.js"></script>
	<script src="/user/module/calendrier/ui/js/mvs.js"></script>
	<script src="/media/ui/js/main.js"></script>
<?php } ?>