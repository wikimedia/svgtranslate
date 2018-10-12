( function () {
	var Encore = require( '@symfony/webpack-encore' );

	Encore

		// Directory where compiled assets will be stored.
		.setOutputPath( './public/assets/' )

		// Public URL path used by the web server to access the output path.
		.setPublicPath( 'assets/' )

		// Set up global variables.
		.autoProvideVariables( {
			OO: 'oojs',
			$: 'jquery'
		} )

		/*
		 * ENTRY CONFIG
		 *
		 * Add 1 entry for each "page" of your app
		 * (including one that's included on every page - e.g. "app")
		 *
		 * Each entry will result in one JavaScript file (e.g. app.js)
		 * and one CSS file (e.g. app.css) if you JavaScript imports CSS.
		 */
		.addEntry( 'app', [
			// JQuery.
			'./node_modules/jquery/dist/jquery.js',

			// OOJS.
			'./node_modules/oojs/dist/oojs.js',

			// OOJS-UI.
			'./node_modules/oojs-ui/dist/oojs-ui-core.js',
			'./node_modules/oojs-ui/dist/oojs-ui-core-wikimediaui.css',

			'./assets/app.js',
			'./assets/app.less',
			'./assets/search.less'
		] )

		// Other options.
		.enableLessLoader()
		.cleanupOutputBeforeBuild()
		.enableSourceMaps( !Encore.isProduction() )
		.enableVersioning( Encore.isProduction() );

	// eslint-disable-next-line no-undef
	module.exports = Encore.getWebpackConfig();
}() );
