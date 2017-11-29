'use strict';

function cbchange(me){
	$('input[name="del[]"]').prop('checked', me.checked);
}

function off(){
	if(confirm("Vraiment ?")){
		$('#listing').submit();
	}
}

$(function(){

	$('#date-start').datepicker({
		format: 'dd/mm/yyyy',
		weekStart: 1
	}).on('changeDate', function(ev){
		$(this).data('datepicker').hide();
	});

	$('#date-end').datepicker({
		format: 'dd/mm/yyyy',
		weekStart: 1
	}).on('changeDate', function(ev){
		$(this).data('datepicker').hide();
	});

	$('i.preview').hover(
		function(){

			var _id = $(this).data('_id');
			var pos = $(this).position();

			$('#preview').remove();

			$.ajax({
				url: 'helper/event/preview',
				data: { _id: _id }
			}).done(function(data){

				var prev    = $(data).siblings('#preview').html();
				var preview = $('<div/>').attr('id', 'preview').html(prev)
					preview.css({
						'position': 'absolute',
						'top':      pos.top+5,
						'left':     pos.left-300
					});

				preview.appendTo($('body'));
			});
		},

		function(){
			$('#preview').remove();
		}
	);

});