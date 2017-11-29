'use strict';

var d = {
	_id:            '',
	models:         {},
	views:          {},
	collections:    {},
	router:         {}
};

// MODELS //////////////////////////////////////////////////////////////////////////////////////////////////////////////

d.models.dates          = Backbone.Model.extend({

	defaults: {
		days: 1
	},

	url: 'manifestation/helper/dates',

	idAttribute: 'start',

	fixUrl: function(url){
		this.url = url;
	},

	urlDelete: function(){
		this.fixUrl('manifestation/helper/dates?_id='+ d._id +'/'+ this.get('start'));
	},

	initialize: function(){
	//	this.fixUrl('manifestation/helper/dates?_id=' + d._id);
	}

});


// COLLECTIONS /////////////////////////////////////////////////////////////////////////////////////////////////////////

d.collections.dates     = Backbone.Collection.extend({

	model: d.models.dates,

	url: '',

	initialize: function(){
		this.url = 'manifestation/helper/dates?_id=' + d._id;
	}

});


// VIEWS ///////////////////////////////////////////////////////////////////////////////////////////////////////////////

d.views.form            = Backbone.View.extend({

	el: $('#form'),

	initialize:function(){
		this.date = '';
		this.toggle();

		// Input du formulaire
		this.inComment = $('input[name="comment"]');
		this.inDays    = $('input[name="days"]');
		this.inStart   = $('input[name="start"]');
	},

	events: {
	},

	//////////////

	reset: function(){
		this.inComment.val('');
		this.inDays.val('0');
	},

	toggle: function(){
		if(d.views.myDates.dates.length > 0){
			this.$el.removeClass('disabled');
		}else{
			this.$el.addClass('disabled');
		}
	},

	//////////////

	load: function(){
		var dates = d.views.myDates.dates;
		if(dates.length == 0) return;

		this.toggleDateField();

		var date = dates[0];

		if(date != this.date){
			var model = d.collections.myDates.get(date);

			this.date = date;
			this.fill(model.toJSON());
		}
	},

	fill: function(data){
		this.inComment.val(data.comment);
		this.inDays.val((data.days > 0) ? data.days : 1);

		this.inStart.val(data.display);
		this.inStart.attr('data-date', data.start);

		this.inStart.datepicker({
			'format': 'yyyy-mm-dd'
		}).on('changeDate', $.proxy(function(ev) {
			var timestamp = ev.date.getTime() / 1000;
			$(ev.currentTarget).attr('data-date', timestamp);
		}, this));
	},

	toggleDateField: function(){
		this.inStart.attr('disabled', d.views.myDates.dates.length > 1);
	},

	save: function(){

		var dates = [];
		var dateAllowed = this.inStart.attr('disabled') != 'disabled';
		var tabset = $('.tabset');
		var data = {
			days: parseInt(this.inDays.val()),
			comment: this.inComment.val(),
			display: this.inStart.val()
		};

		tabset.addClass('editing');

		_.each(d.views.myDates.dates, function(cid){
			var m = d.collections.myDates.get(cid);

			// Mise à jour du model
			m.set(data);

			// Mémo de date spour le sereur
			if(m.hasChanged()) dates.push(m.get('start'));
		});

		if(dateAllowed) dates = [dates[0]];

		// Aucun changement de MODEL = rien à faire coté serveur
		if(dates.length == 0){
			tabset.removeClass('editing');
			return;
		}

		// Pour le PUT coté serveur _ID + array de TIMESTAMP
		data._id   = d._id,
		data.dates = dates;

		// Pas besoin et risque d'embrouille pour le script (car 1 display mais N dates !!!)
		delete data.display;


		$.ajax({
			url: 'manifestation/helper/dates',
			dataType: 'json',
			type: 'put',
			data: data
		}).done(function(){
			tabset.removeClass('editing');
		});
	}

});

d.views.svg             = Backbone.View.extend({

	el: $('#svg'),

	initialize:function(){
		this.paper  = Raphael("svg");

		$(window).resize($.proxy(function(){
			this.svgHeight();
		}, this));
	},

	//////////////

	coordinates: function(e, m){

		var c = [], offset = e.offset(), background = this.$el.offset();

		switch(m){
			case 'center': c = [
				((offset.left - background.left)+ (e.width()  /2) ),
				((offset.top  - background.top) + (e.height() /2) )
			]; break;

			case 'leftCenter': c = [
				((offset.left - background.left) ),
				((offset.top  - background.top) + (e.height() /2) )
			];
		}

		return c;
	},

	svgHeight: function(){
		var dH  = $('#dates').height();
		var fH  = $('#form').height();
		var max = (dH > fH) ? dH : fH;
		$('#svg').height(max);

		this.paper.setSize($('#svg').width(), max);
	},

	//////////////

	pathExists: function(id){
		return $('path[id="'+id+'"]', this.$el).length ? true: false;
	},

	pathRemove: function(id){
		if(id == undefined){
			$('path', this.$el).remove();
		}else{
			var path = $('path[id="'+id+'"]', this.$el);
			path.remove();
		}
	},

	pathCreate: function (id, from, to){

		var middle  = (from[0] + to[0]) / 2;
		var mid     = [
			middle, from[1],
			middle, to[1],
			to[0], 	to[1]
		];

		var path_   = 'M'+ from.join(',') + ' C'+mid.join(',');
		var t       = this.paper.path(path_).attr({
			'stroke':           '#808080',
			'stroke-linecap':   'round'
		});

		if($('path').length > 1){
			t.attr({
				'stroke-dasharray': '--'
			});
		}

		t.node.id = id;
	}

});

