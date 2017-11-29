$(function () {

	"use strict";

	var $kapmap // reference map
		, currentDep // departement courant
		, dateMax // date max
		, dateMin  // date min
		, clusters = {}; // Clusters (contient : {auto}, {moto}, {collection} )


	// Zoom
	$(".fancybox").fancybox();

	// Activer kapmap
	if(!$('#map-detail').length) return;

	$('#map-detail').kapmap({
		'polyStyle': {
			fillColor: 'red',
			fillOpacity: 0.5,
			strokeWeight: 1,
			strokeColor: '#444',
			clickable: false,
			editable: false,
			zIndex: 1
		}
	});

	$kapmap = $('#map-detail').data('kapmap');

	// Activer les clusters
	enableClusterers();


	// Load ALL markers ON START
	getManifestations(null, 0).done(function (d) {
		injectManifestations(d, true);
	});


	/* + + + + + + + + + + + + + + + + + + + + + + + + + + + + + + + + + + + + + + + + + + + + + + + + + + + + + + + +*/
	// FUNCTIONS


	/**
	 * Renvoie les catégories sélectionnées
	 * @returns Array selected
	 */
	function getSelectedCategories() {
		var selected = new Array();
		$('#categories input[data-cat]:checked').each(function () {
			selected.push($(this).attr('data-cat'));
		})
		return selected;
	}

	/**
	 * Injecte les manifs dans la carte, si (true)reset, clean
	 * les précédents markers
	 * @param data [ {manif}, { ... } ]
	 * @param Bool reset
	 */
	function injectManifestations(result, reset) {

		if (reset) {
			$kapmap.clearMarkers(); // clean kapmap markerStore
			clearClusterers(); // clean clusterers
		}

		var me = result.manif;

		// Centrer sur le point et ZOOM
		$kapmap.setMapCenter(
			new google.maps.LatLng(me.gps[0], me.gps[1]),
			13
		);

		// AJOUTER LES AUTRES POINTS AUTOUR ///////////
		var data = result.near;
		for (var i = 0; i < data.length; i++) {

			if (!data[i].gps) continue;

			var marker, icon;

			switch (data[i].type) {
				case 'auto' :       icon = '/media/ui/img/map/marker-red.png';break;
				case 'moto' :			  icon = '/media/ui/img/map/marker-blue.png'; break;
				case 'collection' : icon = '/media/ui/img/map/marker-yellow.png'; break;
				default :           icon = '/media/ui/img/map/marker-red.png';
			}

			marker = $kapmap.displayCustomMarker(  // generate Marker
				data[i].gps,
				icon,
				$kapmap.getInfoWindow(
					'<p class="infotitle">'+data[i].name+'</p>' + '<a href="'+data[i].link+'">En savoir +</a>'
				)
			);

			marker._kapdata = data[i]; // save data in marker

			marker.on('click', function () { // bind click event
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
			//	if (checkok) clusters[marker._kapdata.type].addMarker(marker);
		}

		// AJOUTER LE POINT POUR LA MANIF /////////////
		marker = $kapmap.displayCustomMarker(result.manif.gps, '/media/ui/img/map/marker-grey.png');
		marker._kapdata = me; // save data in marker

	}

	/**
	 * Helper ajax, return promise jqXhr
	 * @returns jqXhr
	 */
	function getManifestations() {
		return $.ajax({
			url: '/map/manifestation',
			dataType: 'json',
			data: { near: $('#map-detail').attr('data-id') }
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
		}
		clusters.auto = {
			gridSize: 50,
			styles: [
				{textColor: 'white', url: '/media/ui/img/map/cluster-red.png', height: 33, width: 33},
				{textColor: 'white', url: '/media/ui/img/map/cluster-red.png', height: 33, width: 33},
				{textColor: 'white', url: '/media/ui/img/map/cluster-red.png', height: 33, width: 33}
			]
		}
		clusters.collection = {
			gridSize: 50,
			styles: [
				{textColor: 'white', url: '/media/ui/img/map/cluster-yellow.png', height: 33, width: 33},
				{textColor: 'white', url: '/media/ui/img/map/cluster-yellow.png', height: 33, width: 33},
				{textColor: 'white', url: '/media/ui/img/map/cluster-yellow.png', height: 33, width: 33}
			]
		}

		clusters.moto = new MarkerClusterer($kapmap.map, [], clusters.moto);
		clusters.auto = new MarkerClusterer($kapmap.map, [], clusters.auto);
		clusters.collection = new MarkerClusterer($kapmap.map, [], clusters.collection);

	}


});