'use strict';

$(function(){

	$('#date-start').datepicker({
		format: 'yyyy-mm-dd',
		weekStart: 1
	}).on('changeDate', function(ev){
		$(this).data('datepicker').hide();
	});

	$('#date-end').datepicker({
		format: 'yyyy-mm-dd',
		weekStart: 1
	}).on('changeDate', function(ev){
		$(this).data('datepicker').hide();
	});

});

function emptyCollection(){
	if(confirm("Vider le log ?")){
		document.location = 'apilog/?flush';
	}
}