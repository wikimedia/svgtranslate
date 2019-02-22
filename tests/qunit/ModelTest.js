require( '../../assets/js/Model' );

QUnit.test( 'source and target languages', function ( assert ) {
	var model = new App.Model();
	model.setSourceLang( 'fr' );
	assert.strictEqual( 'fr', model.getSourceLang() );
} );

QUnit.test( 'get source translation labels', function ( assert ) {
	var model = new App.Model( {
		n1: {
			fr: { text: 'Foo' },
			fallback: { text: 'Bar' }
		}
	} );
	// Switch to an existing language.
	model.setSourceLang( 'fr' );
	assert.deepEqual(
		model.getSourceTranslation( 'n1' ),
		{ label: 'Foo', exists: true }
	);
	// Switch to one that doesn't exist.
	model.setSourceLang( 'eo' );
	assert.deepEqual(
		model.getSourceTranslation( 'n1' ),
		{ label: 'Bar', exists: false }
	);
} );
