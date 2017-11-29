$(function(){

	var finder = $('#mobile-finder')
		, action = $('#geo-action')
		, actionPos = action.offset()
		, btnOptions = $('#show-map-options')
		, btnForm = $('#show-search-form')
		, searchForm = $('form', finder)
		, wrapper = $('#map-wrapper')
		, options = $('#map-options')
		, findMe = $('#find-me')
		, findMeDefault = findMe.html()
		, timer, geo_opt, top;

	if (Modernizr.geolocation) {

		reset();

		findMe.hammer().on('tap', function(){

			findMe.html('Recherche en cours...').prop('disabled', true);

			timer = setTimeout(function(){
				reset();
				prepareMap();
			}, 5000)

			geo_opt = {
				enableHighAccuracy: false,
				timeout: 5000,
				maximumAge: 1000*60*60 // 1h
			}

			navigator.geolocation.getCurrentPosition(geo_map, geo_err, geo_opt)
		});

		function geo_map(coo){
			clearTimeout(timer);
			reset();
			prepareMap(coo);
		}

		function geo_err(err){
			console.log(err)
			reset();
		}

		function reset(){
			clearTimeout(timer);

			findMe
				.addClass('visible')
				.html(findMeDefault)
				.prop('disabled', false);
		}

	}else{
		findMe.css('display', 'none');
	}

	function prepareMap(geoloc){
		var latlng = {}

		if(geoloc){
			console.log('faire un truc avec');
			console.log(geoloc);

			latlng = {
				lat: geoloc.coords.latitude,
				lng: geoloc.coords.longitude
			}

		}

		// Plus de place
		$('footer').css('display', 'none');
		searchForm.removeClass('visible')

		// Positionner le wrapper + options à la bonne place
		top = (parseInt(actionPos.top) + action.height() + 10) + 'px';
		wrapper.css({top: top, 'display': 'block'});
		options.css({top: top});

		// Permettre d'afficher/masquer les options
		btnOptions.hammer().on('tap', function(){
			if(options.hasClass('visible')){
				options.removeClass('visible')
				$(this).html('Afficher les options')
			}else{
				options.addClass('visible')
				$(this).html('Fermer les options')
			}
		})

		// Libérer la carte
		$('#map').appendTo(wrapper).css({
			'width': '100%',
			'height': '100%'
		});

		// On s'occuper des boutons
		btnOptions.addClass('visible')
		btnForm.addClass('visible')
		findMe.removeClass('visible')

		// Revenir à la recherche
		btnForm.hammer().on('tap', function(){
			// On afficher
			searchForm.addClass('visible');
			findMe.addClass('visible')

			// on masque
			wrapper.css('display', 'none');
			options.css('display', 'none');

			btnOptions.removeClass('visible');
			btnForm.removeClass('visible');
		})

		// On affiche la map
		buildMap('map', latlng);
	}

});

