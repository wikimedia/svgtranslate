window.alreadyUpdating = false;
window.enableInputs = function () {
	window.alreadyUpdating = true;
	$( '.translation-fields .oo-ui-fieldLayout .oo-ui-inputWidget' ).each( function () {
		var inputWiget = OO.ui.infuse( $( this ) );
		inputWiget.setDisabled( false );
	} );
	window.alreadyUpdating = false;

	// We need it only once
	window.enableInputs = function () {};
};

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
	function switchToNewTargetLang( ulsElement, language ) {
		// 1. Save the language name and code in the widget and the hidden form field.
		ulsElement.setLabel( $.uls.data.languages[ language ][ 2 ] );
		ulsElement.setData( language );
		ulsElement.setValue( language );
		$( "input[name='target-lang']" ).val( language );

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

		// 3. Update the image by faking a blur event on a form input.
		$( '.translation-fields .oo-ui-fieldLayout .oo-ui-inputWidget input:first' ).trigger( 'blur' );

		// 4. Mark the translation state as not unsaved.
		appConfig.unsaved = false;

		window.enableInputs();
	}
	function onSelectTargetLang( language ) {
		var ulsElement = this;
		// If any translations are currently unsaved, warn the user that they're about to lose their
		// work.
		// @TODO Set the 'OK' button to 'confirm-change-target-lang'.
		if ( appConfig.unsaved === true ) {
			OO.ui.confirm( $.i18n( 'confirmation-to-switch-target-lang' ) ).done( function ( confirmed ) {
				if ( confirmed ) {
					switchToNewTargetLang( ulsElement, language );
				}
			} );
		} else {
			switchToNewTargetLang( ulsElement, language );
		}
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
				nodeId = fieldLayout.getField().data[ 'tspan-id' ];
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
 * When a translation field is changed, update the image preview, and also mark the form as unsaved.
 */
$( window ).on( 'load', function () {
	$( '.translation-fields .oo-ui-fieldLayout .oo-ui-inputWidget' ).each( function () {
		var inputWiget = OO.ui.infuse( $( this ) ),
			$imgElement = $( '#translation-image img' ),
			targetLangWidget = OO.ui.infuse( $( '.target-lang-widget' ) ),
			targetLangCode = targetLangWidget.getValue(),
			requestParams = {},
			updatePreviewImage = function () {
				if ( window.alreadyUpdating ) {
					// Otherwise, it will needlessly update when the input is being enabled
					return;
				}
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
						appConfig.imageMapLayer.setUrl( result.imageSrc );
					},
					error: function () {
						OO.ui.alert( $.i18n( 'preview-error-occurred' ) );
					}
				} );
			};

		if ( targetLangCode !== 'fallback' ) {
			// 'fallback' means no language preselected, otherwise enable the controls
			inputWiget.setDisabled( false );
		}

		// Update the preview image on field blur and after two seconds of no typing.
		inputWiget.$input.on( 'blur', updatePreviewImage );
		inputWiget.on( 'change', OO.ui.debounce( updatePreviewImage, 2000 ) );
		inputWiget.on( 'change', function () {
			appConfig.unsaved = true;
		} );
	} );
	// Trigger a pretend blur on the first input field, in order to force a refresh of the preview
	// on page load (to catch browser-cached input values).
	$( '.translation-fields .oo-ui-fieldLayout .oo-ui-inputWidget input:first' ).trigger( 'blur' );
} );

/**
 * Add LeafletJS to image, for zooming and panning.
 */
$( window ).on( 'load', function () {
	var imagemap, $imageElement,
		$imageWrapper = $( '#translation-image' );
	if ( $imageWrapper.length !== 1 ) {
		// Don't do anything if the translation image isn't present.
		return;
	}
	$imageElement = $imageWrapper.find( 'img' );
	$imageElement.css( 'visibility', 'hidden' );
	$imageWrapper.css( {
		height: '80vh',
		width: 'auto'
	} );
	imagemap = L.map( $imageWrapper.attr( 'id' ), {
		crs: L.CRS.Simple,
		center: [ $imageElement.height() / 2, $imageElement.width() / 2 ],
		zoom: 0
	} );
	appConfig.imageMapLayer = L.imageOverlay( $imageElement.attr( 'src' ), [ [ 0, 0 ], [ $imageElement.height(), $imageElement.width() ] ] );
	appConfig.imageMapLayer.addTo( imagemap );
} );

/**
 * Disable the upload button once it's been clicked.
 */
$( function () {
	var uploadButtonWidget,
		$uploadButtonElement = $( '#upload-button-widget' );
	if ( $uploadButtonElement.length === 1 ) {
		// If there is an upload button, infuse it and modify its submission behaviour.
		uploadButtonWidget = OO.ui.infuse( $uploadButtonElement );
		uploadButtonWidget.on( 'click', function () {
			// Because we want to disable the upload button after its been clicked but before the
			// form has been submitted, we need to make sure the 'upload' parameter is still sent
			// (because that's how we distinguish between upload and download; they're both form
			// submission buttons on the same form). We do this by creating a new hidden element.
			var $form = uploadButtonWidget.$element.parents( 'form' ),
				$hiddenUpload = $( '<input>' ).attr( 'type', 'hidden' ).attr( 'name', 'upload' );
			$form.prepend( $hiddenUpload );
			uploadButtonWidget.setLabel( $.i18n( 'upload-button-in-progress' ) );
			uploadButtonWidget.setDisabled( true );
			$form.submit();
		} );
	}
} );
