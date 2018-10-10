// SVG Translate.

// Require images used in HTML, so they can be used as assets.
require( 'oojs-ui/dist/themes/wikimediaui/images/icons/language.svg' );
require( 'oojs-ui/dist/themes/wikimediaui/images/icons/logo-Wikimedia-Commons.svg' );
require( 'oojs-ui/dist/themes/wikimediaui/images/icons/download.svg' );

// Hide tutorial box (and keep it hidden).
$( '.close' ).on( 'click', function () {
	$( '.tutorial' ).slideUp();
	window.localStorage.setItem( 'tutorial', 'no' );
} );
if ( window.localStorage.getItem( 'tutorial' ) === 'no' ) {
	$( '.tutorial' ).hide();
}
