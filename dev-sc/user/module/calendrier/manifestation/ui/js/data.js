'use strict';

//--- --- --- --- --- --- --- --- --- --- --- --- --- --- --- --- --- --- --- --- --- --- --- --- --- --- --- --- --- --
//--- --- --- --- --- --- --- --- --- --- --- --- --- --- --- --- --- --- --- --- --- --- --- --- --- --- --- --- --- --
var search = function(n){

	var s = {
		name: n,
		query: '',
		selected: -1,
		total: 0,
		duration: 250,
		opened: false,

		//-- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- --

		timer: {
			parent: null,
			running: false,
			callback: {},

			reset: function(){
				clearTimeout(this.t);
				this.start();
			},

			start: function(){
				this.running = true;
				this.t = setTimeout($.proxy(function(){ this.end() }, this), this.duration);
			},

			end: function(){
				this.running = false;
				clearTimeout(this.t);
				this.callback.apply(this.parent);
			}
		},

		//-- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- --

		init: function(){

			this.timer.parent = this;
			this.field = $('input[name="tmp_'+this.name+'"]');

			this.field.keyup($.proxy(function(e){
				var keyCode = e.keyCode;

				if(keyCode == 13){
					if(this.opened) this.select();
				}else
				if(keyCode == 38){
					if(this.opened) this.moveUp();
				}else
				if(keyCode == 40){
					if(this.opened) this.moveDown();
				}else{
					this.search($(e.target).val());
				}

			}, this));

			this.html = $('<div class="typeahead"></div>').attr('id', this.name+'-search').insertAfter(this.field).hide();
			this.ul   = $('<ul>').appendTo(this.html);

			this.html.css({
				width: this.field.width() + 4
			});
		},

		//-- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- --

		search: function(input){
			this.query = input;
			this.timer.callback = this.searchDo;
			this.timer.running ? this.timer.reset() : this.timer.start();
		},

		searchDo: function(){
			var data = {
				q: this.query
			};

			if(this.name == 'organisateur'){
				data.rubrique = $('select[name="mvs_type"]').val();
			}

			var xhr = $.ajax({
				url: 'helper/'+ this.name +'/search',
				dataType: 'json',
				type: 'get',
				data: data
			}).done($.proxy(function(data){
				this.total = data.length;
				this.render(data);
			}, this));
		},

		//-- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- --

		close: function(){
			this.opened = false;
			this.html.hide();
		},

		open: function(){
			this.unselection();
			this.opened = true;
			this.html.show();
		},

		updateForm: function(_id, name){
			$('input[name="id_'+ this.name +'"]').val(_id);
			$('input[name="tmp_'+ this.name +'"]').val(name);
		},

		select: function(){
			var li = $('li', this.html).eq(this.selected);
			if(li != undefined){
				this.updateForm(li.data('_id'), li.data('name'));
				this.close();
			}
		},

		selection: function(scroll){
			this.unselection();

			var li  = $('li', this.ul).eq(this.selected);
			var top = this.ul.scrollTop() + li.position().top;

			li.addClass('selected');

			if(scroll) this.ul.scrollTop(top);
		},

		unselection: function(){
			var li = $('li.selected', this.html).removeClass('selected');
		},

		moveUp: function(){
			if(this.selected > 0){
				this.selected--;
				this.selection(true);
			}
		},

		moveDown: function(){
			if(this.selected < this.total-1){
				this.selected++;
				this.selection(true);
			}
		},

		render: function(data){

			var i=0, max = data.length, ul = $('ul', this.html).empty();

			if(data.length == 0) return;

			this.open();

			$.each(data, $.proxy(function(n, d){
				var _id  = d._id;
				var name = d.name;

				var line    = $('<li/>').appendTo(ul);
				var anchor  = $('<a/>').html(name).appendTo(line);

				line.attr({'data-n':n, 'data-_id':_id, 'data-name':name }).hover(
					$.proxy(function(e){
						this.selected = $(e.target).data('n');
						this.selection(false);
					}, this),

					$.proxy(function(){
					//	this.unselection();
					}, this)
				);

				anchor.click($.proxy(function(){
					this.updateForm(_id, name);
					this.close();
				}, this));

			}, this));
		}
	}

	s.init();
}

////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////

//--- --- --- --- --- --- --- --- --- --- --- --- --- --- --- --- --- --- --- --- --- --- --- --- --- --- --- --- --- --
//--- --- --- --- --- --- --- --- --- --- --- --- --- --- --- --- --- --- --- --- --- --- --- --- --- --- --- --- --- --
function builType(){

	var select = $('select[name="mvs_type"]');
	if(select.length == 0) return;

	var data=mvs.t, options=(select.prop) ? select.prop('options') : select.attr('options');

	select.find('option').remove();

	for(var n in data){
		options[options.length] = new Option(data[n]['name'], data[n]['id']);
	}

	select.val(select.data('sel')).on('change', builCat);
	builCat();
}

