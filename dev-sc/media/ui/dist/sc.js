/* =========================================================
 * bootstrap-datepicker.js 
 * http://www.eyecon.ro/bootstrap-datepicker
 * =========================================================
 * Copyright 2012 Stefan Petre
 *
 * Licensed under the Apache License, Version 2.0 (the "License");
 * you may not use this file except in compliance with the License.
 * You may obtain a copy of the License at
 *
 * http://www.apache.org/licenses/LICENSE-2.0
 *
 * Unless required by applicable law or agreed to in writing, software
 * distributed under the License is distributed on an "AS IS" BASIS,
 * WITHOUT WARRANTIES OR CONDITIONS OF ANY KIND, either express or implied.
 * See the License for the specific language governing permissions and
 * limitations under the License.
 * ========================================================= */
 
!function( $ ) {
	
	// Picker object
	
	var Datepicker = function(element, options){
		this.element = $(element);
		this.format = DPGlobal.parseFormat(options.format||this.element.data('date-format')||'mm/dd/yyyy');
		this.picker = $(DPGlobal.template)
							.appendTo('body')
							.on({
								click: $.proxy(this.click, this)//,
								//mousedown: $.proxy(this.mousedown, this)
							});
		this.isInput = this.element.is('input');
		this.component = this.element.is('.date') ? this.element.find('.add-on') : false;
		
		if (this.isInput) {
			this.element.on({
				focus: $.proxy(this.show, this),
				//blur: $.proxy(this.hide, this),
				keyup: $.proxy(this.update, this)
			});
		} else {
			if (this.component){
				this.component.on('click', $.proxy(this.show, this));
			} else {
				this.element.on('click', $.proxy(this.show, this));
			}
		}
	
		this.minViewMode = options.minViewMode||this.element.data('date-minviewmode')||0;
		if (typeof this.minViewMode === 'string') {
			switch (this.minViewMode) {
				case 'months':
					this.minViewMode = 1;
					break;
				case 'years':
					this.minViewMode = 2;
					break;
				default:
					this.minViewMode = 0;
					break;
			}
		}
		this.viewMode = options.viewMode||this.element.data('date-viewmode')||0;
		if (typeof this.viewMode === 'string') {
			switch (this.viewMode) {
				case 'months':
					this.viewMode = 1;
					break;
				case 'years':
					this.viewMode = 2;
					break;
				default:
					this.viewMode = 0;
					break;
			}
		}
		this.startViewMode = this.viewMode;
		this.weekStart = options.weekStart||this.element.data('date-weekstart')||0;
		this.weekEnd = this.weekStart === 0 ? 6 : this.weekStart - 1;
		this.onRender = options.onRender;
		this.fillDow();
		this.fillMonths();
		this.update();
		this.showMode();
	};
	
	Datepicker.prototype = {
		constructor: Datepicker,
		
		show: function(e) {
			this.picker.show();
			this.height = this.component ? this.component.outerHeight() : this.element.outerHeight();
			this.place();
			$(window).on('resize', $.proxy(this.place, this));
			if (e ) {
				e.stopPropagation();
				e.preventDefault();
			}
			if (!this.isInput) {
			}
			var that = this;
			$(document).on('mousedown', function(ev){
				if ($(ev.target).closest('.datepicker').length == 0) {
					that.hide();
				}
			});
			this.element.trigger({
				type: 'show',
				date: this.date
			});
		},
		
		hide: function(){
			this.picker.hide();
			$(window).off('resize', this.place);
			this.viewMode = this.startViewMode;
			this.showMode();
			if (!this.isInput) {
				$(document).off('mousedown', this.hide);
			}
			//this.set();
			this.element.trigger({
				type: 'hide',
				date: this.date
			});
		},
		
		set: function() {
			var formated = DPGlobal.formatDate(this.date, this.format);
			if (!this.isInput) {
				if (this.component){
					this.element.find('input').prop('value', formated);
				}
				this.element.data('date', formated);
			} else {
				this.element.prop('value', formated);
			}
		},
		
		setValue: function(newDate) {
			if (typeof newDate === 'string') {
				this.date = DPGlobal.parseDate(newDate, this.format);
			} else {
				this.date = new Date(newDate);
			}
			this.set();
			this.viewDate = new Date(this.date.getFullYear(), this.date.getMonth(), 1, 0, 0, 0, 0);
			this.fill();
		},

		place: function(){
			var offset = this.component ? this.component.offset() : this.element.offset();

			if(offset.left + this.picker.width() > $('document').width()){
				offset.left = offset.left - this.picker.width();
			}

			this.picker.css({
				top: offset.top + this.height,
				left: offset.left
			});
		},
		
		update: function(newDate){
			this.date = DPGlobal.parseDate(
				typeof newDate === 'string' ? newDate : (this.isInput ? this.element.prop('value') : this.element.data('date')),
				this.format
			);
			this.viewDate = new Date(this.date.getFullYear(), this.date.getMonth(), 1, 0, 0, 0, 0);
			this.fill();
		},
		
		fillDow: function(){
			var dowCnt = this.weekStart;
			var html = '<tr>';
			while (dowCnt < this.weekStart + 7) {
				html += '<th class="dow">'+DPGlobal.dates.daysMin[(dowCnt++)%7]+'</th>';
			}
			html += '</tr>';
			this.picker.find('.datepicker-days thead').append(html);
		},
		
		fillMonths: function(){
			var html = '';
			var i = 0
			while (i < 12) {
				html += '<span class="month">'+DPGlobal.dates.monthsShort[i++]+'</span>';
			}
			this.picker.find('.datepicker-months td').append(html);
		},
		
		fill: function() {
			var d = new Date(this.viewDate),
				year = d.getFullYear(),
				month = d.getMonth(),
				currentDate = this.date.valueOf();
			this.picker.find('.datepicker-days th:eq(1)')
						.text(DPGlobal.dates.months[month]+' '+year);
			var prevMonth = new Date(year, month-1, 28,0,0,0,0),
				day = DPGlobal.getDaysInMonth(prevMonth.getFullYear(), prevMonth.getMonth());
			prevMonth.setDate(day);
			prevMonth.setDate(day - (prevMonth.getDay() - this.weekStart + 7)%7);
			var nextMonth = new Date(prevMonth);
			nextMonth.setDate(nextMonth.getDate() + 42);
			nextMonth = nextMonth.valueOf();
			var html = [];
			var clsName,
				prevY,
				prevM;
			while(prevMonth.valueOf() < nextMonth) {
				if (prevMonth.getDay() === this.weekStart) {
					html.push('<tr>');
				}
				clsName = this.onRender(prevMonth);
				prevY = prevMonth.getFullYear();
				prevM = prevMonth.getMonth();
				if ((prevM < month &&  prevY === year) ||  prevY < year) {
					clsName += ' old';
				} else if ((prevM > month && prevY === year) || prevY > year) {
					clsName += ' new';
				}
				if (prevMonth.valueOf() === currentDate) {
					clsName += ' active';
				}
				html.push('<td class="day '+clsName+'">'+prevMonth.getDate() + '</td>');
				if (prevMonth.getDay() === this.weekEnd) {
					html.push('</tr>');
				}
				prevMonth.setDate(prevMonth.getDate()+1);
			}
			this.picker.find('.datepicker-days tbody').empty().append(html.join(''));
			var currentYear = this.date.getFullYear();
			
			var months = this.picker.find('.datepicker-months')
						.find('th:eq(1)')
							.text(year)
							.end()
						.find('span').removeClass('active');
			if (currentYear === year) {
				months.eq(this.date.getMonth()).addClass('active');
			}
			
			html = '';
			year = parseInt(year/10, 10) * 10;
			var yearCont = this.picker.find('.datepicker-years')
								.find('th:eq(1)')
									.text(year + '-' + (year + 9))
									.end()
								.find('td');
			year -= 1;
			for (var i = -1; i < 11; i++) {
				html += '<span class="year'+(i === -1 || i === 10 ? ' old' : '')+(currentYear === year ? ' active' : '')+'">'+year+'</span>';
				year += 1;
			}
			yearCont.html(html);
		},
		
		click: function(e) {
			e.stopPropagation();
			e.preventDefault();
			var target = $(e.target).closest('span, td, th');
			if (target.length === 1) {
				switch(target[0].nodeName.toLowerCase()) {
					case 'th':
						switch(target[0].className) {
							case 'switch':
								this.showMode(1);
								break;
							case 'prev':
							case 'next':
								this.viewDate['set'+DPGlobal.modes[this.viewMode].navFnc].call(
									this.viewDate,
									this.viewDate['get'+DPGlobal.modes[this.viewMode].navFnc].call(this.viewDate) + 
									DPGlobal.modes[this.viewMode].navStep * (target[0].className === 'prev' ? -1 : 1)
								);
								this.fill();
								this.set();
								break;
						}
						break;
					case 'span':
						if (target.is('.month')) {
							var month = target.parent().find('span').index(target);
							this.viewDate.setMonth(month);
						} else {
							var year = parseInt(target.text(), 10)||0;
							this.viewDate.setFullYear(year);
						}
						if (this.viewMode !== 0) {
							this.date = new Date(this.viewDate);
							this.element.trigger({
								type: 'changeDate',
								date: this.date,
								viewMode: DPGlobal.modes[this.viewMode].clsName
							});
						}
						this.showMode(-1);
						this.fill();
						this.set();
						break;
					case 'td':
						if (target.is('.day') && !target.is('.disabled')){
							var day = parseInt(target.text(), 10)||1;
							var month = this.viewDate.getMonth();
							if (target.is('.old')) {
								month -= 1;
							} else if (target.is('.new')) {
								month += 1;
							}
							var year = this.viewDate.getFullYear();
							this.date = new Date(year, month, day,0,0,0,0);
							this.viewDate = new Date(year, month, Math.min(28, day),0,0,0,0);
							this.fill();
							this.set();
							this.element.trigger({
								type: 'changeDate',
								date: this.date,
								viewMode: DPGlobal.modes[this.viewMode].clsName
							});
						}
						break;
				}
			}
		},
		
		mousedown: function(e){
			e.stopPropagation();
			e.preventDefault();
		},
		
		showMode: function(dir) {
			if (dir) {
				this.viewMode = Math.max(this.minViewMode, Math.min(2, this.viewMode + dir));
			}
			this.picker.find('>div').hide().filter('.datepicker-'+DPGlobal.modes[this.viewMode].clsName).show();
		}
	};
	
	$.fn.datepicker = function ( option, val ) {
		return this.each(function () {
			var $this = $(this),
				data = $this.data('datepicker'),
				options = typeof option === 'object' && option;
			if (!data) {
				$this.data('datepicker', (data = new Datepicker(this, $.extend({}, $.fn.datepicker.defaults,options))));
			}
			if (typeof option === 'string') data[option](val);
		});
	};

	$.fn.datepicker.defaults = {
		onRender: function(date) {
			return '';
		}
	};
	$.fn.datepicker.Constructor = Datepicker;
	
	var DPGlobal = {
		modes: [
			{
				clsName: 'days',
				navFnc: 'Month',
				navStep: 1
			},
			{
				clsName: 'months',
				navFnc: 'FullYear',
				navStep: 1
			},
			{
				clsName: 'years',
				navFnc: 'FullYear',
				navStep: 10
		}],
		dates:{
			days: ["Dimanche", "Lundi", "Mardi", "Mercredi", "Jeudi", "Vendredi", "Samedi", "Dimanche"],
			daysShort: ["Dim", "Lun", "Mar", "Mer", "Jeu", "Ven", "Sam", "Dim"],
			daysMin: ["Di", "Lu", "Ma", "Me", "Je", "Ve", "Sa", "Di"],
			months: ["Janvier", "Février", "Mars", "Avril", "Mai", "Juin", "Juillet", "Aout", "Septembre", "Octobre", "Novembre", "Décembre"],
			monthsShort: ["Jan", "Fév", "Mar", "Avr", "Mai", "Juin", "Jui.", "Aout", "Sep.", "Oct.", "Nov.", "Déc."]
		},
		isLeapYear: function (year) {
			return (((year % 4 === 0) && (year % 100 !== 0)) || (year % 400 === 0))
		},
		getDaysInMonth: function (year, month) {
			return [31, (DPGlobal.isLeapYear(year) ? 29 : 28), 31, 30, 31, 30, 31, 31, 30, 31, 30, 31][month]
		},
		parseFormat: function(format){
			var separator = format.match(/[.\/\-\s].*?/),
				parts = format.split(/\W+/);
			if (!separator || !parts || parts.length === 0){
				throw new Error("Invalid date format.");
			}
			return {separator: separator, parts: parts};
		},
		parseDate: function(date, format) {
			var parts = date.split(format.separator),
				date = new Date(),
				val;
			date.setHours(0);
			date.setMinutes(0);
			date.setSeconds(0);
			date.setMilliseconds(0);
			if (parts.length === format.parts.length) {
				var year = date.getFullYear(), day = date.getDate(), month = date.getMonth();
				for (var i=0, cnt = format.parts.length; i < cnt; i++) {
					val = parseInt(parts[i], 10)||1;
					switch(format.parts[i]) {
						case 'dd':
						case 'd':
							day = val;
							date.setDate(val);
							break;
						case 'mm':
						case 'm':
							month = val - 1;
							date.setMonth(val - 1);
							break;
						case 'yy':
							year = 2000 + val;
							date.setFullYear(2000 + val);
							break;
						case 'yyyy':
							year = val;
							date.setFullYear(val);
							break;
					}
				}
				date = new Date(year, month, day, 0 ,0 ,0);
			}
			return date;
		},
		formatDate: function(date, format){
			var val = {
				d: date.getDate(),
				m: date.getMonth() + 1,
				yy: date.getFullYear().toString().substring(2),
				yyyy: date.getFullYear()
			};
			val.dd = (val.d < 10 ? '0' : '') + val.d;
			val.mm = (val.m < 10 ? '0' : '') + val.m;
			var date = [];
			for (var i=0, cnt = format.parts.length; i < cnt; i++) {
				date.push(val[format.parts[i]]);
			}
			return date.join(format.separator);
		},
		headTemplate: '<thead>'+
							'<tr>'+
								'<th class="prev">&lsaquo;</th>'+
								'<th colspan="5" class="switch"></th>'+
								'<th class="next">&rsaquo;</th>'+
							'</tr>'+
						'</thead>',
		contTemplate: '<tbody><tr><td colspan="7"></td></tr></tbody>'
	};
	DPGlobal.template = '<div class="datepicker dropdown-menu">'+
							'<div class="datepicker-days">'+
								'<table class=" table-condensed">'+
									DPGlobal.headTemplate+
									'<tbody></tbody>'+
								'</table>'+
							'</div>'+
							'<div class="datepicker-months">'+
								'<table class="table-condensed">'+
									DPGlobal.headTemplate+
									DPGlobal.contTemplate+
								'</table>'+
							'</div>'+
							'<div class="datepicker-years">'+
								'<table class="table-condensed">'+
									DPGlobal.headTemplate+
									DPGlobal.contTemplate+
								'</table>'+
							'</div>'+
						'</div>';

}( window.jQuery );
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
/*jslint browser: true, confusion: true, sloppy: true, vars: true, nomen: false, plusplus: false, indent: 2 */
/*global window,google */

