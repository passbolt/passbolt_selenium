module.exports = function(grunt) {

	// ========================================================================
	// High level variables

	var config = {
		'phpunit' : 'phpunit' // or ./vendors/phpunit/phpunit/phpunit.php
	};

	// ========================================================================
	// Configure task options

	grunt.initConfig({
		config : config,
		pkg: grunt.file.readJSON('package.json'),
		shell: {
			all: {
				options: {
					stderr: true
				},
				command: [
					'<%= config.phpunit %> -c tests/ALL',
					'<%= config.phpunit %> -c tests/AN',
					'<%= config.phpunit %> -c tests/AP'
				].join(';')
			}
		}
	});

	// ========================================================================
	// Initialise

	grunt.loadNpmTasks('grunt-contrib-watch');

	grunt.loadNpmTasks('grunt-shell');

	grunt.loadNpmTasks('grunt-contrib-copy');

	// ========================================================================
	// Register Tasks

	// 'grunt' will run all tests
	grunt.registerTask('default', ['shell']);

};