//--- --- --- --- --- --- --- --- --- --- --- --- --- --- --- --- --- --- --- --- --- --- --- --- --- --- --- --- --- --
//--- --- --- --- --- --- --- --- --- --- --- --- --- --- --- --- --- --- --- --- --- --- --- --- --- --- --- --- --- --
function builCat(){

	var select = $('select[name="mvs_category"]');
	if(select.length == 0) return;

	var type = $('select[name="mvs_type"]').val(), categories=mvs.c[type], data=[];

	for(var k in mvs.c){
		var t = mvs.t[k];
		if(t.id.toString() == type) data = mvs.c[k]
	}

	var options = (select.prop) ? select.prop('options') : select.attr('options');
	select.find('option').remove();

	for(var n in data){
		options[options.length] = new Option(data[n]['name'], data[n]['id']);
	}

	select.val(select.data('sel'));
}

////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////

//--- --- --- --- --- --- --- --- --- --- --- --- --- --- --- --- --- --- --- --- --- --- --- --- --- --- --- --- --- --
//--- --- --- --- --- --- --- --- --- --- --- --- --- --- --- --- --- --- --- --- --- --- --- --- --- --- --- --- --- --
function builReg(){

	var select = $('select[name="region"]');
	if(select.length == 0) return;

	var data=regDep.r, options=(select.prop) ? select.prop('options') : select.attr('options');

	select.find('option').remove().end().append('<option></option>');

	for(var n in data){
		options[options.length] = new Option(data[n]['name'], data[n]['code']);
	}

	select.val(select.data('sel'));

	builDep();
	select.on('change', builDep);
}

//--- --- --- --- --- --- --- --- --- --- --- --- --- --- --- --- --- --- --- --- --- --- --- --- --- --- --- --- --- --
//--- --- --- --- --- --- --- --- --- --- --- --- --- --- --- --- --- --- --- --- --- --- --- --- --- --- --- --- --- --
function builDep(){

	var select = $('select[name="dept"]');
	if(select.length == 0) return;

	var region= $('select[name="region"]').val(), regions=regDep.r, departs=regDep.d, myDpts=[], data=[];

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
}

//--- --- --- --- --- --- --- --- --- --- --- --- --- --- --- --- --- --- --- --- --- --- --- --- --- --- --- --- --- --
// Créer à la volée, tous les menu de type de téléphone et y affecter la bonne valeur
//--- --- --- --- --- --- --- --- --- --- --- --- --- --- --- --- --- --- --- --- --- --- --- --- --- --- --- --- --- --
function buildMenuType(){

	var selects = $('select.menu-type');
	if(selects.length == 0) return;

	var types = {
		'tel': 'tél',
		'mob': 'mobile',
		'fax': 'fax',
		'tfax': 'tél/fax'
	};


	$.each(selects, function(index, select){
		var sel = $(select)
			, val = sel.attr('data-val')
			, options = (sel.prop) ? sel.prop('options') : sel.attr('options');

		sel.find('option').remove();

		for(var k in types){
			options[options.length] = new Option(types[k], k);
		}

		sel.val(val);
	});

}

//--- --- --- --- --- --- --- --- --- --- --- --- --- --- --- --- --- --- --- --- --- --- --- --- --- --- --- --- --- --
// Créer à la volée, tous les menu des indicatif de téléphone et y affecter la bonne valeur
//--- --- --- --- --- --- --- --- --- --- --- --- --- --- --- --- --- --- --- --- --- --- --- --- --- --- --- --- --- --
function buildMenuIndicatif(){

	var selects = $('select.menu-indicatif');
	if(selects.length == 0) return;

	$.each(selects, function(index, select){
		var sel = $(select)
			, val = sel.attr('data-val')
			, options = (sel.prop) ? sel.prop('options') : sel.attr('options');

		for(var i=0; i<telIndicatif.length; i++){
			options[i] = new Option(telIndicatif[i]['name']+' ', telIndicatif[i]['code']);
		}

		sel.val(val);
	});

}

////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////

$(function(){

	builType();
	builReg();
	buildMenuType();
	buildMenuIndicatif();
	phoneAction();
	formValidation();

	new search('organisateur');
	new search('city');

//	setRichEditor();

	$('#openContact').click(function(){
		document.location = 'mailto:'+$('input[name="contact"]').val();
	});

	$('#openWeb').click(function(){
		window.open($('input[name="web"]').val(), '', '');
	});

	$('.cb-toggle').on('change', function(e){
		var me = $(e.target);
		$('.'+me.data('toggle')).attr('checked', e.target.checked);
	})

});

//--- --- --- --- --- --- --- --- --- --- --- --- --- --- --- --- --- --- --- --- --- --- --- --- --- --- --- --- --- --
//--- --- --- --- --- --- --- --- --- --- --- --- --- --- --- --- --- --- --- --- --- --- --- --- --- --- --- --- --- --
function formValidation(){

	var myForm = $('form.check');
	if(!myForm.length) return;

//-- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- --

	myForm.parsley({
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
			errorElem: false //'<li></li>'
		},

		listeners: {
			onFieldValidate: function ( elem, ParsleyField ) { return false; },
			onFormSubmit: function ( isFormValid, event, ParsleyForm ) {},
			onFieldError: function ( elem, constraints, ParsleyField ) {},
			onFieldSuccess: function ( elem, constraints, ParsleyField ) {}

		}
	});


}