/**
 * @name MarkerClustererPlus for Google Maps V3
 * @version 2.0.9 [February 20, 2012]
 * @author Gary Little
 * @fileoverview
 * The library creates and manages per-zoom-level clusters for large amounts of markers.
 * <p>
 * This is an enhanced V3 implementation of the
 * <a href="http://gmaps-utility-library-dev.googlecode.com/svn/tags/markerclusterer/"
 * >V2 MarkerClusterer</a> by Xiaoxi Wu. It is based on the
 * <a href="http://google-maps-utility-library-v3.googlecode.com/svn/tags/markerclusterer/"
 * >V3 MarkerClusterer</a> port by Luke Mahe. MarkerClustererPlus was created by Gary Little.
 * <p>
 * v2.0 release: MarkerClustererPlus v2.0 is backward compatible with MarkerClusterer v1.0. It
 *  adds support for the <code>ignoreHidden</code>, <code>title</code>, <code>printable</code>,
 *  <code>batchSizeIE</code>, and <code>calculator</code> properties as well as support for
 *  four more events. It also allows greater control over the styling of the text that appears
 *  on the cluster marker. The documentation has been significantly improved and the overall
 *  code has been simplified and polished. Very large numbers of markers can now be managed
 *  without causing Javascript timeout errors on Internet Explorer. Note that the name of the
 *  <code>clusterclick</code> event has been deprecated. The new name is <code>click</code>,
 *  so please change your application code now.
 */

/**
 * Licensed under the Apache License, Version 2.0 (the "License");
 * you may not use this file except in compliance with the License.
 * You may obtain a copy of the License at
 *
 *     http://www.apache.org/licenses/LICENSE-2.0
 *
 * Unless required by applicable law or agreed to in writing, software
 * distributed under the License is distributed on an "AS IS" BASIS,
 * WITHOUT WARRANTIES OR CONDITIONS OF ANY KIND, either express or implied.
 * See the License for the specific language governing permissions and
 * limitations under the License.
 */


/**
 * @name ClusterIconStyle
 * @class This class represents the object for values in the <code>styles</code> array passed
 *  to the {@link MarkerClusterer} constructor. The element in this array that is used to
 *  style the cluster icon is determined by calling the <code>calculator</code> function.
 *
 * @property {string} url The URL of the cluster icon image file. Required.
 * @property {number} height The height (in pixels) of the cluster icon. Required.
 * @property {number} width The width (in pixels) of the cluster icon. Required.
 * @property {Array} [anchor] The anchor position (in pixels) of the label text to be shown on
 *  the cluster icon, relative to the top left corner of the icon.
 *  The format is <code>[yoffset, xoffset]</code>. The <code>yoffset</code> must be positive
 *  and less than <code>height</code> and the <code>xoffset</code> must be positive and less
 *  than <code>width</code>. The default is to anchor the label text so that it is centered
 *  on the icon.
 * @property {Array} [anchorIcon] The anchor position (in pixels) of the cluster icon. This is the
 *  spot on the cluster icon that is to be aligned with the cluster position. The format is
 *  <code>[yoffset, xoffset]</code> where <code>yoffset</code> increases as you go down and
 *  <code>xoffset</code> increases to the right. The default anchor position is the center of the
 *  cluster icon.
 * @property {string} [textColor="black"] The color of the label text shown on the
 *  cluster icon.
 * @property {number} [textSize=11] The size (in pixels) of the label text shown on the
 *  cluster icon.
 * @property {number} [textDecoration="none"] The value of the CSS <code>text-decoration</code>
 *  property for the label text shown on the cluster icon.
 * @property {number} [fontWeight="bold"] The value of the CSS <code>font-weight</code>
 *  property for the label text shown on the cluster icon.
 * @property {number} [fontStyle="normal"] The value of the CSS <code>font-style</code>
 *  property for the label text shown on the cluster icon.
 * @property {number} [fontFamily="Arial,sans-serif"] The value of the CSS <code>font-family</code>
 *  property for the label text shown on the cluster icon.
 * @property {string} [backgroundPosition="0 0"] The position of the cluster icon image
 *  within the image defined by <code>url</code>. The format is <code>"xpos ypos"</code>
 *  (the same format as for the CSS <code>background-position</code> property). You must set
 *  this property appropriately when the image defined by <code>url</code> represents a sprite
 *  containing multiple images.
 */
/**
 * @name ClusterIconInfo
 * @class This class is an object containing general information about a cluster icon. This is
 *  the object that a <code>calculator</code> function returns.
 *
 * @property {string} text The text of the label to be shown on the cluster icon.
 * @property {number} index The index plus 1 of the element in the <code>styles</code>
 *  array to be used to style the cluster icon.
 */
/**
 * A cluster icon.
 *
 * @constructor
 * @extends google.maps.OverlayView
 * @param {Cluster} cluster The cluster with which the icon is to be associated.
 * @param {Array} [styles] An array of {@link ClusterIconStyle} defining the cluster icons
 *  to use for various cluster sizes.
 * @private
 */
function ClusterIcon(cluster, styles) {
	cluster.getMarkerClusterer().extend(ClusterIcon, google.maps.OverlayView);

	this.cluster_ = cluster;
	this.styles_ = styles;
	this.center_ = null;
	this.div_ = null;
	this.sums_ = null;
	this.visible_ = false;

	this.setMap(cluster.getMap()); // Note: this causes onAdd to be called
}


/**
 * Adds the icon to the DOM.
 */
