function alimenterChart(){
	var data = new google.visualization.DataTable();

	data.addColumn('string', 'categorie');
	data.addColumn('string', 'x_long');
	data.addColumn('string', 'y_lat');
	data.addColumn('string', 'date_installation');
	data.addRows([
	]);
}

function drawVisualization() {

	var data = alimenterChart();

	var options = {};
	options['width'] = "100%";
//	options['height'] = 300;
	options['page'] = 'enable';
	options['pageSize'] = 10;
	options['pagingSymbols'] = {prev: 'préc', next: 'suiv'};
	options['pagingButtonsConfiguration'] = 'auto';

//options['state'] ='';
//options['showAdvancedPanel'] = true;

	visualization = new google.visualization.Table(document.getElementById('tab'));
	visualization.draw(data, options);

}
