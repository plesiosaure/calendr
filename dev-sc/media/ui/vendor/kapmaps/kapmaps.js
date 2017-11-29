"use strict";

/**
 * Kapmaps, jQuery adapter
 * 2013 @bulgrz
 */

var KAPMap = {

	/**
	 * Privates obj
	 */
	currentId   : 1,
	mapOptions  : {},
	map         : {}, // map obj
	geocoder    : {}, // geocoder obj
	polygons    : {}, // polygons store
	manager     : {},
	$map        : {}, // jquery selector
	markerStore : [], // collection de markers
	cluster     : null, // obj clusterer

	/**
	 * default public options
	 */
	opt : {

		disableDefaultUI: true,

		'center' : {
			'lat' : '46.2276380',
			'lng' : '2.2137490',
			'zoom': '5'
		},

		/**
		 * Polygon styles (stroke, color...)
		 */
		'polyStyle' : {
			strokeColor: "#FF0000",
			strokeOpacity: 0.8,
			strokeWeight: 2,
			fillColor: "#FF0000",
			fillOpacity: 0.35
		},

		/**
		 * Clusterer default opt
		 */
		clusterOptions : {gridSize: 50, maxZoom: 14},

		/**
		 * User allowed events
		 */
		'allowSave'     : false,
		'allowGeocode'  : false,

		/**
		 * Callbacks
		 */
		'onInit' : null,

		/** Kewl Stuff */
		'allowProto' : true

	},

	// TODO, killer le drawingManager; doit pouvoir etre init comme un "plugin"
	init : function(settings, element) {

		//this.opt = $.extend(true, this.opt, settings);
		$.extend(true, this.opt, settings);

		this.setMapOptions();

		if (this.opt.allowMarkerProto)

		if (this.opt.zoom) this.mapOptions.zoom = this.opt.zoom; // override zoom

		this.markerStore = new Array();

		google.maps.visualRefresh = true;

		// init on element
		this.map = new google.maps.Map(element, this.mapOptions);
		this.geocoder = new google.maps.Geocoder();

		// Bind listeners
		this.bindMapEvents();

		if (this.opt.disablePrint) {
			google.maps.event.addListenerOnce(this.map, 'idle', $.proxy(function(){
				this.disablePrint();
			}, this ));
		}

		// Proto POLYGON pour pouvoir rapidement récuperer ses bounds
		google.maps.Polygon.prototype.getBounds = function(latLng) {
			var bounds = new google.maps.LatLngBounds();
			var paths = this.getPaths();
			var path;
			for (var p = 0; p < paths.getLength(); p++) {
				path = paths.getAt(p);
				for (var i = 0; i < path.getLength(); i++) {
					bounds.extend(path.getAt(i));
				}
			}
			return bounds;
		}

		// fire init callback
		if (typeof this.opt.onInit === 'function')
			this.opt['onInit'](this);

	},

	/**
	 * Masque les liens cgu pour éviter les misstouch
	 */
	disablePrint: function() {
		setTimeout(function() { $('.gmnoprint').css('display', 'none'); }, 1); // sry google, not my fault
	},

	bindMapEvents : function() {
		// TODO, a séparer du drawingManager
	},

	/**
	 * Helper uniquID
	 * @returns {number}
	 * @private
	 */
	_getNextID: function() {
		this.currentId = this.currentId +1;
		return this.currentId;
	},

	/**
	 * Centre la map et offset le centre en PIXEL
	 * @param latlng
	 * @param offsetx
	 * @param offsety
	 * @param zoom (optional)
	 * http://stackoverflow.com/questions/3473367/how-to-offset-the-center-of-a-google-maps-api-v3-in-pixels
	 */
	setMapCenterOffset: function(latlng, offsetx, offsety, zoom) {

		if (zoom) this.map.setOptions({zoom : parseInt(zoom)});

		var point1 = this.map.getProjection().fromLatLngToPoint(
			(latlng instanceof google.maps.LatLng) ? latlng : this.map.getCenter()
		);
		var point2 = new google.maps.Point(
			( (typeof(offsetx) == 'number' ? offsetx : 0) / Math.pow(2, this.map.getZoom()) ) || 0,
			( (typeof(offsety) == 'number' ? offsety : 0) / Math.pow(2, this.map.getZoom()) ) || 0
		);

		this.map.panTo(this.map.getProjection().fromPointToLatLng(new google.maps.Point(
			point1.x - point2.x,
			point1.y + point2.y
		)));
	},

	/**
	 * Centre la map et zoom. Anime la transition, sauf si opt.noAnimation
	 * @param latlng
	 * @param zoom (optional)
	 * @param opt (optional)
	 */
	setMapCenter: function(latlng, zoom, opt) {

		var opt = opt || {};

		if (!opt.noAnimation) {

			this.map.panTo(latlng);
			//this.isMap

			if (zoom) { // si zoom, l'appliquer a la fin de l'animation
				this._idleEvent = google.maps.event.addListenerOnce(this.map, 'idle', $.proxy(function(){
					this.map.setZoom(zoom);
				}, this ));
			}

		} else {
			this.map.setCenter(latlng);
			if (zoom) this.map.setZoom(zoom)
		}
	},

	clearEvent: function(handler, event) {
		google.maps.event.clearListeners(event, handler);
	},

	// address = {address : str || location: latlng}
	getGeoCode : function(loc, callback) {

		this.geocoder.geocode( loc, $.proxy(function(results, status) {
			if (status == google.maps.GeocoderStatus.OK) {
				callback(results[0]);
			} else {
				console.error('Geocoding error '+status);
			}
		}, this));
	},

	/**
	 * Callback [setMapAddress]
	 */
	setAddressMarker : function(geoloc) {
		var that = this;
		var marker = new google.maps.Marker({
			position: geoloc,
			map: that.map,
			title:""
		});
		this.drawingManager.clearStore();
		this.drawingManager.addPoly(marker, 'marker');
	},

	setZoom : function(zoom) {
		this.map.setOptions({zoom : parseInt(zoom)});
	},

	/*setter this.location */
	setLocation : function(location) {
		this.location = location;
	},

	/**
	 * Met en place les options de la map & centre/zoom sur opt.center,zoom
	 */
	setMapOptions : function() {

		var styles = []; // map styles
		if (this.opt.disablePoi) styles = [ { featureType: "poi", elementType: "labels", stylers: [ { visibility: "off" } ] } ];

		var coords = new google.maps.LatLng(this.opt.center.lat, this.opt.center.lng);
		this.mapOptions = {
			disableDefaultUI: this.opt.disableDefaultUI,
			center: coords,
			zoom: parseInt(this.opt.center.zoom),
			mapTypeId: google.maps.MapTypeId.ROADMAP,
			styles: styles

		};

		this.setLocation(coords);
	},

	// BOUNDS //////////////////////////////////////////////////////////////////////////////////////////////////////////

	getNorthEastBounds: function() {
		return this._getBounds().getNorthEast();
	},

	getSouthWestBounds: function() {
		return this._getBounds().getSouthWest();
	},

	/**
	 * Renvoie les limites de la map affichée a l'écran
	 * @returns LatLngBounds
	 */
	_getBounds: function() {
		this.currentBounds = this.map.getBounds();
		return this.currentBounds;
	},

	// PLACES //////////////////////////////////////////////////////////////////////////////////////////////////////////

	/**
	 * Renvoie les poi dans le radius sur le callback
	 * @param pos latlng
	 * @param callback function
	 * @param (optional) opt placesOptions
	 */
	getNearPlaces: function(pos, callback, opt) {

		if (!this.placesService) {
			this.placesService = new google.maps.places.PlacesService(this.map);
		}

		var placesOptions = {
			location: pos,
			rankBy: google.maps.places.RankBy.DISTANCE,
			//radius: radius || 1000,
			types: opt.types || []
		}

		this.placesService.nearbySearch(placesOptions, callback);
	},

	// MARKERS /////////////////////////////////////////////////////////////////////////////////////////////////////////

	/**
	 * Ajoute un marker simple à la map
	 * Si une infoWindow est fournie, elle est enregistrée dans marker._kapinfo
	 *
	 * @param Array || {google.maps.LatLng} pos
	 * @param String title
	 * @param (optional) {google.maps.InfoWindow}
	 * @returns {google.maps.Marker}
	 */
	displayMarker: function(pos, title, info) {

		if (pos.constructor === Array) // cast if needed
			pos = new google.maps.LatLng(pos[0], pos[1])

		var marker = new google.maps.Marker({
			position: pos,
			map: this.map,
			title: title || ""
		});

		// register infowindow si fournie
		if (info && info.constructor === google.maps.InfoWindow) marker._kapinfo = info;

		// bind methods ( on() )
		this.bind(marker);

		this.markerStore.push(marker);
		return marker;
	},

	hideMarker: function(marker) {
		marker.setVisible(false);
		marker.setMap(null);
		marker = null;
	},

	/**
	 * Affiche un marker avec un label custom
	 * @param position latlng
	 * @param content String
	 * @param icon String || Icon
	 * @param anchor Array label anchor [x, y]
	 */
	displayMarkerWithLabel: function(position, content, icon, anchor) {
		if (!MarkerWithLabel) throw "displayMarkerWithLabel requires the corresponding utility lib.";

		var _anchor;
		(anchor) ? _anchor = new google.maps.Point(anchor[0], anchor[1])
			: _anchor = null;

		var markerOptions = {
			position: position,
			map: this.map,
			icon: icon,
			labelContent: content,
			labelAnchor: _anchor,
			labelClass: "markerlabel"
		};

		return this._returnMarker(new MarkerWithLabel(markerOptions));
	},

	/**
	 * Affiche un marker avec une icone custom
	 * @param position latlng
	 * @param icon String || Icon
	 * @param (optional) opt markerOptions
	 * @returns {google.maps.Marker}
	 */
	displayCustomMarker: function(pos, icon, info) {

		if (pos.constructor === Array) // cast if needed
			pos = new google.maps.LatLng(pos[0], pos[1])

		var markerOptions = {
			position: pos,
			map: this.map,
			icon: icon // str ou obj Icon
		}

		var marker = new google.maps.Marker(markerOptions);

		// register infowindow si fournie
		if (info && info.constructor === google.maps.InfoWindow) marker._kapinfo = info;

		// bind methods ( on() )
		this.bind(marker);
		this.markerStore.push(marker);

		return marker;

	},

	/**
	 * Clusterise les markers si MarkerClusterer présent
	 */
	clusterMarkers: function() {
		if (MarkerClusterer) {
			this.cluster = new MarkerClusterer(this.map, this.markerStore, this.opt.clusterOptions);
		}
	},

	/**
	 * Retourne une Icon, utilisable dans un marker
	 * @param url
	 * @param height (display)
	 * @param width (display)
	 * @param (optional) Array anchor [x, y], default bottom center
	 * @returns Icon
	 */
	getIcon: function(url, height, width, anchor) {

		var _anchor;
		(anchor) ? _anchor = new google.maps.Point(anchor[0], anchor[1])
			: _anchor = null;

		return new google.maps.MarkerImage(
			url,
			null,
			new google.maps.Point(0,0),
			_anchor,
			new google.maps.Size(width, height)
		);

	},

	/**
	 * Stocke et retourne un marker, si opt.noStore retourne sans stockage
	 * @param Marker
	 * @param opt
	 * @returns {*}
	 * @private
	 */
	_returnMarker: function(obj, opt) {

		if (opt && opt.noStore) return obj;
		if (this.opt.noStore) return obj;

		this.markerStore.push(obj);
		return obj;
	},

	clearMarkers: function() {
		for (var i = 0; i < this.markerStore.length; i++) {
			this.markerStore[i].setMap(null);
			this.markerStore.splice(i, 1);
		}
	},

	// DISPLAY /////////////////////////////////////////////////////////////////////////////////////////////////////////

	/**
	 * Retourne une infowindow pour être bindé sur un click event
	 * @param String content
	 * @returns {google.maps.InfoWindow}
	 */
	getInfoWindow: function(content) {
		return new google.maps.InfoWindow({
			content: content
		});
	},

	/**
	 * Ferme toutes les infoWindows, si except fourni,
	 * ne ferme pas celle qui y est rattaché
	 * @param (optional) {google.maps.Marker} except
	 */
	closeInfoWindows: function(except) {
		for (var i = 0; i < this.markerStore.length; i++) {
			if (this.markerStore[i]._kapinfo && this.markerStore[i] !== except) {
				this.markerStore[i]._kapinfo.close();
			}
		}
	},

	/**
	 * Proxy Polygon w/ intelligent guess
	 * Dispatch vers le loader requis
	 * @param data ??
	 * @param (optional) opt Obj
	 */
	loadPolygon: function(data, opt) {

		switch (typeof data) {
			case "object" :
				if (data.constructor === Array) return this._loadPolygonFromArray(data);
				// MOAR ?
				break;
			default :
				return this._loadPolygonFromArray(data);
				break
		}

	},

	/**
	 * Charge et affiche un polygon avec un model type
	 * [ [(float)lat, (float)lng], [ ... ], [ ... ] ]
	 * @param data Array
	 * @param (optional) opt Obj
	 */
	_loadPolygonFromArray: function(data, opt) {

		if (data.constructor !== Array)
			throw "_loadPolygonFromArray :: Data is not an Array";

		var coords = [];
		for (var i = 0; i < data.length; i++) {
			coords.push(    // cast LatLng
				new google.maps.LatLng(data[i][0], data[i][1])
			)
		}

		// Register & display new polygon
		var polyopt = this.opt.polyStyle;
		polyopt.paths = coords;
		var id = this._getNextID();
		var poly = this.polygons[id] = new google.maps.Polygon(polyopt)
		this.lastPoly = id;
		poly.setMap(this.map);

		return poly;
	},

	getLastPolygon: function() {
		return this.polygons[this.lastPoly];
	},

	/**
	 * Clear tous les polygons affichés & stockés
	 * Si REMOVE, detruit également la reference du poly
	 * @param bool remove
	 */
	clearPolygons: function(remove) {
		for (var i in this.polygons) {
			this.polygons[i].setMap(null);
			if (remove) delete this.polygons[i];
		}
	},

	/**
	 * drawCircle puis centre et zoom dessus
	 * @param radius
	 * @param center
	 * @param (optional) opt circleOptions
	 */
	drawCircleAndZoomin: function(radius, center, opt) {

		var circle = this.drawCircle(radius, center, opt)
		this.setMapCenter(center, 7);

		return circle;
	},

	/**
	 * Dessine un cercle de rayon radius a partir de center
	 * @param radius int
	 * @param center latlng
	 * @param (optional) opt circleOptions
	 */
	drawCircle: function(radius, center, opt) {

		var circleOptions = {
			strokeColor: "#78a3d8",
			strokeOpacity: 0.8,
			strokeWeight: 2,
			fillColor: "#78a3d8",
			fillOpacity: 0.35,
			map: this.map,
			center: center,
			radius: radius // radius en metres
		};

		if (opt) circleOptions = $.extend({}, circleOptions, opt);

		return new google.maps.Circle(circleOptions);
	},

	/**
	 * Prototype un item Google
	 * (wrapper on() )
	 */
	bind: function(item) {
		if (typeof item.on !== 'function' && this.opt.allowProto)
			item.on = this.on;
	},

	/**
	 * Event Proxy pour les markers
	 * (eviter d'utiliser la formulation google.maps.event.addListener)
	 * @param String event
	 * @param Function callback
	 */
	on: function(event, callback) {
		google.maps.event.addListener(this, event, callback);
	}
}

if ( typeof Object.create !== 'function') {
	Object.create = function(o) {
		function F() {};
		F.prototype = o;
		return new F();
	};
}


(function($) {

	$.fn.kapmap = function(options) {
		if (this.length) {
			return this.each(function() {
				var mykapmap = Object.create(KAPMap);
				mykapmap.init(options, this);
				$.data(this, 'kapmap', mykapmap);
			});
		}
	};

})(jQuery);