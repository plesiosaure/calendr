$(function(){

	orgPicker.init();
});

orgPicker = {

	input  : $('#q'), // input de saisie
	result : $('#result'), // div de resulta

	init: function(){
		this.clearResult();

		this.input.keyup($.proxy(function(){
			var fn = $.proxy(this.searchDo, this);

			if(this.input.val() == '') this.clearResult();

			this.timer.running ? this.timer.reset(fn) : this.timer.start(fn);
		}, this));

	},

	timer: {
		duration: 500,
		running: false,
		callback: null,

		reset: function(callback){
			clearTimeout(this.t);
			this.start(callback);
		},

		start: function(callback){
			this.running = true;
			this.t = setTimeout(function(){
				this.running = false;
				clearTimeout(this.t);
				callback();
			}, this.duration)
		}
	},

	clearResult: function(txt){
		var txt = txt || '';
		this.result.html(txt);
	},

	searchDo: function(){

		var data = {
			q: this.input.val()
		};

		this.clearResult('...');
		if(data.q == '') return;

		var xhr = $.ajax({
			url: 'pick-search',
			dataType: 'json',
			type: 'get',
			data: data
		}).done($.proxy(function(data){
			if(!data.length) return;

			this.clearResult();
			for(var i=0; i<data.length; i++){
				var me = data[i];
				var el = $('<div><a href="pick/'+me._id+'">'+me.name+'</a></div>');
				el.appendTo(this.result);
			}

		}, this));
	}
};
