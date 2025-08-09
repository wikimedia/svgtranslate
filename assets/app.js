// SVG Translate.

// Workaround for OOUI and Webpack not loading things to global scope.
global.OO = OO;

// Set up App namespace.
global.App = require( './js/App.js' );

// Require images used in HTML, so they can be used as assets.
require( 'oojs-ui/dist/themes/wikimediaui/images/icons/language.svg' );
require( 'oojs-ui/dist/themes/wikimediaui/images/icons/logo-Wikimedia-Commons.svg' );
require( 'oojs-ui/dist/themes/wikimediaui/images/icons/download.svg' );
require( './fa-question-circle-solid.svg' );

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
 * Helper function for getting values out of the 'svgtranslate' cookie.
 *
 * @param {string} key The key to get, either 'interfaceLang' or 'preferredLangs'.
 * @param {*} defaultVal The default value to use if none is set in the cookie.
 * @return {*}
 */
App.getCookieVal = function ( key, defaultVal ) {
	var cookieData,
		JsCookie = require( 'js-cookie' ),
		cookie = JsCookie.get( 'svgtranslate' );
	if ( cookie ) {
		try {
			cookieData = JSON.parse( cookie );
			return cookieData[ key ];
		} catch ( e ) {
			// If we can't parse the cookie value or fetch the required key, return the default.
			return defaultVal;
		}
	}
	// If no cookie is set, return the default.
	return defaultVal;
};

/**
 * Set a value in the svgtranslate cookie.
 *
 * @param {string} key
 * @param {*} val
 */
App.setCookieVal = function ( key, val ) {
	var JsCookie = require( 'js-cookie' );
	var cookie = {};
	var cookieData = JsCookie.get( 'svgtranslate' );
	if ( cookieData ) {
		try {
			cookie = JSON.parse( cookieData );
		} catch ( e ) {
			// If the cookie is invalid JSON, ignore it.
		}
	}
	cookie[ key ] = val;
	JsCookie.set( 'svgtranslate', JSON.stringify( cookie ) );
};

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
	var $searchWidget = $( '#search-widget' );
	if ( $searchWidget.length > 0 ) {
		OO.ui.infuse( $searchWidget );
	}
} );