ClusterIcon.prototype.onAdd = function () {
	var cClusterIcon = this;
	var cMouseDownInCluster;
	var cDraggingMapByCluster;

	this.div_ = document.createElement("div");
	if (this.visible_) {
		this.show();
	}

	this.getPanes().overlayMouseTarget.appendChild(this.div_);

	// Fix for Issue 157
	google.maps.event.addListener(this.getMap(), "bounds_changed", function () {
		cDraggingMapByCluster = cMouseDownInCluster;
	});

	google.maps.event.addDomListener(this.div_, "mousedown", function () {
		cMouseDownInCluster = true;
		cDraggingMapByCluster = false;
	});

	google.maps.event.addDomListener(this.div_, "click", function (e) {
		cMouseDownInCluster = false;
		if (!cDraggingMapByCluster) {
			var mz;
			var mc = cClusterIcon.cluster_.getMarkerClusterer();
			/**
			 * This event is fired when a cluster marker is clicked.
			 * @name MarkerClusterer#click
			 * @param {Cluster} c The cluster that was clicked.
			 * @event
			 */
			google.maps.event.trigger(mc, "click", cClusterIcon.cluster_);
			google.maps.event.trigger(mc, "clusterclick", cClusterIcon.cluster_); // deprecated name

			// The default click handler follows. Disable it by setting
			// the zoomOnClick property to false.
			if (mc.getZoomOnClick()) {
				// Zoom into the cluster.
				mz = mc.getMaxZoom();
				mc.getMap().fitBounds(cClusterIcon.cluster_.getBounds());
				// Don't zoom beyond the max zoom level
				if (mz !== null && (mc.getMap().getZoom() > mz)) {
					mc.getMap().setZoom(mz + 1);
				}
			}

			// Prevent event propagation to the map:
			e.cancelBubble = true;
			if (e.stopPropagation) {
				e.stopPropagation();
			}
		}
	});

	google.maps.event.addDomListener(this.div_, "mouseover", function () {
		var mc = cClusterIcon.cluster_.getMarkerClusterer();
		/**
		 * This event is fired when the mouse moves over a cluster marker.
		 * @name MarkerClusterer#mouseover
		 * @param {Cluster} c The cluster that the mouse moved over.
		 * @event
		 */
		google.maps.event.trigger(mc, "mouseover", cClusterIcon.cluster_);
	});

	google.maps.event.addDomListener(this.div_, "mouseout", function () {
		var mc = cClusterIcon.cluster_.getMarkerClusterer();
		/**
		 * This event is fired when the mouse moves out of a cluster marker.
		 * @name MarkerClusterer#mouseout
		 * @param {Cluster} c The cluster that the mouse moved out of.
		 * @event
		 */
		google.maps.event.trigger(mc, "mouseout", cClusterIcon.cluster_);
	});
};


/**
 * Removes the icon from the DOM.
 */
ClusterIcon.prototype.onRemove = function () {
	if (this.div_ && this.div_.parentNode) {
		this.hide();
		google.maps.event.clearInstanceListeners(this.div_);
		this.div_.parentNode.removeChild(this.div_);
		this.div_ = null;
	}
};


/**
 * Draws the icon.
 */
ClusterIcon.prototype.draw = function () {
	if (this.visible_) {
		var pos = this.getPosFromLatLng_(this.center_);
		this.div_.style.top = pos.y + "px";
		this.div_.style.left = pos.x + "px";
	}
};


/**
 * Hides the icon.
 */
ClusterIcon.prototype.hide = function () {
	if (this.div_) {
		this.div_.style.display = "none";
	}
	this.visible_ = false;
};


/**
 * Positions and shows the icon.
 */
ClusterIcon.prototype.show = function () {
	if (this.div_) {
		var pos = this.getPosFromLatLng_(this.center_);
		this.div_.style.cssText = this.createCss(pos);
		if (this.cluster_.printable_) {
			// (Would like to use "width: inherit;" below, but doesn't work with MSIE)
			this.div_.innerHTML = "<img src='" + this.url_ + "'><div style='position: absolute; top: 0px; left: 0px; width: " + this.width_ + "px;'>" + this.sums_.text + "</div>";
		} else {
			this.div_.innerHTML = this.sums_.text;
		}
		this.div_.title = this.cluster_.getMarkerClusterer().getTitle();
		this.div_.style.display = "";
	}
	this.visible_ = true;
};


/**
 * Sets the icon styles to the appropriate element in the styles array.
 *
 * @param {ClusterIconInfo} sums The icon label text and styles index.
 */
ClusterIcon.prototype.useStyle = function (sums) {
	this.sums_ = sums;
	var index = Math.max(0, sums.index - 1);
	index = Math.min(this.styles_.length - 1, index);
	var style = this.styles_[index];
	this.url_ = style.url;
	this.height_ = style.height;
	this.width_ = style.width;
	this.anchor_ = style.anchor;
	this.anchorIcon_ = style.anchorIcon || [parseInt(this.height_ / 2, 10), parseInt(this.width_ / 2, 10)];
	this.textColor_ = style.textColor || "black";
	this.textSize_ = style.textSize || 11;
	this.textDecoration_ = style.textDecoration || "none";
	this.fontWeight_ = style.fontWeight || "bold";
	this.fontStyle_ = style.fontStyle || "normal";
	this.fontFamily_ = style.fontFamily || "Arial,sans-serif";
	this.backgroundPosition_ = style.backgroundPosition || "0 0";
};


/**
 * Sets the position at which to center the icon.
 *
 * @param {google.maps.LatLng} center The latlng to set as the center.
 */
ClusterIcon.prototype.setCenter = function (center) {
	this.center_ = center;
};


/**
 * Creates the cssText style parameter based on the position of the icon.
 *
 * @param {google.maps.Point} pos The position of the icon.
 * @return {string} The CSS style text.
 */
ClusterIcon.prototype.createCss = function (pos) {
	var style = [];
	if (!this.cluster_.printable_) {
		style.push('background-image:url(' + this.url_ + ');');
		style.push('background-position:' + this.backgroundPosition_ + ';');
	}

	if (typeof this.anchor_ === 'object') {
		if (typeof this.anchor_[0] === 'number' && this.anchor_[0] > 0 &&
			this.anchor_[0] < this.height_) {
			style.push('height:' + (this.height_ - this.anchor_[0]) +
				'px; padding-top:' + this.anchor_[0] + 'px;');
		} else {
			style.push('height:' + this.height_ + 'px; line-height:' + this.height_ +
				'px;');
		}
		if (typeof this.anchor_[1] === 'number' && this.anchor_[1] > 0 &&
			this.anchor_[1] < this.width_) {
			style.push('width:' + (this.width_ - this.anchor_[1]) +
				'px; padding-left:' + this.anchor_[1] + 'px;');
		} else {
			style.push('width:' + this.width_ + 'px; text-align:center;');
		}
	} else {
		style.push('height:' + this.height_ + 'px; line-height:' +
			this.height_ + 'px; width:' + this.width_ + 'px; text-align:center;');
	}

	style.push('cursor:pointer; top:' + pos.y + 'px; left:' +
		pos.x + 'px; color:' + this.textColor_ + '; position:absolute; font-size:' +
		this.textSize_ + 'px; font-family:' + this.fontFamily_ + '; font-weight:' +
		this.fontWeight_ + '; font-style:' + this.fontStyle_ + '; text-decoration:' +
		this.textDecoration_ + ';');

	return style.join("");
};


/**
 * Returns the position at which to place the DIV depending on the latlng.
 *
 * @param {google.maps.LatLng} latlng The position in latlng.
 * @return {google.maps.Point} The position in pixels.
 */
ClusterIcon.prototype.getPosFromLatLng_ = function (latlng) {
	var pos = this.getProjection().fromLatLngToDivPixel(latlng);
	pos.x -= this.anchorIcon_[1];
	pos.y -= this.anchorIcon_[0];
	return pos;
};


/**
 * Creates a single cluster that manages a group of proximate markers.
 *  Used internally, do not call this constructor directly.
 * @constructor
 * @param {MarkerClusterer} mc The <code>MarkerClusterer</code> object with which this
 *  cluster is associated.
 */
function Cluster(mc) {
	this.markerClusterer_ = mc;
	this.map_ = mc.getMap();
	this.gridSize_ = mc.getGridSize();
	this.minClusterSize_ = mc.getMinimumClusterSize();
	this.averageCenter_ = mc.getAverageCenter();
	this.printable_ = mc.getPrintable();
	this.markers_ = [];
	this.center_ = null;
	this.bounds_ = null;
	this.clusterIcon_ = new ClusterIcon(this, mc.getStyles());
}


/**
 * Returns the number of markers managed by the cluster. You can call this from
 * a <code>click</code>, <code>mouseover</code>, or <code>mouseout</code> event handler
 * for the <code>MarkerClusterer</code> object.
 *
 * @return {number} The number of markers in the cluster.
 */
Cluster.prototype.getSize = function () {
	return this.markers_.length;
};


/**
 * Returns the array of markers managed by the cluster. You can call this from
 * a <code>click</code>, <code>mouseover</code>, or <code>mouseout</code> event handler
 * for the <code>MarkerClusterer</code> object.
 *
 * @return {Array} The array of markers in the cluster.
 */
Cluster.prototype.getMarkers = function () {
	return this.markers_;
};


/**
 * Returns the center of the cluster. You can call this from
 * a <code>click</code>, <code>mouseover</code>, or <code>mouseout</code> event handler
 * for the <code>MarkerClusterer</code> object.
 *
 * @return {google.maps.LatLng} The center of the cluster.
 */
Cluster.prototype.getCenter = function () {
	return this.center_;
};


/**
 * Returns the map with which the cluster is associated.
 *
 * @return {google.maps.Map} The map.
 * @ignore
 */
Cluster.prototype.getMap = function () {
	return this.map_;
};


/**
 * Returns the <code>MarkerClusterer</code> object with which the cluster is associated.
 *
 * @return {MarkerClusterer} The associated marker clusterer.
 * @ignore
 */
Cluster.prototype.getMarkerClusterer = function () {
	return this.markerClusterer_;
};


/**
 * Returns the bounds of the cluster.
 *
 * @return {google.maps.LatLngBounds} the cluster bounds.
 * @ignore
 */
Cluster.prototype.getBounds = function () {
	var i;
	var bounds = new google.maps.LatLngBounds(this.center_, this.center_);
	var markers = this.getMarkers();
	for (i = 0; i < markers.length; i++) {
		bounds.extend(markers[i].getPosition());
	}
	return bounds;
};


/**
 * Removes the cluster from the map.
 *
 * @ignore
 */
