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
			// Bootstrap.
			'./node_modules/bootstrap/dist/css/bootstrap.css',
			'./node_modules/bootstrap/dist/js/bootstrap.js',

			// JQuery.
			'./node_modules/jquery/dist/jquery.js',

			// OOJS.
			'./node_modules/oojs/dist/oojs.js',

			// OOJS-UI.
			'./node_modules/oojs-ui/dist/oojs-ui-core.js',
			'./node_modules/oojs-ui/dist/oojs-ui-core-wikimediaui.css',

			// Wikimedia UI.
			'./node_modules/wikimedia-ui-base/wikimedia-ui-base.css',

			'./assets/app.js',
			'./assets/app.css'
		] )

		// Other options.
		.cleanupOutputBeforeBuild()
		.enableSourceMaps( !Encore.isProduction() )
		.enableVersioning( Encore.isProduction() );

	// eslint-disable-next-line no-undef
	module.exports = Encore.getWebpackConfig();
}() );
