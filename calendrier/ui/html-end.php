<?php if(ONAIR){ ?>

	<script src="https://maps.googleapis.com/maps/api/js?v=3.exp&sensor=true&libraries=drawing,geometry"></script>
	<script src="/media/calendrier/ui/js/sc.js"></script>

	<script type="text/javascript">
		var _gaq = _gaq || []; _gaq.push(['_setAccount', '<?php echo ANALYTICS ?>']); _gaq.push(['_trackPageview']);
		(function() {
			var ga = document.createElement('script'); ga.type = 'text/javascript'; ga.async = true;
			ga.src = ('https:' == document.location.protocol ? 'https://ssl' : 'http://www') + '.google-analytics.com/ga.js';
			var s = document.getElementsByTagName('script')[0]; s.parentNode.insertBefore(ga, s);
		})();
	</script>

<?php }else{ ?>
	<script src="https://maps.googleapis.com/maps/api/js?v=3.exp&sensor=true&libraries=drawing,geometry"></script>

	<script src="/media/calendrier/ui/vendor/jquery/jquery-1.10.2.min.js"></script>
	<script src="/media/calendrier/ui/vendor/jqueryui/jquery-ui-1.10.3.custom.min.js"></script>
	<script src="/media/calendrier/ui/vendor/jQRangeSlider/jQDateRangeSlider-withRuler-min.js"></script>
	<script src="/media/calendrier/ui/vendor/bootstrap-datepicker/js/bootstrap-datepicker.js"></script>
	<script src="/media/calendrier/ui/vendor/Parsley/parsley.js"></script>

	<script src="/media/calendrier/ui/vendor/kapmaps/kapmaps.js"></script>
	<script src="/media/calendrier/ui/vendor/kapmaps/markerclusterer.js"></script>

	<script src="/media/calendrier/ui/js/main.js"></script>
	<script src="/user/module/calendrier/ui/js/dep.js"></script>
	<script src="/user/module/calendrier/ui/js/mvs.js"></script>
<?php } ?>
