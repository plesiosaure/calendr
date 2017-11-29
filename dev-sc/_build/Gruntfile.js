module.exports = function(grunt) {

	grunt.initConfig({
		pkg: grunt.file.readJSON('package.json'),

		uglify: {
			options: {
				banner: '/*! <%= pkg.name %> <%= grunt.template.today("yyyy-mm-dd") %> */\n'
			},
			cal: {
				src:  '../media/ui/dist/sc.js',
				dest: '../media/ui/dist/sc.min.js'
			}
		},

		concat: {
			cal: {
				files: {
					'../media/ui/dist/sc.js': [
						'../media/ui/vendor/bootstrap-datepicker-custom/js/bootstrap-datepicker.js',

						'../media/ui/vendor/kapmaps/kapmaps.js',
						'../media/ui/vendor/kapmaps/markerclusterer.js',

						'../user/module/calendrier/ui/js/dep.js',
						'../user/module/calendrier/ui/js/mvs.js',
						'../media/ui/js/main.js'
					]
				}
			}
		},

		copy: {
			cal: {
				files: [
					{src:'../media/ui/css/__style.css', dest:'../media/ui/dist/style.css'}
				]
			}
		},

		clean: {
			options: {
				force: true
			},
			cal: {
				src: [
					"../media/ui/dist/sc.js",
					"../media/ui/dist/sc.min.js"
				]
			}
		}


	});

	grunt.loadNpmTasks('grunt-contrib-uglify');
	grunt.loadNpmTasks('grunt-contrib-concat');
	grunt.loadNpmTasks('grunt-contrib-clean');
	grunt.loadNpmTasks('grunt-contrib-copy');

	grunt.registerTask('default', function(){
		console.log("Usage");
		console.log("grunt -v dev    Passer CALENDAR en DEV");
		console.log("grunt -v prod   Passer CALENDAR en PROD");
	});

// CALENDRIER //////////////////////////////////////////////////////////////////////////////////////////////////////////

	grunt.registerTask('dev', function(){
		grunt.task.run(['clean:cal', 'concat:cal', 'copy:cal']); //'uglify:cal'
	});

	grunt.registerTask('prod', function(){
		grunt.task.run(['clean:cal', 'concat:cal', 'uglify:cal', 'copy:cal']);
	});

};