// SVG Translate.

// Workaround for OOUI and Webpack not loading things to global scope.
global.OO = OO;

// Set up App namespace.
global.App = {};

// Require images used in HTML, so they can be used as assets.
require( 'oojs-ui/dist/themes/wikimediaui/images/icons/language.svg' );
require( 'oojs-ui/dist/themes/wikimediaui/images/icons/logo-Wikimedia-Commons.svg' );
require( 'oojs-ui/dist/themes/wikimediaui/images/icons/download.svg' );

// Load i18n message files.
$( function () {
	var lang = $( 'html' ).attr( 'lang' ),
		messagesToLoadUls = {},
		messagesToLoadApp = {};
	messagesToLoadUls[ lang ] = appConfig.assetsPath + '/i18n/jquery.uls/' + lang + '.json';
	messagesToLoadApp[ lang ] = appConfig.assetsPath + '/i18n/app/' + lang + '.json';
	if ( lang !== 'en' ) {
		// Also load English files for fallback.
		messagesToLoadUls.en = appConfig.assetsPath + '/i18n/jquery.uls/en.json';
		messagesToLoadApp.en = appConfig.assetsPath + '/i18n/app/en.json';
	}
	$.i18n().locale = lang;
	$.i18n().load( messagesToLoadUls );
	$.i18n().load( messagesToLoadApp )
		.then( App.addLanguageSettingsLink );
} );

/**
 * Add the language-settings link to the user nav list. Called after i18n messages are loaded.
 */
App.addLanguageSettingsLink = function () {
	// Create the link.
	var $langSelectorButton = $( '<a>' )
		.html( $.i18n( 'language-settings' ) )
		.attr( 'href', '#lang-dialog' )
		.on( 'click', function () {
			var windowManager = OO.ui.getWindowManager(),
				processDialog = new App.LanguageDialog( {
					interfaceLang: $( 'html' ).attr( 'lang' )
				} );
			if ( !windowManager.isElementAttached() ) {
				// Add the window manager to the DOM if required.
				$( 'body' ).append( windowManager.$element );
			}
			windowManager.addWindows( [ processDialog ] );
			windowManager.openWindow( processDialog );
		} );
	// Add the link to the DOM.
	$( 'nav.user ul' ).prepend( $( '<li>' ).append( $langSelectorButton ) );
};

// Enable the search form.
$( function () {
	if ( $( '#search-widget' ).length > 0 ) {
		OO.ui.infuse( 'search-widget' );
	}
} );