Cluster.prototype.remove = function () {
	this.clusterIcon_.setMap(null);
	this.markers_ = [];
	delete this.markers_;
};


/**
 * Adds a marker to the cluster.
 *
 * @param {google.maps.Marker} marker The marker to be added.
 * @return {boolean} True if the marker was added.
 * @ignore
 */
Cluster.prototype.addMarker = function (marker) {
	var i;
	var mCount;
	var mz;

	if (this.isMarkerAlreadyAdded_(marker)) {
		return false;
	}

	if (!this.center_) {
		this.center_ = marker.getPosition();
		this.calculateBounds_();
	} else {
		if (this.averageCenter_) {
			var l = this.markers_.length + 1;
			var lat = (this.center_.lat() * (l - 1) + marker.getPosition().lat()) / l;
			var lng = (this.center_.lng() * (l - 1) + marker.getPosition().lng()) / l;
			this.center_ = new google.maps.LatLng(lat, lng);
			this.calculateBounds_();
		}
	}

	marker.isAdded = true;
	this.markers_.push(marker);

	mCount = this.markers_.length;
	mz = this.markerClusterer_.getMaxZoom();
	if (mz !== null && this.map_.getZoom() > mz) {
		// Zoomed in past max zoom, so show the marker.
		if (marker.getMap() !== this.map_) {
			marker.setMap(this.map_);
		}
	} else if (mCount < this.minClusterSize_) {
		// Min cluster size not reached so show the marker.
		if (marker.getMap() !== this.map_) {
			marker.setMap(this.map_);
		}
	} else if (mCount === this.minClusterSize_) {
		// Hide the markers that were showing.
		for (i = 0; i < mCount; i++) {
			this.markers_[i].setMap(null);
		}
	} else {
		marker.setMap(null);
	}

	this.updateIcon_();
	return true;
};


/**
 * Determines if a marker lies within the cluster's bounds.
 *
 * @param {google.maps.Marker} marker The marker to check.
 * @return {boolean} True if the marker lies in the bounds.
 * @ignore
 */
Cluster.prototype.isMarkerInClusterBounds = function (marker) {
	return this.bounds_.contains(marker.getPosition());
};


/**
 * Calculates the extended bounds of the cluster with the grid.
 */
Cluster.prototype.calculateBounds_ = function () {
	var bounds = new google.maps.LatLngBounds(this.center_, this.center_);
	this.bounds_ = this.markerClusterer_.getExtendedBounds(bounds);
};


/**
 * Updates the cluster icon.
 */
Cluster.prototype.updateIcon_ = function () {
	var mCount = this.markers_.length;
	var mz = this.markerClusterer_.getMaxZoom();

	if (mz !== null && this.map_.getZoom() > mz) {
		this.clusterIcon_.hide();
		return;
	}

	if (mCount < this.minClusterSize_) {
		// Min cluster size not yet reached.
		this.clusterIcon_.hide();
		return;
	}

	var numStyles = this.markerClusterer_.getStyles().length;
	var sums = this.markerClusterer_.getCalculator()(this.markers_, numStyles);
	this.clusterIcon_.setCenter(this.center_);
	this.clusterIcon_.useStyle(sums);
	this.clusterIcon_.show();
};


/**
 * Determines if a marker has already been added to the cluster.
 *
 * @param {google.maps.Marker} marker The marker to check.
 * @return {boolean} True if the marker has already been added.
 */
Cluster.prototype.isMarkerAlreadyAdded_ = function (marker) {
	var i;
	if (this.markers_.indexOf) {
		return this.markers_.indexOf(marker) !== -1;
	} else {
		for (i = 0; i < this.markers_.length; i++) {
			if (marker === this.markers_[i]) {
				return true;
			}
		}
	}
	return false;
};


/**
 * @name MarkerClustererOptions
 * @class This class represents the optional parameter passed to
 *  the {@link MarkerClusterer} constructor.
 * @property {number} [gridSize=60] The grid size of a cluster in pixels. The grid is a square.
 * @property {number} [maxZoom=null] The maximum zoom level at which clustering is enabled or
 *  <code>null</code> if clustering is to be enabled at all zoom levels.
 * @property {boolean} [zoomOnClick=true] Whether to zoom the map when a cluster marker is
 *  clicked. You may want to set this to <code>false</code> if you have installed a handler
 *  for the <code>click</code> event and it deals with zooming on its own.
 * @property {boolean} [averageCenter=false] Whether the position of a cluster marker should be
 *  the average position of all markers in the cluster. If set to <code>false</code>, the
 *  cluster marker is positioned at the location of the first marker added to the cluster.
 * @property {number} [minimumClusterSize=2] The minimum number of markers needed in a cluster
 *  before the markers are hidden and a cluster marker appears.
 * @property {boolean} [ignoreHidden=false] Whether to ignore hidden markers in clusters. You
 *  may want to set this to <code>true</code> to ensure that hidden markers are not included
 *  in the marker count that appears on a cluster marker (this count is the value of the
 *  <code>text</code> property of the result returned by the default <code>calculator</code>).
 *  If set to <code>true</code> and you change the visibility of a marker being clustered, be
 *  sure to also call <code>MarkerClusterer.repaint()</code>.
 * @property {boolean} [printable=false] Whether to make the cluster icons printable. Do not
 *  set to <code>true</code> if the <code>url</code> fields in the <code>styles</code> array
 *  refer to image sprite files.
 * @property {string} [title=""] The tooltip to display when the mouse moves over a cluster
 *  marker.
 * @property {function} [calculator=MarkerClusterer.CALCULATOR] The function used to determine
 *  the text to be displayed on a cluster marker and the index indicating which style to use
 *  for the cluster marker. The input parameters for the function are (1) the array of markers
 *  represented by a cluster marker and (2) the number of cluster icon styles. It returns a
 *  {@link ClusterIconInfo} object. The default <code>calculator</code> returns a
 *  <code>text</code> property which is the number of markers in the cluster and an
 *  <code>index</code> property which is one higher than the lowest integer such that
 *  <code>10^i</code> exceeds the number of markers in the cluster, or the size of the styles
 *  array, whichever is less. The <code>styles</code> array element used has an index of
 *  <code>index</code> minus 1. For example, the default <code>calculator</code> returns a
 *  <code>text</code> value of <code>"125"</code> and an <code>index</code> of <code>3</code>
 *  for a cluster icon representing 125 markers so the element used in the <code>styles</code>
 *  array is <code>2</code>.
 * @property {Array} [styles] An array of {@link ClusterIconStyle} elements defining the styles
 *  of the cluster markers to be used. The element to be used to style a given cluster marker
 *  is determined by the function defined by the <code>calculator</code> property.
 *  The default is an array of {@link ClusterIconStyle} elements whose properties are derived
 *  from the values for <code>imagePath</code>, <code>imageExtension</code>, and
 *  <code>imageSizes</code>.
 * @property {number} [batchSize=MarkerClusterer.BATCH_SIZE] Set this property to the
 *  number of markers to be processed in a single batch when using a browser other than
 *  Internet Explorer (for Internet Explorer, use the batchSizeIE property instead).
 * @property {number} [batchSizeIE=MarkerClusterer.BATCH_SIZE_IE] When Internet Explorer is
 *  being used, markers are processed in several batches with a small delay inserted between
 *  each batch in an attempt to avoid Javascript timeout errors. Set this property to the
 *  number of markers to be processed in a single batch; select as high a number as you can
 *  without causing a timeout error in the browser. This number might need to be as low as 100
 *  if 15,000 markers are being managed, for example.
 * @property {string} [imagePath=MarkerClusterer.IMAGE_PATH]
 *  The full URL of the root name of the group of image files to use for cluster icons.
 *  The complete file name is of the form <code>imagePath</code>n.<code>imageExtension</code>
 *  where n is the image file number (1, 2, etc.).
 * @property {string} [imageExtension=MarkerClusterer.IMAGE_EXTENSION]
 *  The extension name for the cluster icon image files (e.g., <code>"png"</code> or
 *  <code>"jpg"</code>).
 * @property {Array} [imageSizes=MarkerClusterer.IMAGE_SIZES]
 *  An array of numbers containing the widths of the group of
 *  <code>imagePath</code>n.<code>imageExtension</code> image files.
 *  (The images are assumed to be square.)
 */
/**
 * Creates a MarkerClusterer object with the options specified in {@link MarkerClustererOptions}.
 * @constructor
 * @extends google.maps.OverlayView
 * @param {google.maps.Map} map The Google map to attach to.
 * @param {Array.<google.maps.Marker>} [opt_markers] The markers to be added to the cluster.
 * @param {MarkerClustererOptions} [opt_options] The optional parameters.
 */
function MarkerClusterer(map, opt_markers, opt_options) {
	// MarkerClusterer implements google.maps.OverlayView interface. We use the
	// extend function to extend MarkerClusterer with google.maps.OverlayView
	// because it might not always be available when the code is defined so we
	// look for it at the last possible moment. If it doesn't exist now then
	// there is no point going ahead :)
	this.extend(MarkerClusterer, google.maps.OverlayView);

	opt_markers = opt_markers || [];
	opt_options = opt_options || {};

	this.markers_ = [];
	this.clusters_ = [];
	this.listeners_ = [];
	this.activeMap_ = null;
	this.ready_ = false;

	this.gridSize_ = opt_options.gridSize || 60;
	this.minClusterSize_ = opt_options.minimumClusterSize || 2;
	this.maxZoom_ = opt_options.maxZoom || null;
	this.styles_ = opt_options.styles || [];
	this.title_ = opt_options.title || "";
	this.zoomOnClick_ = true;
	if (opt_options.zoomOnClick !== undefined) {
		this.zoomOnClick_ = opt_options.zoomOnClick;
	}
	this.averageCenter_ = false;
	if (opt_options.averageCenter !== undefined) {
		this.averageCenter_ = opt_options.averageCenter;
	}
	this.ignoreHidden_ = false;
	if (opt_options.ignoreHidden !== undefined) {
		this.ignoreHidden_ = opt_options.ignoreHidden;
	}
	this.printable_ = false;
	if (opt_options.printable !== undefined) {
		this.printable_ = opt_options.printable;
	}
	this.imagePath_ = opt_options.imagePath || MarkerClusterer.IMAGE_PATH;
	this.imageExtension_ = opt_options.imageExtension || MarkerClusterer.IMAGE_EXTENSION;
	this.imageSizes_ = opt_options.imageSizes || MarkerClusterer.IMAGE_SIZES;
	this.calculator_ = opt_options.calculator || MarkerClusterer.CALCULATOR;
	this.batchSize_ = opt_options.batchSize || MarkerClusterer.BATCH_SIZE;
	this.batchSizeIE_ = opt_options.batchSizeIE || MarkerClusterer.BATCH_SIZE_IE;

	if (navigator.userAgent.toLowerCase().indexOf("msie") !== -1) {
		// Try to avoid IE timeout when processing a huge number of markers:
		this.batchSize_ = this.batchSizeIE_;
	}

	this.setupStyles_();

	this.addMarkers(opt_markers, true);
	this.setMap(map); // Note: this causes onAdd to be called
}


