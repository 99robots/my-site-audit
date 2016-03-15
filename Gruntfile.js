/* jshint node:true */
module.exports = function( grunt ) {
	grunt.initConfig({
		pkg: grunt.file.readJSON( 'package.json' ),
		jscs: {
			src: 'js/*.js',
				options: {
					config: '.jscsrc',
					verbose: true,
					preset: 'wordpress'
				}
			}
	});
	grunt.loadNpmTasks( 'grunt-jscs' );
	grunt.registerTask( 'default', [ 'jscs' ] );
};