d.views.dates           = Backbone.View.extend({

	el: $('#dates ul'),

	initialize:function(){
		this.listenTo(d.collections.myDates, 'add',    this.fillItem);
		this.listenTo(d.collections.myDates, 'reset',  this.fill);
		this.dates = [];

		this.load();
	},

	clear: function(){
		this.$el.empty();
		d.views.mySvg.svgHeight();
	},

	fill: function(){
		this.clear();
		d.collections.myDates.each(this.fillItem, this);
		d.views.mySvg.svgHeight();
	},

	fillItem: function(m){
		var view  = new d.views.datesItem({model: m});
		this.$el.append(view.render().el);
		view.postRender(this);
	},

	load: function(){
		d.collections.myDates.fetch();
	},

	//////////////


	addFromPicker: function(nd){

		var mod = new d.models.dates;

		console.log('addFromPicket');

		mod.save({
				_id: d._id,
				start: nd,
				days: 1,
				create: true,
				display: 'display'
			},{
			'success': function(model, v){
				model.set('display', v.display);
				console.log(model, v);
				d.collections.myDates.add(model);
			},
			'error': function(model, b){
				var rsp = JSON.parse(b.responseText);
				if(typeof(rsp) != 'object') return;

				if(rsp.duplicate){
					alert("Duplicate");
				}
			}
		});
	},

	dateExists: function(date){
		return this.dates.indexOf(date) >= 0;
	},

	datePush: function(model){
		this.dates.push(model.cid);
	},

	dateRemove: function(model){
		if(model == undefined){
			this.dates = [];
		}else{
			this.dates.splice(this.dates.indexOf(model.cid), 1);
		}
	}

});

d.views.datesItem       = Backbone.View.extend({

	tagName:    'li',
	className:  'clearfix',

	initialize: function(){
		this.listenTo(this.model, 'change',  this.render);
		this.listenTo(this.model, 'destroy', this.destroy);
	},

	events: {
		'click .remove':    'kill',
		'click .date':      'selected'
	},

	//////////////

	template: _.template($('#date-item').html()),

	render: function() {
		var data = this.model.toJSON();
		this.$el.html(this.template(data));
		return this;
	},

	postRender: function(parent){
		this.parent = parent;

		this.$el.attr({
			'data-cid':   this.model.cid,
			'data-start': this.model.get('start')
		});
	},

	destroy: function(){
		this.remove();
		d.views.mySvg.svgHeight();
	},

	//////////////

	kill: function(){

		if(!confirm('Supprimer ?')) return;

		// forcer un fake ID pour déclencher le DELETE via AJAX
		this.model.set('id', this.model.get('start'));
		this.model.urlDelete();

		this.model.destroy({
			wait: true
		});

		d.views.mySvg.pathRemove(this.model.get('start'));
		d.views.myForm.toggle();
	},

	selected: function(e){
		this.path(e);
		d.views.myForm.load();
	},

	path: function(e){

		var svg  = d.views.mySvg;
		var date = this.$el.data('start');

		// SANS ALT
		if(!e.altKey){
			if(svg.pathExists(date)){
				svg.pathRemove();
				this.parent.dateRemove();
				d.views.myForm.toggle();
				return;
			}

			svg.pathRemove();
			this.parent.dateRemove();
		}

		// AVEC ALT
		else{
			if(svg.pathExists(date)){
				svg.pathRemove(date);
				this.parent.dateRemove(this.model);
				d.views.myForm.toggle();
				return;
			}
		}

		if(!svg.pathExists(date)){
			var circle   = $('i.circle', this.$el);
			var position = svg.coordinates($(circle), 'center');
			var formLeft = svg.coordinates($('#form'), 'leftCenter');

			svg.pathCreate(date, position, formLeft);
			this.parent.datePush(this.model);
		}

		d.views.myForm.toggle();
	}

});


// APP /////////////////////////////////////////////////////////////////////////////////////////////////////////////////

d.views.app             = Backbone.View.extend({

	el: $('body'),

	initialize: function(){
		d._id = this.$el.data('_id');

		// Collections
		d.collections.myDates   = new d.collections.dates;

		// Views
		d.views.mySvg   = new d.views.svg;
		d.views.myDates = new d.views.dates;
		d.views.myForm  = new d.views.form;

		this.datePicker();
	},

	events: {
		'click #saveBtn': 'save'
	},

	/////////

	datePicker: function(){
		$('#new-date').datepicker({
			format: 'yyyy-mm-dd',
			weekStart: 1
		}).on('changeDate', function(){

			var timestamp  = $(this).data('datepicker').date.getTime() / 1000;
			var datepicker = $(this).data('datepicker');

			datepicker.element.val('');
			datepicker.hide();

			if(!d.collections.myDates.get(timestamp)){
				d.views.myDates.addFromPicker(timestamp);
			}
		});
	},

	save: function(){
		d.views.myForm.save();
	}

});


// INIT ////////////////////////////////////////////////////////////////////////////////////////////////////////////////

$(function(){
	d.views.myApp = new d.views.app;
});