/**
 * The preferred languages input widget.
 * @param {Object} [config] Configuration options
 * @constructor
 */
App.UlsTagMultiselectWidget = function AppUlsTagMultiselectWidget( config ) {
	var i, cookie, cookieData,
		preferredLangs = [],
		preferredLangsData = [],
		Cookies = require( 'js-cookie' );

	// See if there are any preferred languages in the cookie.
	cookie = Cookies.get( 'svgtranslate' );
	if ( cookie ) {
		try {
			cookieData = JSON.parse( cookie );
			preferredLangs = cookieData.preferredLangs;
		} catch ( e ) {
			// If we can't parse the cookie value, default to none.
			preferredLangs = [];
		}
	}
	// Reorganise the saved preferred languages into the form required by the widget.
	for ( i = 0; i < preferredLangs.length; i++ ) {
		preferredLangsData.push( {
			data: preferredLangs[ i ],
			label: $.uls.data.languages[ preferredLangs[ i ] ][ 2 ]
		} );
	}

	// Call the parent constructor.
	config = $.extend( {
		// All ULS languages are allowed.
		allowedValues: Object.keys( $.uls.data.languages ),
		// The stored languages are selected.
		selected: preferredLangsData,
		// Don't show an input. This prevents users typing anything into the tag box.
		inputPosition: 'none'
	}, config );
	App.UlsTagMultiselectWidget.parent.call( this, config );
};

// Inheritance.
OO.inheritClass( App.UlsTagMultiselectWidget, OO.ui.TagMultiselectWidget );

/**
 * Select handler for the ULS popup.
 * @param {string} language The code of the selected language.
 */
App.UlsTagMultiselectWidget.prototype.onUlsSelect = function ( language ) {
	this.addTag( language, $.uls.data.languages[ language ][ 2 ] );
};
