// SVG Translate.

// Hide tutorial box (and keep it hidden).
$( '.close' ).on( 'click', function () {
	$( '.tutorial' ).slideUp();
	window.localStorage.setItem( 'tutorial', 'no' );
} );
if ( window.localStorage.getItem( 'tutorial' ) === 'no' ) {
	$( '.tutorial' ).hide();
}
