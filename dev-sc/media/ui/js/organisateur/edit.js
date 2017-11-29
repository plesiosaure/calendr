$(function(){
	orgWizard();
	orgUpload();
	orgDateSelector();
	orgCity();
	orgEditorCity();
});

function orgWizard(){

	var wizard = $('#wizard')
		, goNext = $('#go-next')
		, panel = $('#panel')
		, form = $('#form-edit')
		, message = $('#message')
		, status = $('.status', wizard)
		, presentation = $('textarea[name="presentation"]')
		, paper_count = $('#paperCount')
		, step = 1;

	if(!wizard || !form.length) return;

	// Init
	wizard.addClass('step-1');

	// Tableau
	$('.step', wizard).css('display', 'none').eq(0).css('display', 'block');

	// Revenir sur des étapes précédentes
	$('a', status).click(function(){
		var myStep = parseInt($(this).attr('data-step'));

		if(myStep <= step){
			$('#message').removeClass('visible');
			$('.parsley-error').removeClass('parsley-error')
			changeStep(myStep);
		}
	});

	// Téléphone
	orgPhone();

	// Formulaire
	form.parsley({
		// basic data-api overridable properties here..
		inputs: 'input, textarea, select',
		excluded: 'input[type=hidden]' ,
		trigger: false,
		focus: 'first',
		validationMinlength: 2,
		successClass: '', //'parsley-success',  // EN VERT SI OK
		errorClass: 'parsley-error',            // EN ROUGE SI PAS OK
		validators: {},
		showErrors: true,
		messages: {},

		//some quite advanced configuration here..
		validateIfUnchanged: false,

		errors: {
			// specify where parsley error-success classes are set
			classHandler: function ( elem, isRadioOrCheckbox ) {},
			container: function ( elem, isRadioOrCheckbox ) {},
			errorsWrapper: '<ul></ul>', // false
			errorElem:  '<li></li>' // false
		},

		listeners: {
			onFieldValidate: function ( elem, ParsleyField ) { return false; },
			onFormSubmit: function ( isFormValid, event, ParsleyForm ) {},
			onFieldError: function ( elem, constraints, ParsleyField ) {},
			onFieldSuccess: function ( elem, constraints, ParsleyField ) {}

		}
	});

	/*form.parsley().subscribe('parsley:form:validate', function (formInstance) {
	 formInstance.submitEvent.preventDefault();
	 });*/

	goNext.click(function(){

		var goto;

		// Masquer le message s'il y en avait un
		message.removeClass('visible');

		var auto = $('select[name="cat-auto"]')
			, moto = $('select[name="cat-moto"]')
			, coll = $('select[name="cat-collection"]')
			, city = $ ('[name="id_ville"]')

		if(step == 1){
			// Sur le formulaire d'ajout uniquement
			// Au minimum auto/moto/coll doit être renseigné
			if(auto && (auto.val() == '' && moto.val() == '' && coll.val() == '')){
				message.addClass('visible').html('Vous devez au minimum choisir un type auto/moto/collection');
				return
			}

			// Validation du groupe MANIF (champs principaux de la manif)
			if(false === form.parsley().validate('step-1')){
				message.addClass('visible').html("L'adresse de la manifestation n’est pas correcte ou incomplète");
				return
			}

			// Validation de la ville
			if(city.val().length == 0) {
				message.addClass('visible').html('La ville est manquante');
				return
			}

			goto = 2;
		}

		if(step == 2){

			// Validation du groupe DATES)
			if(false === form.parsley().validate('step-2')){
				message.addClass('visible').html('Les dates de la manif ne sont pas bonnes, ou le texte de résumé n\'est pas présent');
				return
			}

			goto = 3;
		}

		if(step == 3){

			// Validation des infos de base
			if(false === form.parsley().validate('step-3')){
				message.addClass('visible').html('Certains champs ne sont pas remplis correctement.');
				return
			}

			goto = 4;
		}

		if(goto > 0) changeStep(goto);

		return;
	});

	function changeStep(n){

		var wizard = $('#wizard');

		wizard
			.removeClass('step-'+step)
			.addClass('step-'+n)
			.find('.step')
			.css('display', 'none')
			.eq(n-1)
			.css('display', 'block');

			step = n;

		if(step < $('.step', wizard).length-1){
			wizard.removeClass('last')
		}

		console.log('step is', step);
	}

	presentation.keyup(function(){
		var total = presentation.val().length


		if(280 - total <= 0){
			presentation.val(presentation.val().substr(0,280))
			paper_count.html('Caractères restants: 0');
		}else{
			paper_count.html('Caractères restants: ' + (280 - total));
		}

	})

}

