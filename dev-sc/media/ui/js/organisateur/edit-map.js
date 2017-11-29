var KAPMap = {

	/*Default opt b */
	mapOptions : {},

	map : {}, // map obj
	geocoder : {}, // geocoder obj
	selectors : {
		markerPanel : "#chateau",
		markerSave 	: "#savemarker",
		markerTitle : "#title"
	},

	init : function() {

		this.setMapOptions();
		this.map        = new google.maps.Map(document.getElementById('map'), this.mapOptions);
		this.geocoder   = new google.maps.Geocoder();

		DrawingManager.init();
		DrawingManager.setMap(this.map);

		// si on a des datas, charger une map enregistrée
		var lat = $('input[name="lat"]').val().replace(',', '.');
		var lng = $('input[name="lng"]').val().replace(',', '.');

		if(lat != '' && lng != ''){
			var geoloc = new google.maps.LatLng(lat, lng);
			this.setAddressMarker(geoloc)
			this.setLocation(geoloc);

			this.map.setCenter(geoloc);
		}

		// si on a un zoom level
		var zoom = $('input[name="zoom"]').val();
		if(parseInt(zoom) > 0) this.setZoom(zoom);

		//if (typeof savedPolys === 'object') this.loadMap(savedPolys);
		//if (typeof savedMarkers === 'object') this.loadMap(savedMarkers);
	},

	/*Traduit une adresse en lat/lng, centre la map dessus
	 * @address : str
	 * @callback : str, scope this
	 * */
	getGeoCode : function(address, callback) {

		var that = this;

		this.geocoder.geocode( { 'address': address }, function(results, status) {
			if (status == google.maps.GeocoderStatus.OK) {
				that.map.setCenter(results[0].geometry.location);
				that.setLocation(results[0].geometry.location);
				that[callback](results[0].geometry.location); // yep.
			}else{
				alert('Impossible de géolocaliser cette adresse')
			}
		});
	},

	setMapAddress : function() {

		var zip = $('input[name="zip"]').val()
			, city = $('#city-search').val()
			, pays = $('input[name="pays"]').val();
		//, adresse = $('textarea[name="address"]').val();

		if(!zip || !city){
			alert('Vous devez remplir une adresse complète avant de la localiser sur la carte');
			return;
		}

		this.getGeoCode(zip+' '+city, 'setAddressMarker');
	},

	/**
	 * Callback [setMapAddress]
	 */
	setAddressMarker : function(geoloc) {

		var that    = this;
		var marker  = new google.maps.Marker({
			position:   geoloc,
			map:        that.map,
			title:      'M'
		});

		DrawingManager.clearStore();
		DrawingManager.addPoly(marker, 'marker');
	},

	setZoom : function(zoom) {
		this.map.setOptions({zoom : parseInt(zoom)});
	},

	/*Affiche l'édition des markers;
	 * @show : bool
	 * */
	showMarkerPanel : function(show) {
		if (show) {
			$(this.selectors.markerPanel).show();
			$(this.selectors.markerPanel).stop().animate({
				'height' : '220px'
			}, 118);

			$(this.selectors.markerSave).on('click', $.proxy(function() {
				DrawingManager.addInfoWindow({
					'title' : $(this.selectors.markerTitle).val(),
					'info'  : tinyMCE.activeEditor.getContent()
				});
			}, this ));

		} else {
			$(this.selectors.markerPanel).stop().animate({
				'height' : '0px'
			}, 118, function() {$(this).hide();});
		}
	},

	/*setter this.location */
	setLocation : function(location) {
		this.location = location;
	},

	/*Met en place les opt de la map*/
	setMapOptions : function() {

		var coords = new google.maps.LatLng(46.649436, 2.557751);
		this.mapOptions = {
			center: coords,
			zoom: 5,
			mapTypeId: google.maps.MapTypeId.ROADMAP
		};

		this.setLocation(coords);
	},

	saveMap : function() {

		var build = [];
		// true si on enregistre un région,
		// il faut itérer les polygons derrière pour updater chaque appellation
		var region = 'none';

		for (var i in DrawingManager.store) {

			var poly = {};
			poly.location = {};
			poly.color 	  = DrawingManager.store[i].fillColor;
			poly.kapType  = DrawingManager.store[i].kapType;

			// Si type appellation sauver les polys
			if (DrawingManager.store[i].kapType == 'poly' && myType != 78) {
//				console.log(DrawingManager.store[i])
				var path  = DrawingManager.store[i].getPath();
//				poly.path = google.maps.geometry.encoding.encodePath(path);
				poly.location.lat = path.getAt(0).lat();
				poly.location.lng = path.getAt(0).lng();
				poly.zoom = this.map.zoom;
				poly.id	  = myID;

				// Si modification depuis region
				//if (myType == 83) poly.id_content = DrawingManager.store[i].id_content;

				build.push(poly);
			}
			// Si type chateau sauver les markers
			/*if (DrawingManager.store[i].kapType != 'poly' && myType == 78) {
			 // les markers centrent la map sur eux
			 poly.content 	  = DrawingManager.store[i].kapInfo.content;
			 poly.title 	 	  = DrawingManager.store[i].title;
			 poly.location.lat = DrawingManager.store[i].position.lat();
			 poly.location.lng = DrawingManager.store[i].position.lng();
			 poly.zoom		  = this.map.zoom;
			 build.push(poly);
			 }*/


			/* Sauver la position de la map. Toujours utiliser les accesseurs
			 * plutot que la prop directement; les clés ne sont pas garanties et les
			 * objs sont obfusqués.
			 */

		}

		// Si type region sauver la location & zoom uniquement
		/*if (myType == 83) {
		 region = {};
		 region.lat  = this.location.lat();
		 region.lng  = this.location.lng();
		 region.zoom = this.map.zoom;
		 }*/

		build = JSON.stringify(build);
		region = JSON.stringify(region);

		$.ajax({
			url  : 'data',
			type : 'POST',
			data : {'build' : build, 'id' : myID, 'type' : myType, 'region' : region}
		}).done($.proxy(function(d) {
				$('#saved').fadeTo(218,1);
			}, this ));

	},

	loadMap : function(data) {

		var mapOpt = this.getLoadOptions();
		//	console.log(data);

		// importer
		for (var i = 0; i < data.length; i++) {

			// ajouter un poly
			var kapID = Math.floor(Math.random()*999999);
			var poly =  $.extend({}, { // params étendus par mapOpt
					fillColor: data[i].color,
					fillOpacity: 0.5,
					strokeWeight: 1,
					strokeColor : '#444',
					clickable: true,
					editable: true,
					zIndex: 1
				},
				mapOpt.polyOpt);

			if (data[i].kapType == 'poly') {
				poly.paths  = google.maps.geometry.encoding.decodePath(data[i].path);
				_polygon 	= new google.maps.Polygon(poly); // créer le poly
				_polygon.kapID = kapID;
				_polygon.id_content = data[i].id_content;
				DrawingManager.addPoly(_polygon, data[i].kapType);

			} else
			// ajouter un marker
				if (data[i].kapType == 'marker') {
					poly.title	  = data[i].title;
					poly.clickable= true;
					poly.position = new google.maps.LatLng(data[i].location.lat, data[i].location.lng);
					_polygon 	  = new google.maps.Marker(poly); // créer le marker
					_polygon.kapID = kapID;

					DrawingManager.addPoly(_polygon, data[i].kapType);
					DrawingManager.addInfoWindow({
						'title' : data[i].title,
						'info'  : data[i].content
					});
				}

			_polygon.setMap(this.map); //  l'ajouter a la map
		}

		// Centrer la map sur région, sinon poly[0]
		if (typeof savedLocation !== 'undefined') {
			var coords = new google.maps.LatLng(savedLocation.lat, savedLocation.lng);
			//	console.log(coords)
			this.setZoom(savedLocation.zoom);
			this.setLocation(coords);
			this.map.setCenter(coords);
		} else {
			var coords = new google.maps.LatLng(data[0].location.lat, data[0].location.lng);
			if (typeof data[0].zoom !== 'undefined') {
				this.setZoom(data[0].zoom);
			}
			this.setLocation(coords);
			this.map.setCenter(coords);
		}

	},

	/*Options de la carte qui va etre chargée
	 * return {
	 *     polyOpt : {}
	 * }
	 * */
	getLoadOptions : function() {
	}
};