//--- --- --- --- --- --- --- --- --- --- --- --- --- --- --- --- --- --- --- --- --- --- --- --- --- --- --- --- --- --
// Action ADD/REMOVE/ENTER pour les lignes de téléphones
//--- --- --- --- --- --- --- --- --- --- --- --- --- --- --- --- --- --- --- --- --- --- --- --- --- --- --- --- --- --
function phoneAction(){

	// Remove = Kill le DOM + renommer les inputs
	var lines = $('.phones-line a.icon-remove');
	if(lines.length){
		lines.unbind('click').click(function(){
			var p = $(this).parents('tr');
			if(p && confirm("Confirmer ?")){
				p.remove();
				phoneFixName();
			}
		})
	}

	// Insert = Clone + Renommer les inputs
	var insert = $('a.insert-phone-line');
	if(insert.length){
		insert.unbind('click').click(function(){

			var table = $('#phones-table')
				, nLine = $('tr.n', table)
				, nInd  = $('select.menu-indicatif', nLine).val()
				, nType = $('select.menu-type', nLine).val()
				, clone = nLine.clone();

			// N'est plus la nouvelle ligne
			clone.removeClass('n').addClass('phones-line');
			clone.insertAfter(nLine);

			// Remettre les <select> avec les bonnes valeurs
			clone.find('select.menu-indicatif').val(nInd);
			clone.find('select.menu-type').val(nType);

			// Gerer le noms des inputs
			phoneFixName();

			// Remettre à zero la ligne d'ajout
			nLine.find('input, select').val('').find('input').eq(0).focus();
		});
	}

	// ENTER = ajouter la ligne
	var inputs = $('#phones-table .n input');
	if(inputs.length){
		inputs.off('keypress').on('keypress', function(evt){
			if(evt.keyCode == 13){
				$('#phones-table .n a.insert-phone-line').trigger('click');
				evt.preventDefault();
				//	evt.stopPropagation();
			}
		});
	}

	/*$('#phones-table input[type="number"]').keydown(function(event) {
		// Allow only backspace and delete
		if ( event.keyCode == 46 || event.keyCode == 8 ) {
			// let it happen, don't do anything
		}
		else {
			// Ensure that it is a number and stop the keypress
			if (event.keyCode < 48 || event.keyCode > 57 ) {
				event.preventDefault();
			}
		}
	});*/

}

//--- --- --- --- --- --- --- --- --- --- --- --- --- --- --- --- --- --- --- --- --- --- --- --- --- --- --- --- --- --
// Remettre les bon noms pour les [] des lignes pour le numero de téléphone
//--- --- --- --- --- --- --- --- --- --- --- --- --- --- --- --- --- --- --- --- --- --- --- --- --- --- --- --- --- --
function phoneFixName(){

	var table = $('#phones-table')
		, reg = /phones\[.*\]\[(.*)\]/
		, lines =  $('.phones-line', table);

	$.each(lines, function(index, line){
		var inputs = $('input, select', $(line));

		$.each(inputs, function(i, e){
			var oldName = $(e).attr('name')
				, newName = oldName.replace(reg, 'phones['+index+'][$1]');
			$(e).attr('name', newName);
		});

	});

	phoneAction();
}

function setRichEditor(){

	tinyMCE.init({
		mode		: 'exact',
		elements	: ['texte'],
		theme		: 'advanced',
		plugins		: 'safari,pagebreak,style,layer,table,save,advhr,advlink,emotions,inlinepopups,preview,contextmenu,paste,directionality,fullscreen,noneditable,visualchars,nonbreaking,xhtmlxtras',

		remove_script_host		: true,
		convert_urls 			: false,
		theme_advanced_buttons1 : 'code,newdocument,|,bold,italic,underline,strikethrough,|,styleselect,formatselect,|,fullscreen,pastetext,pasteword,|,bullist,numlist,|,link,unlink,anchor',

		theme_advanced_toolbar_location		: 'top',
		theme_advanced_toolbar_align		: 'left',
		theme_advanced_statusbar_location	: 'bottom',
		theme_advanced_resizing				: false,

		// Example content CSS (should be your site CSS)
//		content_css		: '../core/helper/tinymce',

		// Custom FORMAT
		//style_formats 	: MceStyleFormats,

		// Drop lists for link/image/media/template dialogs
		//	template_external_list_url	: "js/template_list.js",
		//	external_link_list_url 		: "js/link_list.js",
		//	external_image_list_url 	: "js/image_list.js",
		//	media_external_list_url 	: "js/media_list.js",

		setup : function(ed) {
			/*ed.addButton('mybutton', {
				title : 'Ins�rer des images',
				image : '../core/ui/img/_img/myb.gif',
				onclick : function() {
					mediaPicker(ed.id, 'mce');
				}
			});*/
		}

	});

}
