$(function(){

	$('.removePicture').click(function(){

		var doit = confirm('Confirmation.');
		if(!doit) return false;

		$(this).parent().remove();

		return true;
	});

	orgCity();
	orgPhone();

});


function orgCity(){

	var input = $('#city-search')
		, id_city = $('[name="id_city"]')
		, result = $('#city-result')
		, timer;

	input.keyup(function(){
		clearTimeout(timer);
		timer = setTimeout( search , 250);
	});

	function search(){

		id_city.val('');

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
		result.empty();

		$.each(results, function(n,m){

			var d = m.name +' ('+ m.zip +')'
				, i = $('<div class="item">'+d+'</div>');

			i.click(function(){
				id_city.val(m._id);
				input.val(d);
				result.empty();
			});

			result.append(i);
		});
	}


}

function orgPhone(){

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

			var table = $('table.phones-table')
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
	var inputs = $('table.phones-table .n input');
	if(inputs.length){
		inputs.off('keypress').on('keypress', function(evt){
			if(evt.keyCode == 13){
				$('table.phones-table .n a.insert-phone-line').trigger('click');
				//evt.preventDefault();
				//evt.stopPropagation();
			}
		});
	}

	function phoneFixName(){

		var table = $('table.phones-table')
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