var DrawingManager = {

	manager : {},               // obj drawing manager
	selectedShape : false,      // poly selectionné dans le manager
	store : {},                 // registre de polys
	polyOpt : {                 // params des polys
		fillColor: '#000',
		fillOpacity: 0.5,
		strokeWeight: 1,
		strokeColor : '#444',
		clickable: true,
		editable: true,
		zIndex: 1
	},

	init : function() {
		this.manager = new google.maps.drawing.DrawingManager({
			drawingMode: this.setDefaultDrawingMode(),
			drawingControl: true,
			drawingControlOptions: {
				position: google.maps.ControlPosition.TOP_CENTER,
				drawingModes: this.setDrawingMode()
			},
			markerOptions: {},

			polygonOptions : this.polyOpt,
			rectangleOptions: this.polyOpt
		});

		// ajoute les listeners du manager
		this.bindDrawEvents();
	},

	/*Renvoie les opt du manager*/
	setDrawingMode : function() {
		return [google.maps.drawing.OverlayType.MARKER];
	},

	setDefaultDrawingMode : function() {
		return google.maps.drawing.OverlayType.MARKER;
	},

	bindDrawEvents : function() {

		// instance
		var that = this;

		google.maps.event.addListener(that.manager, 'overlaycomplete',      function(e) {

			// Gérer un nouveau poly ou rectangle
			if (e.type != google.maps.drawing.OverlayType.MARKER) {
				var newShape = e.overlay;
				// Selectionner le poly que l'on vient d'ajouter
				that.addPoly(newShape, 'poly');
			}

			// Gérer un nouveau marker
			if (e.type == google.maps.drawing.OverlayType.MARKER) {
				var newShape = e.overlay;
				// Selectionner le poly que l'on vient d'ajouter
				that.clearStore();
				that.addPoly(newShape, 'marker');
				KAPMap.showMarkerPanel(true);
			}

		});

		google.maps.event.addListener(that.manager, 'drawingmode_changed',  function() {that.clearSelection()});
		google.maps.event.addListener(KAPMap.map,   'click',                function() {that.clearSelection()});
		google.maps.event.addListener(KAPMap.map,   'zoom_changed',         this.saveZoom);

		google.maps.event.addDomListener(document.getElementById('set-map-adress'), 'click', function() {
			KAPMap.setMapAddress()
		});
	},

	/*Ajouter un poly au manager
	 * @poly : overlay du manager
	 * @ type : str, ajouté a l'obj,
	 * => plus safe que instanceof si changemnet de l'api
	 * */
	addPoly : function(poly, type) {
		var that = this;

		var kapID = Math.floor(Math.random()*999999);
		poly.kapID = kapID;
		poly.kapType = type;

		this.store[kapID] = poly;
		this.manager.setDrawingMode(null); // poly terminé, quitter le mode dessin

		google.maps.event.addListener(poly, 'click', function() {
			that.setSelection(poly);
		});
		google.maps.event.addListener(poly, 'rightclick', function(e) {
			that.deleteVertex(e);
		});

		//this.setSelection(poly);
		// mettre a jour les champs lat lng de la form
		this.updateFormGeoloc(this.store[kapID].getPosition());
	},

	updateFormGeoloc : function(data) {
		$('input[name="lat"]').val(data.lat());
		$('input[name="lng"]').val(data.lng());
	},

	/**
	 * Nettoie tout le store (ex, placer un marker unique sur la map)
	 */
	clearStore : function() {
		//	console.log("clearing")
		for (var i in this.store) {
			//	console.log(this.store[i]);
			this.store[i].setMap(null);
			delete this.store[i];
		}
	},

	deleteVertex : function(mev) {
		if (mev.vertex != null) {
			this.selectedShape.getPath().removeAt(mev.vertex);
		}
	},

	/*Selectionner un poly
	 * @shape : overlay du manager
	 * */
	setSelection : function(shape) {
		this.clearSelection();
		this.selectedShape = shape;
		if (shape.kapType != 'marker') {
			shape.setEditable(true);
		} else
			if (shape.kapType == 'marker') {
				KAPMap.showMarkerPanel(true);
			}
	},

	/*Déselectionner les polys*/
	clearSelection : function() {

		if (typeof this.selectedShape === 'object') {
			if (this.selectedShape.kapType != 'marker') {
				this.selectedShape.setEditable(false);
			} else
				if (this.selectedShape.kapType == 'marker') {
					KAPMap.showMarkerPanel(false);
				}

			this.selectedShape = false;
		}
	},

	/*Supprimer le poly selectedShape*/
	deleteSelectedShape : function() {
		if (typeof this.selectedShape === 'object') {
			this.selectedShape.setMap(null); // supprimer le poly de la map
			delete this.store[this.selectedShape.kapID]; // supprimer le poly du store
		}
	},

	/*Change la couleur de fond des polys
	 * @color : #hex
	 * */
	setFillColor : function(color) {
		var optPoly = this.manager.get('polygonOptions');
		optPoly.fillColor = color;
		var optRect = this.manager.get('rectangleOptions');
		optRect.fillColor = color;
		this.manager.set('rectangleOptions', optRect);
		this.manager.set('polygonOptions', optPoly);

		// change le poly selectionné
		if (typeof this.selectedShape === 'object') {
			this.selectedShape.setOptions({fillColor : color});
		}
	},

	/*Ajoute un tooltip sur un marker
	 * @opt : { 'title' : '', info : '' }
	 * */
	addInfoWindow : function(opt) {

		// Doit mettre a jour les prop du marker pour le title
		this.selectedShape.setOptions({title : opt.title});
		// Doit binder une infoWindow avec le contenu
		this.selectedShape.kapInfo = new google.maps.InfoWindow({
			content : opt.info
		});
		google.maps.event.addListener(this.selectedShape, 'click', $.proxy(function() {
			this.selectedShape.kapInfo.open(KAPMap.map, this.selectedShape);
			tinyMCE.activeEditor.setContent(this.selectedShape.kapInfo.content);
			$(KAPMap.selectors.markerTitle).val(this.selectedShape.title);
		}, this ));

	},

	setMap : function(map) {
		this.manager.setMap(map);
	},

	saveZoom : function(){
		$('input[name="zoom"]').val(KAPMap.map.zoom);
	}

};

$(function() {
	KAPMap.init();
});