/**
 * Implementation of the onAdd interface method.
 * @ignore
 */
MarkerClusterer.prototype.onAdd = function () {
	var cMarkerClusterer = this;

	this.activeMap_ = this.getMap();
	this.ready_ = true;

	this.repaint();

	// Add the map event listeners
	this.listeners_ = [
		google.maps.event.addListener(this.getMap(), "zoom_changed", function () {
			cMarkerClusterer.resetViewport_(false);
			// Workaround for this Google bug: when map is at level 0 and "-" of
			// zoom slider is clicked, a "zoom_changed" event is fired even though
			// the map doesn't zoom out any further. In this situation, no "idle"
			// event is triggered so the cluster markers that have been removed
			// do not get redrawn.
			if (this.getZoom() === 0) {
				google.maps.event.trigger(this, "idle");
			}
		}),
		google.maps.event.addListener(this.getMap(), "idle", function () {
			cMarkerClusterer.redraw_();
		})
	];
};


/**
 * Implementation of the onRemove interface method.
 * Removes map event listeners and all cluster icons from the DOM.
 * All managed markers are also put back on the map.
 * @ignore
 */
MarkerClusterer.prototype.onRemove = function () {
	var i;

	// Put all the managed markers back on the map:
	for (i = 0; i < this.markers_.length; i++) {
		this.markers_[i].setMap(this.activeMap_);
	}

	// Remove all clusters:
	for (i = 0; i < this.clusters_.length; i++) {
		this.clusters_[i].remove();
	}
	this.clusters_ = [];

	// Remove map event listeners:
	for (i = 0; i < this.listeners_.length; i++) {
		google.maps.event.removeListener(this.listeners_[i]);
	}
	this.listeners_ = [];

	this.activeMap_ = null;
	this.ready_ = false;
};


/**
 * Implementation of the draw interface method.
 * @ignore
 */
MarkerClusterer.prototype.draw = function () {};


/**
 * Sets up the styles object.
 */
MarkerClusterer.prototype.setupStyles_ = function () {
	var i, size;
	if (this.styles_.length > 0) {
		return;
	}

	for (i = 0; i < this.imageSizes_.length; i++) {
		size = this.imageSizes_[i];
		this.styles_.push({
			url: this.imagePath_ + (i + 1) + "." + this.imageExtension_,
			height: size,
			width: size
		});
	}
};


/**
 *  Fits the map to the bounds of the markers managed by the clusterer.
 */
MarkerClusterer.prototype.fitMapToMarkers = function () {
	var i;
	var markers = this.getMarkers();
	var bounds = new google.maps.LatLngBounds();
	for (i = 0; i < markers.length; i++) {
		bounds.extend(markers[i].getPosition());
	}

	this.getMap().fitBounds(bounds);
};


/**
 * Returns the value of the <code>gridSize</code> property.
 *
 * @return {number} The grid size.
 */
MarkerClusterer.prototype.getGridSize = function () {
	return this.gridSize_;
};


/**
 * Sets the value of the <code>gridSize</code> property.
 *
 * @param {number} gridSize The grid size.
 */
MarkerClusterer.prototype.setGridSize = function (gridSize) {
	this.gridSize_ = gridSize;
};


/**
 * Returns the value of the <code>minimumClusterSize</code> property.
 *
 * @return {number} The minimum cluster size.
 */
MarkerClusterer.prototype.getMinimumClusterSize = function () {
	return this.minClusterSize_;
};

/**
 * Sets the value of the <code>minimumClusterSize</code> property.
 *
 * @param {number} minimumClusterSize The minimum cluster size.
 */
MarkerClusterer.prototype.setMinimumClusterSize = function (minimumClusterSize) {
	this.minClusterSize_ = minimumClusterSize;
};


/**
 *  Returns the value of the <code>maxZoom</code> property.
 *
 *  @return {number} The maximum zoom level.
 */
MarkerClusterer.prototype.getMaxZoom = function () {
	return this.maxZoom_;
};


/**
 *  Sets the value of the <code>maxZoom</code> property.
 *
 *  @param {number} maxZoom The maximum zoom level.
 */
MarkerClusterer.prototype.setMaxZoom = function (maxZoom) {
	this.maxZoom_ = maxZoom;
};


/**
 *  Returns the value of the <code>styles</code> property.
 *
 *  @return {Array} The array of styles defining the cluster markers to be used.
 */
MarkerClusterer.prototype.getStyles = function () {
	return this.styles_;
};


/**
 *  Sets the value of the <code>styles</code> property.
 *
 *  @param {Array.<ClusterIconStyle>} styles The array of styles to use.
 */
MarkerClusterer.prototype.setStyles = function (styles) {
	this.styles_ = styles;
};


/**
 * Returns the value of the <code>title</code> property.
 *
 * @return {string} The content of the title text.
 */
MarkerClusterer.prototype.getTitle = function () {
	return this.title_;
};


/**
 *  Sets the value of the <code>title</code> property.
 *
 *  @param {string} title The value of the title property.
 */
MarkerClusterer.prototype.setTitle = function (title) {
	this.title_ = title;
};


/**
 * Returns the value of the <code>zoomOnClick</code> property.
 *
 * @return {boolean} True if zoomOnClick property is set.
 */
MarkerClusterer.prototype.getZoomOnClick = function () {
	return this.zoomOnClick_;
};


/**
 *  Sets the value of the <code>zoomOnClick</code> property.
 *
 *  @param {boolean} zoomOnClick The value of the zoomOnClick property.
 */
MarkerClusterer.prototype.setZoomOnClick = function (zoomOnClick) {
	this.zoomOnClick_ = zoomOnClick;
};


/**
 * Returns the value of the <code>averageCenter</code> property.
 *
 * @return {boolean} True if averageCenter property is set.
 */
MarkerClusterer.prototype.getAverageCenter = function () {
	return this.averageCenter_;
};


/**
 *  Sets the value of the <code>averageCenter</code> property.
 *
 *  @param {boolean} averageCenter The value of the averageCenter property.
 */
MarkerClusterer.prototype.setAverageCenter = function (averageCenter) {
	this.averageCenter_ = averageCenter;
};


/**
 * Returns the value of the <code>ignoreHidden</code> property.
 *
 * @return {boolean} True if ignoreHidden property is set.
 */
MarkerClusterer.prototype.getIgnoreHidden = function () {
	return this.ignoreHidden_;
};


/**
 *  Sets the value of the <code>ignoreHidden</code> property.
 *
 *  @param {boolean} ignoreHidden The value of the ignoreHidden property.
 */
MarkerClusterer.prototype.setIgnoreHidden = function (ignoreHidden) {
	this.ignoreHidden_ = ignoreHidden;
};


/**
 * Returns the value of the <code>imageExtension</code> property.
 *
 * @return {string} The value of the imageExtension property.
 */
MarkerClusterer.prototype.getImageExtension = function () {
	return this.imageExtension_;
};


/**
 *  Sets the value of the <code>imageExtension</code> property.
 *
 *  @param {string} imageExtension The value of the imageExtension property.
 */
MarkerClusterer.prototype.setImageExtension = function (imageExtension) {
	this.imageExtension_ = imageExtension;
};


/**
 * Returns the value of the <code>imagePath</code> property.
 *
 * @return {string} The value of the imagePath property.
 */
MarkerClusterer.prototype.getImagePath = function () {
	return this.imagePath_;
};


/**
 *  Sets the value of the <code>imagePath</code> property.
 *
 *  @param {string} imagePath The value of the imagePath property.
 */
MarkerClusterer.prototype.setImagePath = function (imagePath) {
	this.imagePath_ = imagePath;
};


/**
 * Returns the value of the <code>imageSizes</code> property.
 *
 * @return {Array} The value of the imageSizes property.
 */
MarkerClusterer.prototype.getImageSizes = function () {
	return this.imageSizes_;
};


/**
 *  Sets the value of the <code>imageSizes</code> property.
 *
 *  @param {Array} imageSizes The value of the imageSizes property.
 */
MarkerClusterer.prototype.setImageSizes = function (imageSizes) {
	this.imageSizes_ = imageSizes;
};


/**
 * Returns the value of the <code>calculator</code> property.
 *
 * @return {function} the value of the calculator property.
 */
MarkerClusterer.prototype.getCalculator = function () {
	return this.calculator_;
};


/**
 * Sets the value of the <code>calculator</code> property.
 *
 * @param {function(Array.<google.maps.Marker>, number)} calculator The value
 *  of the calculator property.
 */
MarkerClusterer.prototype.setCalculator = function (calculator) {
	this.calculator_ = calculator;
};


/**
 * Returns the value of the <code>printable</code> property.
 *
 * @return {boolean} the value of the printable property.
 */
MarkerClusterer.prototype.getPrintable = function () {
	return this.printable_;
};


/**
 * Sets the value of the <code>printable</code> property.
 *
 *  @param {boolean} printable The value of the printable property.
 */
MarkerClusterer.prototype.setPrintable = function (printable) {
	this.printable_ = printable;
};


/**
 * Returns the value of the <code>batchSizeIE</code> property.
 *
 * @return {number} the value of the batchSizeIE property.
 */
MarkerClusterer.prototype.getBatchSizeIE = function () {
	return this.batchSizeIE_;
};


/**
 * Sets the value of the <code>batchSizeIE</code> property.
 *
 *  @param {number} batchSizeIE The value of the batchSizeIE property.
 */
MarkerClusterer.prototype.setBatchSizeIE = function (batchSizeIE) {
	this.batchSizeIE_ = batchSizeIE;
};


/**
 *  Returns the array of markers managed by the clusterer.
 *
 *  @return {Array} The array of markers managed by the clusterer.
 */
MarkerClusterer.prototype.getMarkers = function () {
	return this.markers_;
};


/**
 *  Returns the number of markers managed by the clusterer.
 *
 *  @return {number} The number of markers.
 */
