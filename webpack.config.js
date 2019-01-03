( function () {
	var Encore = require( '@symfony/webpack-encore' ),
		CopyWebpackPlugin = require( 'copy-webpack-plugin' );

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
			// There's a problem with ./images/grab.cur etc. in oojs-ui-wikimediaui.css
			// and until https://github.com/webpack-contrib/css-loader/issues/781 has
			// been rolled out we're installing a fixed version of css-loader in package.json.
			// That can be removed when the above issue's fix makes it into a release.
			'./node_modules/oojs-ui/dist/oojs-ui.js',
			'./node_modules/oojs-ui/dist/oojs-ui-wikimediaui.js',
			'./node_modules/oojs-ui/dist/oojs-ui-wikimediaui.min.css',
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
