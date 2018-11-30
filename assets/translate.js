/**
 * Add ULS to the target-language button.
 */
$( function () {
	var targetLangButton,
		$targetLangButton = $( '.target-lang-widget' );
	if ( $targetLangButton.length === 0 ) {
		// If the widget isn't present, do nothing.
		return;
	}
	function onSelectTargetLang( language ) {
		// Save the language name and code in the widget.
		this.setLabel( $.uls.data.languages[ language ][ 2 ] );
		this.setData( language );
		// Also switch what's displayed in the form when a new language is selected in the ULS.
		$( '.translation-fields .oo-ui-fieldLayout' ).each( function () {
			var field = OO.ui.infuse( $( this ) ).getField();
			if ( appConfig.translations[ field.data.nodeId ] &&
				appConfig.translations[ field.data.nodeId ][ language ]
			) {
				// If there's a translation available, set the field's value.
				field.setValue( appConfig.translations[ field.data.nodeId ][ language ].text );
			} else {
				// Otherwise, blank the field.
				field.setValue( '' );
			}
		} );
	}
	targetLangButton = OO.ui.infuse( $targetLangButton );
	targetLangButton.$element.uls( {
		onSelect: onSelectTargetLang.bind( targetLangButton ),
		// Add the preferred languages as the quick-list.
		quickList: App.getCookieVal( 'preferredLangs', [] ),
		// @HACK: Re-align the ULS menu because we're customizing its layout in translate.less.
		left: targetLangButton.$element.offset().left
	} );
} );

/**
 * Switch displayed 'from' language.
 */
$( function () {
	var sourceLangWidget,
		$sourceLangWidget = $( '.source-lang-widget' );
	if ( $sourceLangWidget.length !== 1 ) {
		// Don't do anything if the widget isn't present.
		return;
	}
	sourceLangWidget = OO.ui.infuse( $sourceLangWidget[ 0 ] );
	sourceLangWidget.on( 'change', function () {
		var newLangCode = sourceLangWidget.getValue();
		// Go through all the field labels and fetch new values from the translations.
		$( '.translation-fields .oo-ui-fieldLayout' ).each( function () {
			var fieldLayout = OO.ui.infuse( $( this ) ),
				nodeId = fieldLayout.getField().data.nodeId,
				newLabel = appConfig.translations[ nodeId ][ newLangCode ].text;
			fieldLayout.setLabel( newLabel );
		} );
	} );
} );