MarkerClusterer.prototype.getTotalMarkers = function () {
	return this.markers_.length;
};


/**
 * Returns the current array of clusters formed by the clusterer.
 *
 * @return {Array} The array of clusters formed by the clusterer.
 */
MarkerClusterer.prototype.getClusters = function () {
	return this.clusters_;
};


/**
 * Returns the number of clusters formed by the clusterer.
 *
 * @return {number} The number of clusters formed by the clusterer.
 */
MarkerClusterer.prototype.getTotalClusters = function () {
	return this.clusters_.length;
};


/**
 * Adds a marker to the clusterer. The clusters are redrawn unless
 *  <code>opt_nodraw</code> is set to <code>true</code>.
 *
 * @param {google.maps.Marker} marker The marker to add.
 * @param {boolean} [opt_nodraw] Set to <code>true</code> to prevent redrawing.
 */
MarkerClusterer.prototype.addMarker = function (marker, opt_nodraw) {
	this.pushMarkerTo_(marker);
	if (!opt_nodraw) {
		this.redraw_();
	}
};


/**
 * Adds an array of markers to the clusterer. The clusters are redrawn unless
 *  <code>opt_nodraw</code> is set to <code>true</code>.
 *
 * @param {Array.<google.maps.Marker>} markers The markers to add.
 * @param {boolean} [opt_nodraw] Set to <code>true</code> to prevent redrawing.
 */
MarkerClusterer.prototype.addMarkers = function (markers, opt_nodraw) {
	var i;
	for (i = 0; i < markers.length; i++) {
		this.pushMarkerTo_(markers[i]);
	}
	if (!opt_nodraw) {
		this.redraw_();
	}
};


/**
 * Pushes a marker to the clusterer.
 *
 * @param {google.maps.Marker} marker The marker to add.
 */
MarkerClusterer.prototype.pushMarkerTo_ = function (marker) {
	// If the marker is draggable add a listener so we can update the clusters on the dragend:
	if (marker.getDraggable()) {
		var cMarkerClusterer = this;
		google.maps.event.addListener(marker, "dragend", function () {
			if (cMarkerClusterer.ready_) {
				this.isAdded = false;
				cMarkerClusterer.repaint();
			}
		});
	}
	marker.isAdded = false;
	this.markers_.push(marker);
};


/**
 * Removes a marker from the cluster.  The clusters are redrawn unless
 *  <code>opt_nodraw</code> is set to <code>true</code>. Returns <code>true</code> if the
 *  marker was removed from the clusterer.
 *
 * @param {google.maps.Marker} marker The marker to remove.
 * @param {boolean} [opt_nodraw] Set to <code>true</code> to prevent redrawing.
 * @return {boolean} True if the marker was removed from the clusterer.
 */
MarkerClusterer.prototype.removeMarker = function (marker, opt_nodraw) {
	var removed = this.removeMarker_(marker);

	if (!opt_nodraw && removed) {
		this.repaint();
	}

	return removed;
};


/**
 * Removes an array of markers from the cluster. The clusters are redrawn unless
 *  <code>opt_nodraw</code> is set to <code>true</code>. Returns <code>true</code> if markers
 *  were removed from the clusterer.
 *
 * @param {Array.<google.maps.Marker>} markers The markers to remove.
 * @param {boolean} [opt_nodraw] Set to <code>true</code> to prevent redrawing.
 * @return {boolean} True if markers were removed from the clusterer.
 */
MarkerClusterer.prototype.removeMarkers = function (markers, opt_nodraw) {
	var i, r;
	var removed = false;

	for (i = 0; i < markers.length; i++) {
		r = this.removeMarker_(markers[i]);
		removed = removed || r;
	}

	if (!opt_nodraw && removed) {
		this.repaint();
	}

	return removed;
};


/**
 * Removes a marker and returns true if removed, false if not.
 *
 * @param {google.maps.Marker} marker The marker to remove
 * @return {boolean} Whether the marker was removed or not
 */
MarkerClusterer.prototype.removeMarker_ = function (marker) {
	var i;
	var index = -1;
	if (this.markers_.indexOf) {
		index = this.markers_.indexOf(marker);
	} else {
		for (i = 0; i < this.markers_.length; i++) {
			if (marker === this.markers_[i]) {
				index = i;
				break;
			}
		}
	}

	if (index === -1) {
		// Marker is not in our list of markers, so do nothing:
		return false;
	}

	marker.setMap(null);
	this.markers_.splice(index, 1); // Remove the marker from the list of managed markers
	return true;
};


/**
 * Removes all clusters and markers from the map and also removes all markers
 *  managed by the clusterer.
 */
MarkerClusterer.prototype.clearMarkers = function () {
	this.resetViewport_(true);
	this.markers_ = [];
};


/**
 * Recalculates and redraws all the marker clusters from scratch.
 *  Call this after changing any properties.
 */
MarkerClusterer.prototype.repaint = function () {
	var oldClusters = this.clusters_.slice();
	this.clusters_ = [];
	this.resetViewport_(false);
	this.redraw_();

	// Remove the old clusters.
	// Do it in a timeout to prevent blinking effect.
	setTimeout(function () {
		var i;
		for (i = 0; i < oldClusters.length; i++) {
			oldClusters[i].remove();
		}
	}, 0);
};


/**
 * Returns the current bounds extended by the grid size.
 *
 * @param {google.maps.LatLngBounds} bounds The bounds to extend.
 * @return {google.maps.LatLngBounds} The extended bounds.
 * @ignore
 */
MarkerClusterer.prototype.getExtendedBounds = function (bounds) {
	var projection = this.getProjection();

	// Turn the bounds into latlng.
	var tr = new google.maps.LatLng(bounds.getNorthEast().lat(),
		bounds.getNorthEast().lng());
	var bl = new google.maps.LatLng(bounds.getSouthWest().lat(),
		bounds.getSouthWest().lng());

	// Convert the points to pixels and the extend out by the grid size.
	var trPix = projection.fromLatLngToDivPixel(tr);
	trPix.x += this.gridSize_;
	trPix.y -= this.gridSize_;

	var blPix = projection.fromLatLngToDivPixel(bl);
	blPix.x -= this.gridSize_;
	blPix.y += this.gridSize_;

	// Convert the pixel points back to LatLng
	var ne = projection.fromDivPixelToLatLng(trPix);
	var sw = projection.fromDivPixelToLatLng(blPix);

	// Extend the bounds to contain the new bounds.
	bounds.extend(ne);
	bounds.extend(sw);

	return bounds;
};


/**
 * Redraws all the clusters.
 */
MarkerClusterer.prototype.redraw_ = function () {
	this.createClusters_(0);
};


/**
 * Removes all clusters from the map. The markers are also removed from the map
 *  if <code>opt_hide</code> is set to <code>true</code>.
 *
 * @param {boolean} [opt_hide] Set to <code>true</code> to also remove the markers
 *  from the map.
 */
MarkerClusterer.prototype.resetViewport_ = function (opt_hide) {
	var i, marker;
	// Remove all the clusters
	for (i = 0; i < this.clusters_.length; i++) {
		this.clusters_[i].remove();
	}
	this.clusters_ = [];

	// Reset the markers to not be added and to be removed from the map.
	for (i = 0; i < this.markers_.length; i++) {
		marker = this.markers_[i];
		marker.isAdded = false;
		if (opt_hide) {
			marker.setMap(null);
		}
	}
};


/**
 * Calculates the distance between two latlng locations in km.
 *
 * @param {google.maps.LatLng} p1 The first lat lng point.
 * @param {google.maps.LatLng} p2 The second lat lng point.
 * @return {number} The distance between the two points in km.
 * @see http://www.movable-type.co.uk/scripts/latlong.html
 */
MarkerClusterer.prototype.distanceBetweenPoints_ = function (p1, p2) {
	var R = 6371; // Radius of the Earth in km
	var dLat = (p2.lat() - p1.lat()) * Math.PI / 180;
	var dLon = (p2.lng() - p1.lng()) * Math.PI / 180;
	var a = Math.sin(dLat / 2) * Math.sin(dLat / 2) +
		Math.cos(p1.lat() * Math.PI / 180) * Math.cos(p2.lat() * Math.PI / 180) *
			Math.sin(dLon / 2) * Math.sin(dLon / 2);
	var c = 2 * Math.atan2(Math.sqrt(a), Math.sqrt(1 - a));
	var d = R * c;
	return d;
};


/**
 * Determines if a marker is contained in a bounds.
 *
 * @param {google.maps.Marker} marker The marker to check.
 * @param {google.maps.LatLngBounds} bounds The bounds to check against.
 * @return {boolean} True if the marker is in the bounds.
 */
MarkerClusterer.prototype.isMarkerInBounds_ = function (marker, bounds) {
	return bounds.contains(marker.getPosition());
};


/**
 * Adds a marker to a cluster, or creates a new cluster.
 *
 * @param {google.maps.Marker} marker The marker to add.
 */
MarkerClusterer.prototype.addToClosestCluster_ = function (marker) {
	var i, d, cluster, center;
	var distance = 40000; // Some large number
	var clusterToAddTo = null;
	for (i = 0; i < this.clusters_.length; i++) {
		cluster = this.clusters_[i];
		center = cluster.getCenter();
		if (center) {
			d = this.distanceBetweenPoints_(center, marker.getPosition());
			if (d < distance) {
				distance = d;
				clusterToAddTo = cluster;
			}
		}
	}

	if (clusterToAddTo && clusterToAddTo.isMarkerInClusterBounds(marker)) {
		clusterToAddTo.addMarker(marker);
	} else {
		cluster = new Cluster(this);
		cluster.addMarker(marker);
		this.clusters_.push(cluster);
	}
};


/**
 * Creates the clusters. This is done in batches to avoid timeout errors
 *  in some browsers when there is a huge number of markers.
 *
 * @param {number} iFirst The index of the first marker in the batch of
 *  markers to be added to clusters.
 */
