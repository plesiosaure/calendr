var KAPMap = {

	elementID : 'maps',

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
		this.map = new google.maps.Map(document.getElementById(this.elementID), this.mapOptions);
		this.geocoder = new google.maps.Geocoder();

		// init drawingManager
		DrawingManager.init();
		DrawingManager.setMap(this.map);
		this.loadMap({});
	},


	/*Traduit une adresse en lat/lng, centre la map dessus
	 * @address : str
	 * */
	getGeoCode : function(address) {

		var that = this;

		this.geocoder.geocode( { 'address': address }, function(results, status) {
			if (status == google.maps.GeocoderStatus.OK) {
				that.map.setCenter(results[0].geometry.location);d
				that.setLocation(results[0].geometry.location);
			}
		});
	},

	setZoom : function(zoom) {
		this.map.setOptions({zoom : parseInt(zoom)});
	},

	/*setter this.location */
	setLocation : function(location) {
		this.location = location;
	},

	/*Met en place les opt de la map*/
	setMapOptions : function() {

		var coords = new google.maps.LatLng(44.80583, -0.63038);
		this.mapOptions = {
			center: coords,
			zoom: 8,
			mapTypeControl: false,
			scaleControl: false,
			navigationControl: false,
			overviewMapControl: false,
			streetViewControl: false,
			zoomControl: false,
			mapTypeId: google.maps.MapTypeId.ROADMAP
		};

		this.setLocation(coords);
	},

	saveMap : function() {

		var build = [];

		for (var i in DrawingManager.store) {

			var poly = {};
			poly.location = {};
			poly.color 	  = DrawingManager.store[i].fillColor;
			poly.kapType  = DrawingManager.store[i].kapType;

			// Si type appellation sauver les polys
			if (DrawingManager.store[i].kapType == 'poly' && myType == 85) {
				var path  = DrawingManager.store[i].getPath();
				poly.path = google.maps.geometry.encoding.encodePath(path);
				poly.location.lat = path.getAt(0).lat();
				poly.location.lng = path.getAt(0).lng();
				poly.zoom = this.map.zoom;
				build.push(poly);
			}
			// Si type chateau sauver les markers
			if (DrawingManager.store[i].kapType != 'poly' && myType == 78) {
				// les markers centrent la map sur eux
				poly.content 	  = DrawingManager.store[i].kapInfo.content;
				poly.title 	 	  = DrawingManager.store[i].title;
				build.push(poly);
			}


			/* Sauver la position de la map. Toujours utiliser les accesseurs
			 * plutot que la prop directement; les clés ne sont pas garanties et les
			 * objs sont obfusqués.
			 */

		}

		// Si type region sauver la location & zoom uniquement
		if (myType == 83) {
			build = {};
			build.lat  = this.location.lat();
			build.lng  = this.location.lng();
			build.zoom = this.map.zoom;
		}

		build = JSON.stringify(build);

		$.ajax({
			url  : 'data',
			type : 'POST',
			data : {'build' : build, 'id' : myID, 'type' : myType}
		}).done($.proxy(function() {
			$('#saved').fadeTo(218,1);
		}, this ));

	},

	loadMap : function(data) {

		// Load markers
		$.ajax({
			url 	: '/poc/data',
			dataType: 'json'
		}).done($.proxy(function(r) {
			this.loadMarkers(r);
		}, this ));

	},

	/*Options de la carte qui va etre chargée
	 * return {
	 *     polyOpt : {}
	 * }
	 * */
	getLoadOptions : function() {
		return {
			polyOpt : {
				clickable: true,
				editable: false
			}
		}
	},

	loadMarkers : function(r) {

		console.log(r);

		for(var i in r.data) {
			console.log(r.data[i])
			var coords = new google.maps.LatLng(r.data[i].pos[0], r.data[i].pos[1]);
			DrawingManager.addMarker(coords, "hello you", r.data[i]._id)
		}

	},

	saveMarker : function(marker) {

		$.ajax({

			url     : '/poc/data',
			data    : {
				'lat' : marker.position.lat(),
				'lng' : marker.position.lng()
			},
			type    : 'POST',
			dataType: 'json'

		}).done(function(r) {
					console.log(r)
				})
	}

};

