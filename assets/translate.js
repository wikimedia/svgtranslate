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
		var $imgElement, newImageUrl;
		// 1. Save the language name and code in the widget.
		this.setLabel( $.uls.data.languages[ language ][ 2 ] );
		this.setData( language );
		this.setValue( language );
		// 2. Switch what's displayed in the form when a new language is selected in the ULS.
		$( '.translation-fields .oo-ui-fieldLayout' ).each( function () {
			var field = OO.ui.infuse( $( this ) ).getField(),
				tspanId = field.data[ 'tspan-id' ];
			if ( appConfig.translations[ tspanId ] &&
				appConfig.translations[ tspanId ][ language ]
			) {
				// If there's a translation available, set the field's value.
				field.setValue( appConfig.translations[ tspanId ][ language ].text );
			} else {
				// Otherwise, blank the field.
				field.setValue( '' );
			}
		} );
		// 3. Update the image.
		$imgElement = $( '.image img' );
		newImageUrl = $imgElement.attr( 'src' ).replace( /[a-z_-]*\.png.*$/, language + '.png' );
		$imgElement.attr( 'src', newImageUrl );
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
				nodeId = fieldLayout.getField().data.nodeId;
			if ( appConfig.translations[ nodeId ][ newLangCode ] === undefined ) {
				// If there's no source language available for a string,
				// show a message and the fallback language.
				fieldLayout.setLabel( $.i18n(
					'source-lang-not-found',
					[ appConfig.translations[ nodeId ].fallback.text ]
				) );
				fieldLayout.$element.addClass( 'source-lang-not-found' );
			} else {
				// Where available, set the source language string.
				fieldLayout.setLabel( appConfig.translations[ nodeId ][ newLangCode ].text );
			}
		} );
	} );
} );

/**
 * When a translation field is changed, update the image preview.
 */
$( function () {
	$( '.translation-fields .oo-ui-fieldLayout .oo-ui-inputWidget' ).each( function () {
		var inputWiget = OO.ui.infuse( $( this ) ),
			$imgElement = $( '.image img' ),
			targetLangWidget = OO.ui.infuse( $( '.target-lang-widget' ) ),
			targetLangCode = targetLangWidget.getValue(),
			requestParams = {},
			updatePreviewImage = function () {
				// Go through all fields and construct the request parameters.
				$( '.translation-fields .oo-ui-fieldLayout' ).each( function () {
					var fieldLayout = OO.ui.infuse( $( this ) ),
						tspanId = fieldLayout.getField().data[ 'tspan-id' ];
					requestParams[ tspanId ] = fieldLayout.getField().getValue();
				} );
				// Update the image.
				$.ajax( {
					type: 'POST',
					url: appConfig.baseUrl + 'api/translate/' + $imgElement.data( 'filename' ) + '/' + targetLangCode,
					data: requestParams,
					success: function ( result ) {
						$imgElement.attr( 'src', result.imageSrc );
					},
					error: function () {
						OO.ui.alert( $.i18n( 'preview-error-occurred' ) );
					}
				} );
			};
		// Update the preview image on field blur and after two seconds of no typing.
		inputWiget.$input.on( 'blur', updatePreviewImage );
		inputWiget.on( 'change', OO.ui.debounce( updatePreviewImage, 2000 ) );
	} );
	// Trigger a pretend blur on the first input field, in order to force a refresh of the preview
	// on page load (to catch browser-cached input values).
	$( '.translation-fields .oo-ui-fieldLayout .oo-ui-inputWidget input:first' ).trigger( 'blur' );
} );