/**
 * Prépare les lignes de date (.date-selector) et l'ajoute d'une nouvelle ligne (#newDateSelector)
 */
function orgDateSelector(){

	var dateSelector = $('.date-selector')

	// Ajouter les DATE PICKER sur les lignes
	$.each(dateSelector, function(n,e){
		orgDateAction(e);
	});

	// Dupliquer une ligne de selecteur de date
	$('#newDateSelector').click(function(){

		var n = $(dateSelector.eq(0)).clone();

		n.find('input').val('');
		n.insertAfter('.date-selector:last');

		// Date Picker sur ce qu'on vient d'ajouter
		orgDateAction(n);

		// Nom des champs
		orgDateFixName();
	});

}

/**
 * Ajoute les date picker et le remove-line sur une ligne de date
 *
 * @param e // DOM Element
 */
function orgDateAction(e){

	var from = $('.date-from', e)
		, to   = $('.date-to', e)
		, nowTemp = new Date()
		, now = new Date(nowTemp.getFullYear(), nowTemp.getMonth(), nowTemp.getDate(), 0, 0, 0, 0)
		, checkin, checkout;

	//-- DATE DE DEBUT ----------
	checkin = from.datepicker({
		format: 'dd/mm/yyyy',
		weekStart: 1,
		onRender: function(date) {
			return date.valueOf() < now.valueOf() ? 'disabled' : '';
		}
	}).on('changeDate', function(ev) {
		if (ev.date.valueOf() >= checkout.date.valueOf()) {
			var newDate = new Date(ev.date);
			newDate.setDate(newDate.getDate()); // +1 si on veux le jour d'après
			checkout.setValue(newDate);
		}

		checkin.hide();
		from.trigger('change');
		to.focus();
	}).data('datepicker');

	//-- DATE DE FIN ----------
	checkout = to.datepicker({
		format: 'dd/mm/yyyy',
		weekStart: 1,
		onRender: function(date) {
			return date.valueOf() < checkin.date.valueOf() ? 'disabled' : '';
		}
	}).on('changeDate', function(ev) {
		checkout.hide();
		to.trigger('change');
	}).data('datepicker');

	//-- SUPPRESSION DE LA LIGNE ----
	$('.removeDateSelector', e).click(function(){

		// Ne pas supprimer la seul ligne qu'il reste
		var lines = $('.date-selector');
		if(lines.length <= 1) return false;

		// Supprimer la ligne
		var line = $(this).parent('.date-selector');
		if(line.length == 1) line.remove();

		// Renommer propremant le nom des champs
		orgDateFixName();
	});
}

/**
 * Fix les [0] [1] [2]... sur le nom de date picker
 */
function orgDateFixName(){

	var dateSelector = $('.date-selector')

	$.each(dateSelector, function(n, e){

		var from = $('.date-from', e)
			, to   = $('.date-to', e);


			from.attr('name', 'dates['+n+'][start]');
			to.attr('name',   'dates['+n+'][end]');
	});

}