var DrawingManager = {

	manager : {}, // obj drawing manager
	polyOpt : { // params des polys
		fillColor: '#000',
		fillOpacity: 0.5,
		strokeWeight: 1,
		strokeColor : '#444',
		clickable: true,
		editable: true,
		zIndex: 1
	},
	selectedShape : false, // poly selectionné dans le manager
	store : [], // registre de polys

	init : function() {

		this.manager = new google.maps.drawing.DrawingManager({
			drawingMode: google.maps.drawing.OverlayType.MARKER,
			drawingControl: true,
			drawingControlOptions: {
				position: google.maps.ControlPosition.TOP_CENTER,
				drawingModes: [google.maps.drawing.OverlayType.MARKER]
			},
			markerOptions: {},

			polygonOptions : this.polyOpt,
			rectangleOptions: this.polyOpt
		});


		this.bindDrawEvents();

	},

	// Dispatche la construction du marker
	addMarker : function(position, title, mongoid) {

		var marker = new google.maps.Marker({
			position: position,
			map: KAPMap.map,
			animation: google.maps.Animation.DROP,
			title: title
		});

		marker.mongoid = mongoid;

		this.bindRemoveMarker(marker);

		/*		var infowindow = new google.maps.InfoWindow({
		 content: '<div style="color:#ad5598; text-transform: uppercase; font-size: 16px;height:20px;">'+title+'</div>'+
		 '<a href="#c_list">'+count+' chateau(x) listés pour cette appellation</a>'
		 });

		 this.addToStore(infowindow);*/

		this.addToStore(marker);
	},

	bindDrawEvents : function() {
		// instance
		var that = this;

		google.maps.event.addListener(that.manager, 'overlaycomplete', function(e) {

			// Gérer un nouveau poly ou rectangle
			if (e.type == google.maps.drawing.OverlayType.MARKER) {
				var newShape = e.overlay;
				// Selectionner le poly que l'on vient d'ajouter

				that.addPoly(newShape, 'marker');

				that.bindRemoveMarker(newShape);

				if (CONSTADD) {
					KAPMap.saveMarker(newShape);
				}
			}
		});

	},

	/*Ajouter un poly au manager
	 * @poly : overlay du manager
	 * @ type : str, ajout� a l'obj,
	 * => plus safe que instanceof si changemnet de l'api
	 * */
	addPoly : function(poly, type) {
		var that = this;

		var kapID = Math.floor(Math.random()*999999);
		poly.kapID = kapID;
		poly.kapType = type;

		this.store[kapID] = poly;
		//this.manager.setDrawingMode(null); // poly termin�, quitter le mode dessin

		google.maps.event.addListener(poly, 'click', function() {
			that.setSelection(poly);
		});
		google.maps.event.addListener(poly, 'rightclick', function(e) {
			that.deleteVertex(e);
		});

		this.setSelection(poly);
	},

	bindRemoveMarker : function(marker) {

		var that = this;

		google.maps.event.addListener(marker, 'click', function() {

			that.selectedShape = marker;

			if (typeof that.selectedShape === 'object') {

				var mongoid = marker.mongoid;

				that.selectedShape.setMap(null); // supprimer le poly de la map
				delete that.store[that.selectedShape.kapID]; // supprimer le poly du store

				$.ajax({
					url     : '/poc/data',
					data    : {'remove' : mongoid},
					type    : 'POST',
					dataType: 'json'

				}).done(function(r) {
							console.log(r);
						});
			}

		});

	},

	/*Selectionner un poly
	 * @shape : overlay du manager
	 * */
	setSelection : function(shape) {

		this.clearSelection();
		this.selectedShape = shape;
		if (shape.kapType != 'marker') {
			shape.setEditable(true);
		}
	},

	addToStore : function(poly) {
		this.store.push(poly);
	},


	/*Déselectionner les polys*/
	clearSelection : function() {

	},

	/*Supprimer le poly selectedShape*/
	deleteSelectedShape : function() {

	},

	/*Change la couleur de fond des polys
	 * @color : #hex
	 * */
	setFillColor : function(color) {

	},



	setMap : function(map) {
		this.manager.setMap(map);
	}
};


/* + - + - + - + - + - + - + - + - + - + - + - + - + - + - + - + - + - + - + - + - + - + - + - + - + - */
/* + - + - + - + - + - + - + - + - + - + - + - + - + - + - + - + - + - + - + - + - + - + - + - + - + - */

$(function() {
	KAPMap.init();
});