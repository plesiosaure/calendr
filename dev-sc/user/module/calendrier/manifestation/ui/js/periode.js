var allDates = [];

$(function(){
	pick();

	$('#start, #finish').datepicker({
		format: 'yyyy-mm',
		viewMode: 'years',
		minViewMode: 'months'
	})
});

//-- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -
//-- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -
function display(label){

	var label = $('#labels').val();
	if(label == '') return;

	$.ajax({
		url: 'manifestation/helper/label',
		dataType: 'json',
		data: {
			label: label,
			from:  $('#start').val(),
			end:   $('#finish').val()
		}
	}).done(function(j){
		allDates = j;
		dark();
		highlight();
	});
}

//-- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -
//-- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -
function dark(){
	$('.hightlight').removeClass('hightlight');
}

//-- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -
//-- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -
function highlight(){

	var i= 0, length = allDates.length;
	if(length == 0) return;

	for(i=0; i<length; i++){
		$('td[data-date="'+ allDates[i] +'"]').addClass('hightlight');
	}
}

//-- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -
//-- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -
function pick(){

	$('.myCal .days td').on('click', function(e){
		e.stopPropagation();

		var cli = $(e.target);
		var tar = (e.target.tagName == 'TD') ? cli : cli.parents('td');

		if(tar.hasClass('previous') || tar.hasClass('next') || tar.hasClass('hasData')) return false;

		dateToggle(tar);
	});
}

//-- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -
//-- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -
function dateToggle(el){

	var d = el.data('date');

	if(allDates.indexOf(d) == -1){
		allDates.push(d);
		el.addClass('hightlight');
	}else{
		allDates.splice(allDates.indexOf(d), 1);
		el.removeClass('hightlight');
	}
}

//-- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -
//-- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -
function pushToEvent(_id){

	if(allDates.length == 0) return false;

	$.ajax({
		url: 'manifestation/helper/period-push',
		dateType: 'json',
		type: 'post',
		data: {_id:_id, dates:allDates}
	}).done(function(j){
	//	document.location = 'manifestation/date?_id='+_id
	});

}