function orgUpload() {

	var fu = $('#fileupload');
	if(!fu.length) return;

	fu.fileupload({
		url: 'upload',
		dataType: 'json',
		done: function (e, data) {

			$.each(data.result.files, function (index, file) {

				var html = '<input type="hidden" name="images[]" value="' + file.url + '" >'+
									 '<img src="' + file.thumbnailUrl + '" >';

				var z = $(
					'<div class="item">'+
						'<div class="bar">'+
							'<i class="icon-star icon-white ttip poster" rel="tooltip" data-original-title="Mettre en avant"></i>'+
							'<i class="icon-remove icon-white ttip remove" rel="tooltip" data-original-title="Supprimer cette photo"></i>'+
						'</div>'+
						'<div class="data">'+html+'</div>'+
					'</div>'
				);

				addEvent(z);

				z.appendTo('#files');
			});
		},

		progressall: function (e, data) {
			var progress = parseInt(data.loaded / data.total * 100, 10);
			$('#progress .bar').css('width', progress + '%');
		}
	}).bind('fileuploadadd', function (e, data) {
			$('#progress, #files').css('display', 'block');
		})
		.prop('disabled', !$.support.fileInput)
		.parent().addClass($.support.fileInput ? undefined : 'disabled');


	function addEvent(e){

		$(e).hover(
			function(){
				$(this).addClass('hover');
			},
			function(){
				$(this).removeClass('hover');
			}
		);

		$('i.poster', e).click(function(){
			var item = $(this).parents('.item')
				, poster = $('#poster-url')
				, url = $('input', item).val()
				, define = true;

			// Correspond à un retrait
			if(item.hasClass('poster')) define = false;

			// Tout le monde OFF
			$('.item.poster').removeClass('poster');
			poster.val('');

			if(define){
				item.addClass('poster');
				poster.val(url);
			}

		});

		$('i.remove', e).click(function(){
			$(this).parents('.item').remove();
		});

	}

	$.each($('.image-group .item'), function(i,e){
		addEvent(e);
	});

}

function orgCity(){

	var input = $('#city-search')
		, country = $('select[name="country"]')
		, id_ville = $('[name="id_ville"]')
		, zip = $('[name="zip"]')
		, result = $('#city-result')
		, timer, wiper

	result.width(input.width()).css('display', 'none');

	input.keyup(function(){
		clearTimeout(timer);
		timer = setTimeout( search , 250);
	});

	input.focus(function(){
		clearTimeout(wiper);
	});

	input.blur(function(){
		wiper = setTimeout(function(){
			result.css('display', 'none');
		}, 1000);
	});

	function search(){

		id_ville.val('');

		$.ajax({
			url: '/organisateur/city-search',
			dataType: 'json',
			data: {
				q : input.val()
			}
		}).done(function(results){
				display(results);
		});
	}

	function display(results){
		result.empty().css('display', 'block');

		$.each(results, function(n,m){

			var d = '<b>'+m.name +'</b> <i>'+ m.zip +'</i>'
				, i = $('<div class="item">'+d+'</div>');

			i.click(function(){
				id_ville.val(m._id);
				zip.val(m.zip);
				input.val(m.name);
				country.val(m.country)

				result.empty().css('display', 'none');
			});

			result.append(i);
	});


	}
}

function orgEditorCity(){

	var input = $('[name="org_city"]')
		, country = $('select[name="org_country"]')
		, id_ville = $('[name="org_id_ville"]')
		, zip = $('[name="org_zip"]')
		, result = $('#orga-city-result')
		, timer, wiper

	result.width(input.width()).css('display', 'none');

	input.keyup(function(){
		clearTimeout(timer);
		timer = setTimeout( search , 250);
	});

	input.focus(function(){
		clearTimeout(wiper);
	});

	input.blur(function(){
		wiper = setTimeout(function(){
			result.css('display', 'none');
		}, 1000);
	});

	function search(){

		id_ville.val('');

		$.ajax({
			url: '/organisateur/city-search',
			dataType: 'json',
			data: {
				q : input.val()
			}
		}).done(function(results){
			display(results);
		});
	}

	function display(results){
		result.empty().css('display', 'block');

		$.each(results, function(n,m){

			var d = '<b>'+m.name +'</b> <i>'+ m.zip +'</i>'
				, i = $('<div class="item">'+d+'</div>');

			i.click(function(){
				id_ville.val(m._id);
				zip.val(m.zip);
				input.val(m.name);
				country.val(m.country)

				result.empty().css('display', 'none');
			});

			result.append(i);
		});


	}
}

function orgPhone(){

	// Remove = Kill le DOM + renommer les inputs
	var lines = $('.phones-line a.remove-me');
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
			clone.removeClass('n').addClass('phones-line').insertAfter(nLine);

			console.log(clone.find('select.menu-indicatif'), nInd);

			// Remettre les <select> avec les bonnes valeurs
			setTimeout(function(){
				clone.find('select.menu-indicatif').val(nInd);
				clone.find('select.menu-type').val(nType);
			}, 10); // WTF ?

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
				//evt.preventDefault();
				//evt.stopPropagation();
			}
		});
	}

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

		orgPhone();
	}

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

	//-- -- -- -- -- -- -- -- -- -- -- -- -- -- --

	buildMenuIndicatif();
	buildMenuType();

}