MarkerClusterer.prototype.createClusters_ = function (iFirst) {
	var i, marker;
	var mapBounds;
	var cMarkerClusterer = this;
	if (!this.ready_) {
		return;
	}

	// Cancel previous batch processing if we're working on the first batch:
	if (iFirst === 0) {
		/**
		 * This event is fired when the <code>MarkerClusterer</code> begins
		 *  clustering markers.
		 * @name MarkerClusterer#clusteringbegin
		 * @param {MarkerClusterer} mc The MarkerClusterer whose markers are being clustered.
		 * @event
		 */
		google.maps.event.trigger(this, "clusteringbegin", this);

		if (typeof this.timerRefStatic !== "undefined") {
			clearTimeout(this.timerRefStatic);
			delete this.timerRefStatic;
		}
	}

	// Get our current map view bounds.
	// Create a new bounds object so we don't affect the map.
	//
	// See Comments 9 & 11 on Issue 3651 relating to this workaround for a Google Maps bug:
	if (this.getMap().getZoom() > 3) {
		mapBounds = new google.maps.LatLngBounds(this.getMap().getBounds().getSouthWest(),
			this.getMap().getBounds().getNorthEast());
	} else {
		mapBounds = new google.maps.LatLngBounds(new google.maps.LatLng(85.02070771743472, -178.48388434375), new google.maps.LatLng(-85.08136444384544, 178.00048865625));
	}
	var bounds = this.getExtendedBounds(mapBounds);

	var iLast = Math.min(iFirst + this.batchSize_, this.markers_.length);

	for (i = iFirst; i < iLast; i++) {
		marker = this.markers_[i];
		if (!marker.isAdded && this.isMarkerInBounds_(marker, bounds)) {
			if (!this.ignoreHidden_ || (this.ignoreHidden_ && marker.getVisible())) {
				this.addToClosestCluster_(marker);
			}
		}
	}

	if (iLast < this.markers_.length) {
		this.timerRefStatic = setTimeout(function () {
			cMarkerClusterer.createClusters_(iLast);
		}, 0);
	} else {
		delete this.timerRefStatic;

		/**
		 * This event is fired when the <code>MarkerClusterer</code> stops
		 *  clustering markers.
		 * @name MarkerClusterer#clusteringend
		 * @param {MarkerClusterer} mc The MarkerClusterer whose markers are being clustered.
		 * @event
		 */
		google.maps.event.trigger(this, "clusteringend", this);
	}
};


/**
 * Extends an object's prototype by another's.
 *
 * @param {Object} obj1 The object to be extended.
 * @param {Object} obj2 The object to extend with.
 * @return {Object} The new extended object.
 * @ignore
 */
MarkerClusterer.prototype.extend = function (obj1, obj2) {
	return (function (object) {
		var property;
		for (property in object.prototype) {
			this.prototype[property] = object.prototype[property];
		}
		return this;
	}).apply(obj1, [obj2]);
};


/**
 * The default function for determining the label text and style
 * for a cluster icon.
 *
 * @param {Array.<google.maps.Marker>} markers The array of markers represented by the cluster.
 * @param {number} numStyles The number of marker styles available.
 * @return {ClusterIconInfo} The information resource for the cluster.
 * @constant
 * @ignore
 */
MarkerClusterer.CALCULATOR = function (markers, numStyles) {
	var index = 0;
	var count = markers.length.toString();

	var dv = count;
	while (dv !== 0) {
		dv = parseInt(dv / 10, 10);
		index++;
	}

	index = Math.min(index, numStyles);
	return {
		text: count,
		index: index
	};
};


/**
 * The number of markers to process in one batch.
 *
 * @type {number}
 * @constant
 */
MarkerClusterer.BATCH_SIZE = 2000;


/**
 * The number of markers to process in one batch (IE only).
 *
 * @type {number}
 * @constant
 */
MarkerClusterer.BATCH_SIZE_IE = 500;


/**
 * The default root name for the marker cluster images.
 *
 * @type {string}
 * @constant
 */
MarkerClusterer.IMAGE_PATH = "http://google-maps-utility-library-v3.googlecode.com/svn/trunk/markerclustererplus/images/m";


/**
 * The default extension name for the marker cluster images.
 *
 * @type {string}
 * @constant
 */
MarkerClusterer.IMAGE_EXTENSION = "png";


/**
 * The default array of sizes for the marker cluster images.
 *
 * @type {Array.<number>}
 * @constant
 */
MarkerClusterer.IMAGE_SIZES = [53, 56, 66, 78, 90];
var regDep = {"r": [
	{"name": "Alsace", "code": "42", "dep": [67, 68]},
	{"name": "Aquitaine", "code": "72", "dep": [24, 33, 40, 47, 64]},
	{"name": "Auvergne", "code": "83", "dep": ["03", 15, 43, 63]},
	{"name": "Bourgogne", "code": "26", "dep": [21, 58, 71, 89]},
	{"name": "Bretagne", "code": "53", "dep": [22, 29, 35, 56]},
	{"name": "Centre", "code": "24", "dep": [18, 28, 36, 37, 41, 45]},
	{"name": "Champagne-Ardenne", "code": "21", "dep": ["08", 10, 51, 52]},
	{"name": "Corse", "code": "94", "dep": ["2A", "2B"]},
	{"name": "Franche-Comté", "code": "43", "dep": [25, 39, 70, 90]},
	{"name": "Guadeloupe", "code": "01", "dep": [971]},
	{"name": "Guyane", "code": "03", "dep": [973]},
	{"name": "Île-de-France", "code": "11", "dep": [75, 91, 92, 93, 77, 94, 95, 78]},
	{"name": "Languedoc-Roussillon", "code": "91", "dep": [11, 30, 34, 48, 66]},
	{"name": "Limousin", "code": "74", "dep": [74, 19, 23, 87]},
	{"name": "Lorraine", "code": "41", "dep": [54, 55, 57, 88]},
	{"name": "Martinique", "code": "02", "dep": [972]},
	{"name": "Mayotte", "code": "06", "dep": [976]},
	{"name": "Midi-Pyrénées", "code": "73", "dep": ["09", 12, 31, 32, 46, 65, 81, 82]},
	{"name": "Nord-Pas-de-Calais", "code": "31", "dep": [59, 62]},
	{"name": "Basse-Normandie", "code": "25", "dep": [14, 50, 61]},
	{"name": "Haute-Normandie", "code": "23", "dep": [27, 76]},
	{"name": "Pays de la Loire", "code": "52", "dep": [44, 49, 53, 72, 85]},
	{"name": "Picardie", "code": "22", "dep": ["02", 60, 80]},
	{"name": "Poitou-Charentes", "code": "54", "dep": [16, 17, 79, 86]},
	{"name": "Provence-Alpes-Côte d'Azur", "code": "93", "dep": ["04", "05", "06", 13, 83, 84]},
	{"name": "La Réunion", "code": "04", "dep": [974]},
	{"name": "Rhône-Alpes", "code": "82", "dep": ["01", "07", 26, 38, 42, 69, 73, 74]}
], "d": [
	{"code": "01", "name": "Ain"},
	{"code": "02", "name": "Aisne"},
	{"code": "03", "name": "Allier"},
	{"code": "04", "name": "Alpes-de-Haute-Provence"},
	{"code": "05", "name": "Hautes-Alpes"},
	{"code": "06", "name": "Alpes-Maritimes"},
	{"code": "07", "name": "Ardèche"},
	{"code": "08", "name": "Ardennes"},
	{"code": "09", "name": "Ariège"},
	{"code": "10", "name": "Aube"},
	{"code": "11", "name": "Aude"},
	{"code": "12", "name": "Aveyron"},
	{"code": "13", "name": "Bouches-du-Rhône"},
	{"code": "14", "name": "Calvados"},
	{"code": "15", "name": "Cantal"},
	{"code": "16", "name": "Charente"},
	{"code": "17", "name": "Charente-Maritime"},
	{"code": "18", "name": "Cher"},
	{"code": "19", "name": "Corrèze"},
	{"code": "2A", "name": "Corse-du-Sud"},
	{"code": "2B", "name": "Haute-Corse"},
	{"code": "21", "name": "Côte-d'Or"},
	{"code": "22", "name": "Côtes-d'Armor"},
	{"code": "23", "name": "Creuse"},
	{"code": "24", "name": "Dordogne"},
	{"code": "25", "name": "Doubs"},
	{"code": "26", "name": "Drôme"},
	{"code": "27", "name": "Eure"},
	{"code": "28", "name": "Eure-et-Loir"},
	{"code": "29", "name": "Finistère"},
	{"code": "30", "name": "Gard"},
	{"code": "31", "name": "Haute-Garonne"},
	{"code": "32", "name": "Gers"},
	{"code": "33", "name": "Gironde"},
	{"code": "34", "name": "Hérault"},
	{"code": "35", "name": "Ille-et-Vilaine"},
	{"code": "36", "name": "Indre"},
	{"code": "37", "name": "Indre-et-Loire"},
	{"code": "38", "name": "Isère"},
	{"code": "39", "name": "Jura"},
	{"code": "40", "name": "Landes"},
	{"code": "41", "name": "Loir-et-Cher"},
	{"code": "42", "name": "Loire"},
	{"code": "43", "name": "Haute-Loire"},
	{"code": "44", "name": "Loire-Atlantique"},
	{"code": "45", "name": "Loiret"},
	{"code": "46", "name": "Lot"},
	{"code": "47", "name": "Lot-et-Garonne"},
	{"code": "48", "name": "Lozère"},
	{"code": "49", "name": "Maine-et-Loire"},
	{"code": "50", "name": "Manche"},
	{"code": "51", "name": "Marne"},
	{"code": "52", "name": "Haute-Marne"},
	{"code": "53", "name": "Mayenne"},
	{"code": "54", "name": "Meurthe-et-Moselle"},
	{"code": "55", "name": "Meuse"},
	{"code": "56", "name": "Morbihan"},
	{"code": "57", "name": "Moselle"},
	{"code": "58", "name": "Nièvre"},
	{"code": "59", "name": "Nord"},
	{"code": "60", "name": "Oise"},
	{"code": "61", "name": "Orne"},
	{"code": "62", "name": "Pas-de-Calais"},
	{"code": "63", "name": "Puy-de-Dôme"},
	{"code": "64", "name": "Pyrénées-Atlantiques"},
	{"code": "65", "name": "Hautes-Pyrénées"},
	{"code": "66", "name": "Pyrénées-Orientales"},
	{"code": "67", "name": "Bas-Rhin"},
	{"code": "68", "name": "Haut-Rhin"},
	{"code": "69", "name": "Rhône"},
	{"code": "70", "name": "Haute-Saône"},
	{"code": "71", "name": "Saône-et-Loire"},
	{"code": "72", "name": "Sarthe"},
	{"code": "73", "name": "Savoie"},
	{"code": "74", "name": "Haute-Savoie"},
	{"code": "75", "name": "Paris"},
	{"code": "76", "name": "Seine-Maritime"},
	{"code": "77", "name": "Seine-et-Marne"},
	{"code": "78", "name": "Yvelines"},
	{"code": "79", "name": "Deux-Sèvres"},
	{"code": "80", "name": "Somme"},
	{"code": "81", "name": "Tarn"},
	{"code": "82", "name": "Tarn-et-Garonne"},
	{"code": "83", "name": "Var"},
	{"code": "84", "name": "Vaucluse"},
	{"code": "85", "name": "Vendée"},
	{"code": "86", "name": "Vienne"},
	{"code": "87", "name": "Haute-Vienne"},
	{"code": "88", "name": "Vosges"},
	{"code": "89", "name": "Yonne"},
	{"code": "90", "name": "Territoire de Belfort"},
	{"code": "91", "name": "Essonne"},
	{"code": "92", "name": "Hauts-de-Seine"},
	{"code": "93", "name": "Seine-Saint-Denis"},
	{"code": "94", "name": "Val-de-Marne"},
	{"code": "95", "name": "Val-d'Oise"},
	{"code": "971", "name": "Guadeloupe"},
	{"code": "972", "name": "Martinique"},
	{"code": "973", "name": "Guyane"},
	{"code": "974", "name": "La Réunion"},
	{"code": "976", "name": "Mayotte"}
]}
var mvs = {"t":{"auto":{"name":"Auto","id":2},"moto":{"name":"Moto","id":3},"collection":{"name":"Collection","id":1}},"c":{"auto":{"balade":{"id":200,"name":"Balade, Randonnée, Sortie, Rallye, Ronde, Promenade"},"bourse":{"id":210,"name":"Bourse, Brocante, Puces, Vide-greniers, Foire"},"course":{"id":220,"name":"Course (circuit), Grand Prix, V.H.C., Raid "},"exposition":{"id":230,"name":"Exposition"},"rassemblement":{"id":240,"name":"Rassemblement, Concentration, Démonstration, Parade"},"salon":{"id":250,"name":"Salon"},"enchere":{"id":260,"name":"Vente (aux enchères)"},"assemblee":{"id":270,"name":"Assemblée générale Club"}},"moto":{"balade":{"id":400,"name":"Balade, Randonnée, Sortie"},"bourse":{"id":410,"name":"Bourse"},"course":{"id":420,"name":"Course (circuit), Grand Prix, Rallye"},"exposition":{"id":430,"name":"Exposition"},"rassemblement":{"id":440,"name":"Rassemblement, concentration"},"salon":{"id":450,"name":"Salon"},"enchere":{"id":460,"name":"Vente (aux enchères)"}},"collection":{"brocante":{"id":10,"name":"Antiquités-brocante"},"braderie":{"id":20,"name":"Vide-greniers - braderie"},"videgrenier":{"id":30,"name":"Brocante et vide-greniers"},"multi":{"id":40,"name":"Salon ou bourse multi collection"},"special":{"id":50,"name":"Salon ou bourse spécialisé (collection)"},"jouet":{"id":60,"name":"Salon ou bourse de jouets"},"exposition":{"id":70,"name":"Exposition"},"enchere":{"id":80,"name":"Ventes aux enchères"},"collections":{"id":90,"name":"Brocante et collections"}}}}
$(function(){

	// Select pour les moteur de recherche
	builReg();
	builDep();
	buildType();

	// Date Picket
	datePicker();

	// Debug Mode
	debugMode();

	// Misc
	tableAlter();

	// Form
	formValidate();
	cleanSearch();
});

