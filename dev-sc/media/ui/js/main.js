$(function(){

	// Select pour les moteur de recherche
	builReg();
	builDep();
	buildType();

	// Date Picket
	datePicker();

	// Debug Mode
	//debugMode();

	// Misc
	tableAlter();

	// Form
	formValidate();
	cleanSearch();

	// Autoload
	Modernizr.load([

		// Hammer pour les touch event
		{
			test : Modernizr['touch'],
		  yep  : '/media/ui/vendor/jquery-hammerjs/jquery.hammer-full.min.js'
		},

		// Home Page
		{
			test : $('#main').hasClass('home') && Modernizr['touch'],
		  yep  : '/media/ui/js/home-mobile.min.js'
		}
	])


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

	console.log(r);

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

	var el = $('#searchDatePicker');
	if(!el.length) return false;

	var search = el.datepicker({
		weekStart: 1
	}).on('changeDate', function(e) {

		if(e.viewMode == 'days'){
			$('.datepicker').css('display', 'none'); // Todo: fixer plus efficacement
		}
	});

	$('span.empty', el).unbind('click').click(function(){
		$('input', search).val('');
	})
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

/* --- --- --- --- --- --- --- --- --- --- --- --- --- --- --- --- --- --- --- --- --- --- --- --- --- --- --- --- --- -
 Vide proprement le formulaire de recheche de la colonne de droite
 --- --- --- --- --- --- --- --- --- --- --- --- --- --- --- --- --- --- --- --- --- --- --- --- --- --- --- --- --- - */
function cleanSearch(){
	$('#cleanSearch').click(function(){
		$('.search-clean').val('');
		$(this).parents('form').submit();
	})

}
