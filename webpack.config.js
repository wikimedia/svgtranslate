( function () {
	var Encore = require( '@symfony/webpack-encore' ),
		CopyWebpackPlugin = require( 'copy-webpack-plugin' ),
		CSSJanusPlugin = require( '@mooeypoo/cssjanus-webpack' );

	Encore

		// Directory where compiled assets will be stored.
		.setOutputPath( './public/assets/' )

		// Public URL path used by the web server to access the output path.
		.setPublicPath( 'assets/' )

		// Set up global variables.
		.autoProvideVariables( {
			OO: 'oojs',
			jQuery: 'jquery',
			$: 'jquery'
		} )

		// Copy i18n files for use by jquery.i18n.
		.addPlugin( new CopyWebpackPlugin( [
			{ from: './node_modules/jquery.uls/i18n/', to: 'i18n/jquery.uls/' },
			{ from: './i18n/', to: 'i18n/app/' }
		] ) )

		.addPlugin( new CSSJanusPlugin( [ 'app' ] ) )

		// Add extra loader for OOUI's *.cur cursor image files.
		.addLoader( { test: /\.cur$/, loader: 'file-loader' } )

		// Remove deprecated options from css-loader.
		// @TODO remove this after the options have been removed from Encore defaults.
		.configureCssLoader( function ( options ) {
			delete options.minimize;
			delete options.sourceMap;
			return options;
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
			'./node_modules/oojs-ui/dist/oojs-ui.js',
			'./node_modules/oojs-ui/dist/oojs-ui-wikimediaui.js',
			'./node_modules/oojs-ui/dist/oojs-ui-core-wikimediaui.css',
			'./node_modules/oojs-ui/dist/oojs-ui-widgets-wikimediaui.css',
			'./node_modules/oojs-ui/dist/oojs-ui-windows-wikimediaui.css',
			'./node_modules/oojs-ui/dist/oojs-ui-wikimediaui-icons-interactions.css',
			'./node_modules/oojs-ui/dist/oojs-ui-wikimediaui-icons-wikimedia.css',

			// jQuery i18n.
			'./node_modules/@wikimedia/jquery.i18n/src/jquery.i18n.js',
			'./node_modules/@wikimedia/jquery.i18n/src/jquery.i18n.messagestore.js',
			'./node_modules/@wikimedia/jquery.i18n/src/jquery.i18n.fallbacks.js',
			'./node_modules/@wikimedia/jquery.i18n/src/jquery.i18n.parser.js',
			'./node_modules/@wikimedia/jquery.i18n/src/jquery.i18n.emitter.js',
			'./node_modules/@wikimedia/jquery.i18n/src/jquery.i18n.language.js',
			'./node_modules/@wikimedia/jquery.i18n/src/languages/he.js',
			'./node_modules/@wikimedia/jquery.i18n/src/languages/fi.js',
			'./node_modules/@wikimedia/jquery.i18n/src/languages/ml.js',

			// Universal Language Selector.
			'./node_modules/jquery.uls/src/jquery.uls.data.js',
			'./node_modules/jquery.uls/src/jquery.uls.data.utils.js',
			'./node_modules/jquery.uls/src/jquery.uls.lcd.js',
			'./node_modules/jquery.uls/src/jquery.uls.languagefilter.js',
			'./node_modules/jquery.uls/src/jquery.uls.core.js',
			'./node_modules/jquery.uls/css/jquery.uls.css',
			'./node_modules/jquery.uls/css/jquery.uls.grid.css',
			'./node_modules/jquery.uls/css/jquery.uls.lcd.css',

			// Leaflet.
			'./node_modules/leaflet/dist/leaflet.js',
			'./node_modules/leaflet/dist/leaflet.css',

			// This app.
			'./assets/app.js',
			'./assets/app.less',
			'./assets/search.less',
			'./assets/translate.js',
			'./assets/translate.less',
			'./assets/InterfaceLangButton.js',
			'./assets/LanguageDialog.js',
			'./assets/SearchWidget.js',
			'./assets/UlsTagMultiselectWidget.js'
		] )

		// Other options.
		.enableLessLoader()
		.cleanupOutputBeforeBuild()
		.disableSingleRuntimeChunk()
		.enableSourceMaps( !Encore.isProduction() )
		.enableVersioning( Encore.isProduction() );

	// eslint-disable-next-line no-undef
	module.exports = Encore.getWebpackConfig();

	// Set a relative path for image assets; see https://github.com/symfony/webpack-encore/issues/88
	// eslint-disable-next-line no-undef
	module.exports.module.rules[ 2 ].options.publicPath = '../assets/';
}() );