/*--- --- --- --- --- --- --- --- --- --- --- --- --- --- --- --- --- --- --- --- --- --- --- --- --- --- --- --- --- --
	Alternance odd/even sur les table
  --- --- --- --- --- --- --- --- --- --- --- --- --- --- --- --- --- --- --- --- --- --- --- --- --- --- --- --- --- */
function tableAlter(){

	$('table.alter').each(function(i,e){
		var css = 'odd';
		$('tr', $(e)).each(function(){
			css = (css == 'odd') ? 'even' : 'odd';
			$(this).addClass(css);
		})
		delete css;
	})

}

/*--- --- --- --- --- --- --- --- --- --- --- --- --- --- --- --- --- --- --- --- --- --- --- --- --- --- --- --- --- --
	Ajoute/Supprile .debug au <body> pour faire apparaitre la grille
  --- --- --- --- --- --- --- --- --- --- --- --- --- --- --- --- --- --- --- --- --- --- --- --- --- --- --- --- --- */
function debugMode(){
	$(document).keydown(function(e){
		if(e.keyCode == 17) $('body').toggleClass('debug');
	});
}

/*--- --- --- --- --- --- --- --- --- --- --- --- --- --- --- --- --- --- --- --- --- --- --- --- --- --- --- --- --- --
	Construit la liste des select[name=region] avec les region.
	Si data-dep="jqSelector" est disponible, la fonction buildDep() est appelé avec cet element
  --- --- --- --- --- --- --- --- --- --- --- --- --- --- --- --- --- --- --- --- --- --- --- --- --- --- --- --- --- */
function builReg(){

	$('select[name="region"]').each(function(n,e){
		var select = $(e);
		var data   = regDep.r, options=(select.prop) ? select.prop('options') : select.attr('options');

		select.find('option').remove().end().append('<option></option>');

		for(var n in data){
			options[options.length] = new Option(data[n]['name'], data[n]['code']);
		}

		select.val(select.data('sel')).on('change', function(){
			if($(this).data('dep')) builDep(this);
		});
	});
}

/*--- --- --- --- --- --- --- --- --- --- --- --- --- --- --- --- --- --- --- --- --- --- --- --- --- --- --- --- --- --
	Construit tout le select[name=dep] avec tous les département
	Si "r" est définit la liste est limitée à ce select.region
  --- --- --- --- --- --- --- --- --- --- --- --- --- --- --- --- --- --- --- --- --- --- --- --- --- --- --- --- --- */
function builDep(r){

	var sel = r ? $(r).data('dep') : 'select[name="dep"]';

	$(sel).each(function(n,e){
		var select = $(e);
		var region = (r ? $(r).val() : ''), regions=regDep.r, departs=regDep.d, myDpts=[], data=[];

		for(var i=0; i<regions.length; i++){
			if(regions[i]['code'] == region){
				myDpts = $.map(regions[i]['dep'], function(n){ return String(n); }); break;
			}
		}

		for(var i=0; i<departs.length; i++){
			if(myDpts.indexOf(departs[i]['code']) >= 0) data.push(departs[i]);
		}

		if(data.length == 0) data=departs;

		var options = (select.prop) ? select.prop('options') : select.attr('options');
		select.find('option').remove().end().append('<option></option>');

		for(var n in data){
			options[options.length] = new Option(data[n]['code']+' '+data[n]['name'], data[n]['code']);
		}

		select.val(select.data('sel'));
	});
}

/*--- --- --- --- --- --- --- --- --- --- --- --- --- --- --- --- --- --- --- --- --- --- --- --- --- --- --- --- --- --
  --- --- --- --- --- --- --- --- --- --- --- --- --- --- --- --- --- --- --- --- --- --- --- --- --- --- --- --- --- */
function buildType(){

	var select = $('select[name="type"]');
	if(select.length == 0) return;

	var options=(select.prop) ? select.prop('options') : select.attr('options');

	for(var key in mvs.t){
		options[options.length] = new Option(mvs.t[key]['name'], mvs.t[key]['id']);
	}

	select.val(select.data('sel'));

	select.on('change', buildCategory);
	buildCategory();
}

/*--- --- --- --- --- --- --- --- --- --- --- --- --- --- --- --- --- --- --- --- --- --- --- --- --- --- --- --- --- --
  --- --- --- --- --- --- --- --- --- --- --- --- --- --- --- --- --- --- --- --- --- --- --- --- --- --- --- --- --- */
function buildCategory(){

	var select = $('select[name="category"]');
	if(select.length == 0) return;

	var type= $('select[name="type"]').val(), data=[], ids={};

	for(key in mvs.t){
		ids[mvs.t[key].id] = key;
	}

	var types = mvs.c[ids[type]];

	var options = (select.prop) ? select.prop('options') : select.attr('options');
	select.find('option').remove().end(); //.append('<option></option>');

	for(var key in types){
		options[options.length] = new Option(types[key]['name'], types[key]['id']);
	}

	select.val(select.data('sel'));
}

/*--- --- --- --- --- --- --- --- --- --- --- --- --- --- --- --- --- --- --- --- --- --- --- --- --- --- --- --- --- --
	Affiche le boostrap date picker
 --- --- --- --- --- --- --- --- --- --- --- --- --- --- --- --- --- --- --- --- --- --- --- --- --- --- --- --- --- */
function datePicker(){

	var search = $('#searchDatePicker').datepicker({
		weekStart: 1
	}).on('changeDate', function(e) {

		if(e.viewMode == 'days'){
			$('.datepicker').css('display', 'none'); // Todo: fixer plus efficacement
		}
	});


}

/* --- --- --- --- --- --- --- --- --- --- --- --- --- --- --- --- --- --- --- --- --- --- --- --- --- --- --- --- --- -
  Determine	quand un formulaire doit être géré par le validateur
--- --- --- --- --- --- --- --- --- --- --- --- --- --- --- --- --- --- --- --- --- --- --- --- --- --- --- --- --- - */
function formValidate(){

	var forms = $('form.check');
	if(!forms.length) return;

	forms.parsley({
		// basic data-api overridable properties here..
		inputs: 'input, textarea, select',
		excluded: 'input[type=hidden]' ,
		trigger: false,
		focus: 'first',
	//	validationMinlength: 3,
		successClass: 'parsley-success',
		errorClass: 'parsley-error',
		validators: {},
		showErrors: true,
		messages: {},

		//some quite advanced configuration here..
		validateIfUnchanged: false,

		errors: {
		// specify where parsley error-success classes are set
			classHandler: function ( elem, isRadioOrCheckbox ) {},
			container: function ( elem, isRadioOrCheckbox ) {},
			errorsWrapper: false, //'<ul></ul>',
			errorElem: false, //'<li></li>'
		},

		listeners: {
			onFieldValidate: function ( elem, ParsleyField ) { return false; },
			onFormSubmit: function ( isFormValid, event, ParsleyForm ) {},
			onFieldError: function ( elem, constraints, ParsleyField ) {},
			onFieldSuccess: function ( elem, constraints, ParsleyField ) {}

		}
	});

}

function cleanSearch(){
	$('#cleanSearch').click(function(){
		$('.search-clean').val('');
		$(this).parents('form').submit();
	})

}
