$(function(){
	importMVS.init();
});

////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////

importMVS = {
	running: false,

	init: function(){
		$('#start').click(this.start)
	},

	start: function(){
		if(this.running) return;
		this.running = true;

		$.ajax({
			url: 'mvs/helper/import',
			dataType: 'json',
			data: {action: "prepare"}
		}).done(function(){
			importMVS.organisateur.init();
		//	importMVS.manifestation.init();
		});
	},

	////////////////////////////////////////////////////////////

	organisateur: {
		imported: 0,
		total: 0,
		bar: $('#organisateur .bar'),
		status: $('#organisateur-log'),

		init: function(){
			$.ajax({
				url: 'mvs/helper/import',
				dataType: 'json',
				data: {action: "count-organisateur"}
			}).done($.proxy(function(d){
				this.total = d.total;
				this.run();
			}, this));
		},

		run: function(){
			var ratio = this.imported / this.total;

			this.status.html("Organisateur: "+this.imported+"/"+this.total);
			this.bar.css('width', Math.round(ratio * 100)+'%');

			console.log(this.imported, this.total);
			if(this.imported == this.total) return true;

			$.ajax({
				url: 'mvs/helper/import',
				dataType: 'json',
				data: {action: "import-organisateur"}
			}).done($.proxy(function(d){
				this.imported += d.done;
				this.run();
			}, this));
		}
	},

	manifestation: {
		imported: 0,
		total: 0,

		init: function(){
			$.ajax({
				url: 'mvs/helper/import',
				dataType: 'json',
				data: {action: "count-manifestation"}
			}).done($.proxy(function(d){
				console.log(d);
				this.total = d.total;
				this.start();
			}, this));
		},

		start: function(){
			console.log("Manif: 0/"+this.total);
		}
	}

};