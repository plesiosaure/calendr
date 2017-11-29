$(function() {

	"use strict";

	var $kapmap         // reference map
		, currentDep      // departement courant
//	, currentDate     // la semaine courante a afficher
		, clusters = {}   // Clusters (contient : {auto}, {moto}, {collection} )
		, currentDate = $('button.week-num.current').attr('data-date')
	//	, currentDate = '2014/02/06'
		, moisList = ['Jan', 'Févr.', 'Mars', 'Avr.',  'Mai', 'Juin', 'Juil.', 'Août', 'Sep.', 'Oct.', 'Nov.', 'Déc.']
		, weekMS = 604800000; // Nombre de ms par semaine


	window.buildMap = function(id, latlng){

		var opt = {
			'disableDefaultUI': false,
			'polyStyle': {
				fillColor: 'red',
				fillOpacity: 0.5,
				strokeWeight: 1,
				strokeColor: '#444',
				clickable: false,
				editable: false,
				zIndex: 1
			}
		};

		// Si on fournit un lat/lng , centrer dessus a l'init de la map
		if(latlng && latlng.lat && latlng.lng){
			opt.center = {
				lat:   latlng.lat,
				lng:   latlng.lng,
				zoom:  10
			}
		}

		if (!$('#'+id).length || !$.fn.kapmap) return

		// Activer kapmap
		$('#'+id).kapmap(opt);
		$kapmap = $('#'+id).data('kapmap');

		// Activer les clusters
		enableClusterers();

		// Bind select event, on change load POLYGON & DISPLAY
		$('#dep-select').on('change', function() {
			currentDep = $(this).children('option:selected').val();

			$.ajax({
				url : '/map/departement',
				data : {'dep': currentDep},
				dataType : 'json'
			}).done(function(d) {
					$kapmap.clearPolygons(); // Clean
					var poly = $kapmap.loadPolygon(d); // Display

					$kapmap.setMapCenter( // center & zoomin
						poly.getBounds().getCenter(),
						8
					);

					var manif = getManifestations(getSelectedCategories(), getSelectedDep());
					manif.done(function(d) {
						injectManifestations(d, true);
					});
				});

		});

		// Load ALL markers ON START
		getManifestations(null, 0).done(function(d) {
			injectManifestations(d, true);
		});

		// Filter Markers on click Todo: devrait réellement filtrer plutot que d'ajaxer
		$('#categories input[data-cat]').on('click', function() {
			var manif = getManifestations(getSelectedCategories(), getSelectedDep());
			manif.done(function(d) {
				injectManifestations(d, true);
			});
		});

	}

	// FUNCTIONS /////////////////////////////////////////////////////////////////////////////////////////////////////////

	function isMapLoading(loading) {
		if (loading) {
			$('#homeload').css('opacity', 1)
		} else {
			$('#homeload').css('opacity', 0)
		}
	}

	/**
	 * Retourne le departement selectionné
	 * @returns Int departement
	 */
	function getSelectedDep() {
		return $('#dep-select').children('option:selected').val() || 0;
	}

	/**
	 * Renvoie les catégories sélectionnées
	 * @returns Array selected
	 */
	function getSelectedCategories() {
		var selected = new Array();
		$('#categories input[data-cat]:checked').each(function() {
			selected.push($(this).attr('data-cat'));
		});
		return selected;
	}

	/**
	 * Injecte les manifs dans la carte, si (true)reset, clean
	 * les précédents markers
	 * @param data [ {manif}, { ... } ]
	 * @param Bool reset
	 */
	function injectManifestations(data, reset) {

		if (reset) {
			$kapmap.clearMarkers(); // clean kapmap markerStore
			clearClusterers(); // clean clusterers
		}

		for (var i = 0; i < data.length; i++) {

			if (!data[i].gps) continue;

			var marker,
				icon;

			switch (data[i].type) {
				case 'auto' :       icon = '/media/ui/img/map/marker-red.png'; break;
				case 'moto' :       icon = '/media/ui/img/map/marker-blue.png'; break;
				case 'collection' : icon = '/media/ui/img/map/marker-yellow.png'; break;
				default :           icon = '/media/ui/img/map/marker-red.png';
			}

			marker = $kapmap.displayCustomMarker(  // generate Marker
				data[i].gps,
				icon,
				$kapmap.getInfoWindow(
					'<p class="infotitle">'+data[i].name+'</p>' +
					'<a href="'+data[i].link+'">En savoir +</a>'
				)
			);
			marker._kapdata = data[i]; // save data in marker

			marker.on('click', function() { // bind click event
				$kapmap.closeInfoWindows();
				this._kapinfo.open($kapmap.map, this);
			});

			/**
			 * Si on a un poly de département et que le marker n'est pas a l'intérieur,
			 * signaler un mauvais géocode a l'api, masquer le marker et l'exclure du cluster
			 */
			var checkok = true;
			var poly = $kapmap.getLastPolygon();
			if (poly && poly.constructor === google.maps.Polygon) {
				var bounds = poly.getBounds();
				if (!bounds.contains(marker.getPosition())) {
					marker.setMap(null);
					checkok = false;
					console.warn('-- BOUNDS CHECK FAILED FOR ', marker._kapdata._id);
					$.ajax({
						url: '/map/fail',
						data: {_id: marker._kapdata._id}
					});
				}
			}

			// Ajouter dans le cluster correspondant
			if (checkok) clusters[marker._kapdata.type].addMarker(marker);
		}

		isMapLoading(false);
	}

	/**
	 * Helper ajax, return promise jqXhr
	 * @param Array type
	 * @param String dep
	 * @returns jqXhr
	 */
	function getManifestations(type, dep) {

		isMapLoading(true);

		var data = { dep: dep, date: currentDate }
		if (type && type.length) data.category = type.join(',');

		return $.ajax({
			url: '/map/manifestation',
			dataType: 'json',
			data: data
		});
	}

	/**
	 * Vide tous les clusterers
	 */
	function clearClusterers() {
		clusters.moto.clearMarkers();
		clusters.auto.clearMarkers();
		clusters.collection.clearMarkers();
	}

	/**
	 * Active les clusterers (car, moto, collection) avec leurs options
	 */
	function enableClusterers() {

		clusters.moto = {
			gridSize: 50,
			styles: [
				{textColor: 'white', url: '/media/ui/img/map/cluster-blue.png', height: 33, width: 33},
				{textColor: 'white', url: '/media/ui/img/map/cluster-blue.png', height: 33, width: 33},
				{textColor: 'white', url: '/media/ui/img/map/cluster-blue.png', height: 33, width: 33}
			]
		};

		clusters.auto = {
			gridSize: 50,
			styles: [
				{textColor: 'white', url: '/media/ui/img/map/cluster-red.png', height: 33, width: 33},
				{textColor: 'white', url: '/media/ui/img/map/cluster-red.png', height: 33, width: 33},
				{textColor: 'white', url: '/media/ui/img/map/cluster-red.png', height: 33, width: 33}
			]
		};

		clusters.collection = {
			gridSize: 50,
			styles: [
				{textColor: 'white', url: '/media/ui/img/map/cluster-yellow.png', height: 33, width: 33},
				{textColor: 'white', url: '/media/ui/img/map/cluster-yellow.png', height: 33, width: 33},
				{textColor: 'white', url: '/media/ui/img/map/cluster-yellow.png', height: 33, width: 33}
			]
		};

		clusters.moto       = new MarkerClusterer($kapmap.map, [], clusters.moto);
		clusters.auto       = new MarkerClusterer($kapmap.map, [], clusters.auto);
		clusters.collection = new MarkerClusterer($kapmap.map, [], clusters.collection);
	}

	// EVENTS ////////////////////////////////////////////////////////////////////////////////////////////////////////////

	$('.allAuto').click(function(){
		categoryCheck('auto');
	});

	$('.allMoto').click(function(){
		categoryCheck('moto');
	});

	$('.allCollection').click(function(){
		categoryCheck('collection');
	});

	$('#btnHome').click(function(){ // center on geoloc

		if (!$kapmap) return false;

		if(navigator.geolocation) {
			isMapLoading(true);
			navigator.geolocation.getCurrentPosition(

				function(pos) { // success
					isMapLoading(false);
					var latlng = new google.maps.LatLng(pos.coords.latitude, pos.coords.longitude);

					// Center la MAP sur la position de l'internaute
					$kapmap.setMapCenter(latlng, 8)

					// Ajouter un marker custom (maison)
					$kapmap.displayCustomMarker(latlng, '/media/ui/img/map/marker-home.png');
				},

				function(err) { // error
					isMapLoading(false);
					console.error('Could not determine geoposition', err);
				});
		} else {
			alert('Désolé, votre navigateur ne permet pas de vous géolocalisaer');
		}
	});

	// DATES //////////////////////////////////////////
	$('#previousWeek').click(function(){
		previousWeek();
		updateWeekUI();
		autoClick();
	});

	$('#nextWeek').click(function(){
		nextWeek();
		updateWeekUI();
		autoClick();
	});

	$('#weekPicker').change(function(){
		setWeekFromDate($(this).val());
	});

	$('.week-num').click(function(){
		setWeekFromDate($(this).attr('data-date'));
	});

	///////////////////////////////////////////////////

	function autoClick(){
		var manif = getManifestations(getSelectedCategories(), getSelectedDep());
		manif.done(function(d){
			injectManifestations(d, true);
		});
	}

	function categoryUncheck(){
		$('#categories input[type="checkbox"]').prop('checked', false);
	}

	function categoryCheck(cat){
		var boxes = $('.is-'+cat+' input[type="checkbox"]')
		if(!boxes.length && boxes.length > 0) return false;

		var v = !boxes.eq(0).prop('checked') == true;

		//categoryUncheck();
		boxes.prop('checked', v);

		autoClick();
	}

	// DATES //////////////////////////////////////////
	function setWeekFromDate(date){
		currentDate = date;
		updateWeekUI();
		autoClick();
	}

	/* Met à jour l'interface de choix des date après un changement de date courante (setWeekFromDate)
	* */
	function updateWeekUI(){

		var datesButtons = $('#dates-buttons')
			, delta = datesButtons.attr('data-delta')
			, last = $('#previousWeek')
			, diff = weekDiff()
			, before
			, after
			, item;

		// Si j'ai 0|1|2 semaine BEFORE=DIFF, si non je force à 2
		before = 2;
		if(diff >= 0 && diff <= 2) before = diff;

		// Trouver le nombre de semaine après la semaine courante
		after = delta - 1 - before;

		// Nettoyer la zone
		$('.week-num').remove();

		//-- -- -- -- -- -- -- -- -- -- -- BEFORE

		for(var i=before; i>0; i--){
			item = weekItem(i*-1);
			item.insertAfter(last);
			last = item;
		}

		//-- -- -- -- -- -- -- -- -- -- -- CURRENT

		item = weekItem(0, true);
		item.insertAfter(last);
		last = item;

		//-- -- -- -- -- -- -- -- -- -- -- AFTER

		for(var i=1; i<=after; i++){
			item = weekItem(i);
			item.insertAfter(last);
			last = item;
		}

		// Mise à jour du menu
		var num = getWeekNumber(currentDate)
			, sel = '#weekPicker option[data-week="'+ num +'"]';

		$(sel).attr('selected','selected');
	}

	/* Retourne un bouton pour la date a jouter a la liste,
	 *
	 * @week: nombre de semaine en plus de la date courante
	 * @sel: boolean (si true, ajoute une class CSS)
	 * */
	function weekItem(week, sel){
		sel = sel || false;

		var date = new Date(currentDate)
			, timestamp = date.getTime() + (7*86400 * 1000 *  week)
			, itemDate = new Date(timestamp)
			, num = getWeekNumber(itemDate)
			, css = (sel) ? 'current' : '';

		/*console.log('Current date', currentDate);
		console.log('Next date', itemDate);
		console.log('Semaine', weekNum);*/

		var year = itemDate.getFullYear()
			, month = (itemDate.getMonth()+1)
			, day = itemDate.getDate()
			, mois = moisList[month-1]
			, btn;

		// Utiliser le jousr + mois à 2 chiffre (dirty)
		if(month < 10) month = '0'+month;
		if(day   < 10) day   = '0'+day;
		if(num < 10)   num   = '0'+num;

		btn = $('<button class="btn week-num '+ css +'" data-date="'+year+'/'+month+'/'+day+'">'+ day+' '+mois +'</button>');

		btn.click(function(){
			setWeekFromDate($(this).attr('data-date'));
		});

		return btn;
	}

	/* Retoure le numéro de la semaine d'après une date (yyyy/mm/dd ou objet js)
	 * */
	function getWeekNumber(d) {
		// Copy date so don't modify original
		d = new Date(d);
		d.setHours(0,0,0);
		// Set to nearest Thursday: current date + 4 - current day number
		// Make Sunday's day number 7
		d.setDate(d.getDate() + 4 - (d.getDay()||7));
		// Get first day of year
		var yearStart = new Date(d.getFullYear(),0,1);
		// Calculate full weeks to nearest Thursday
		var weekNo = Math.ceil(( ( (d - yearStart) / 86400000) + 1)/7)
		// Return array of year and week number
	//	return [d.getFullYear(), weekNo];
		return weekNo;
	}

	/* Determnine la différence entre une date et la date courante (en nombre de semaine)
	* */
	function weekDiff(){
		var tmp = new Date()
			, now = new Date(tmp.getFullYear() +'/'+ (tmp.getMonth()+1) +'/'+ tmp.getDate())
			, cur = new Date(currentDate)
			, diff = now.getTime() - cur.getTime();

		var result = Math.round(diff / weekMS) * -1

//	console.log(diff, 'ms', result, 'semaine');

		return result

	}

	/* La date courante est reculé d'une semaine
	*  Protège le fait de pouvoir remonter plus loin que la semaine en cours
	* */
	function previousWeek(){
		var now  = new Date()
			, date = new Date(currentDate)
			, prev = new Date( date.getTime() - weekMS)

		// Ne pas aller trop loin en arrière
		if(prev.getTime() < now.getTime()) prev = now;

		currentDate = dateFormat(prev);

	//	console.log("La currentDate est maintenant ", currentDate);
	}

	/* La date courante est décalé d'une semaine
	* */
	function nextWeek(){
		var date = new Date(currentDate)
			, next = new Date( date.getTime() + weekMS)

		currentDate = dateFormat(next);

		//console.log("La currentDate est maintenant ", currentDate);
	}

	/* Reformat une date YYYY/MM/DD depuis un objet JS
	 *
	 */
	function dateFormat(d){
		var string = d.getFullYear() +'/';
		string += (     (d.getMonth() < 9) ? '0'+(d.getMonth()+1) : d.getMonth()+1)        +'/';
		string += d.getDate();
		return string;
	}

	// Auto load de la map sur le bureau
	if(!Modernizr.touch) buildMap('map');

});