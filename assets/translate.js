require( './js/Model.js' );

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
		ulsElement.setValue( language );
		// 2. Update the image by faking a blur event on a form input.
		$( '.translation-fields .oo-ui-fieldLayout .oo-ui-inputWidget input:first' ).trigger( 'blur' );
		// 3. Mark the translation state as not unsaved.
		appConfig.unsaved = false;
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
 * Add model event handlers to the widgets.
 */
$( function () {
	var sourceLangWidget, targetLangWidget,
		model = new App.Model( appConfig.translations ),
		$sourceLangWidget = $( '.source-lang-widget' ),
		$targetLangWidget = $( '.target-lang-widget' ),
		translationFields = [];

	// Change the source lang value.
	if ( $sourceLangWidget.length === 1 ) {
		sourceLangWidget = OO.ui.infuse( $sourceLangWidget[ 0 ] );
		sourceLangWidget.on( 'change', function () {
			model.setSourceLang( sourceLangWidget.getValue() );
		} );
	}

	// Change the target lang value.
	if ( $targetLangWidget.length === 1 ) {
		targetLangWidget = OO.ui.infuse( $targetLangWidget[ 0 ] );
		targetLangWidget.on( 'change', function () {
			model.setTargetLang( targetLangWidget.getValue() );
		} );
		model.on( 'targetLangSet', function () {
			// Modify the form value that will be submitted.
			$( "input[name='target-lang']" ).val( model.getTargetLang() );
			// We can assume ULS contains the language, because that's how it's been set.
			targetLangWidget.setLabel( $.uls.data.languages[ model.getTargetLang() ][ 2 ] );
			targetLangWidget.setData( model.getTargetLang() );
		} );
	}

	// Add event handlers for the translation fields.
	$( '.translation-fields .oo-ui-fieldLayout' ).each( function () {
		var fieldLayout = OO.ui.infuse( $( this ) ),
			nodeId = fieldLayout.getField().data[ 'tspan-id' ];
		translationFields.push( fieldLayout.getField() );

		// Set target translation.
		fieldLayout.getField().on( 'change', function () {
			model.setTargetTranslation( nodeId, fieldLayout.getField().getValue() );
		} );

		// Change field labels when the source lang changes.
		model.on( 'sourceLangSet', function () {
			var sourceTranslation = model.getSourceTranslation( nodeId );
			if ( !sourceTranslation.exists ) {
				fieldLayout.setLabel(
					$.i18n( 'source-lang-not-found', [ sourceTranslation.label ] )
				);
				fieldLayout.$element.addClass( 'source-lang-not-found' );
			} else {
				fieldLayout.setLabel( sourceTranslation.label );
				fieldLayout.$element.removeClass( 'source-lang-not-found' );
			}
		} );

		// Change field values when the target lang changes.
		model.on( 'targetLangSet', function () {
			var targetTranslation = model.getTargetTranslation( nodeId );
			fieldLayout.getField().setValue( targetTranslation );
			fieldLayout.getField().setDisabled( false );
		} );
	} );

	// After adding all the event handlers above, update the widget values.
	model.loadFromLocalStorage();
	if ( sourceLangWidget && targetLangWidget ) {
		sourceLangWidget.setValue( model.getSourceLang() );
		targetLangWidget.setValue( model.getTargetLang() );
	}
	translationFields.forEach( function ( field ) {
		field.setValue( model.getTargetTranslation( field.data[ 'tspan-id' ] ) );
	} );
} );

/**
 * When a translation field is changed, update the image preview, and also mark the form as unsaved.
 */
$( window ).on( 'load', function () {
	$( '.translation-fields .oo-ui-fieldLayout .oo-ui-inputWidget' ).each( function () {
		var inputWiget = OO.ui.infuse( $( this ) ),
			$imgElement = $( '#translation-image img' ),
			targetLang = $( ':input[name="target-lang"]' ).val(),
			updatePreviewImage = function () {
				var requestParams = {},
					canUpload = false,
					$uploadButtonElement = $( '#upload-button-widget' ),
					uploadButtonWidget = OO.ui.infuse( $uploadButtonElement );

				if ( window.alreadyUpdating ) {
					// Otherwise, it will needlessly update when the input is being enabled
					return;
				}
				window.alreadyUpdating = true;
				// Show loading indicator.
				$( '.image-column' ).addClass( 'loading' );
				// Go through all fields and construct the request parameters.
				$( '.translation-fields .oo-ui-fieldLayout' ).each( function () {
					var textChanged,
						originalText = '',
						fieldLayout = OO.ui.infuse( $( this ) ),
						tspanId = fieldLayout.getField().data[ 'tspan-id' ],
						text = fieldLayout.getField().getValue();
					if ( appConfig.translations[ tspanId ][ targetLang ] !== undefined ) {
						originalText = appConfig.translations[ tspanId ][ targetLang ].text;
					}
					textChanged = text !== '' && text !== originalText;
					requestParams[ tspanId ] = text;
					canUpload = canUpload || ( textChanged && appConfig.loggedIn );
				} );
				// Update the image.
				$.ajax( {
					type: 'POST',
					url: appConfig.baseUrl + 'api/translate/' + $imgElement.data( 'filename' ) + '/' + targetLang,
					data: requestParams,
					success: function ( result ) {
						// Remove the loading class after the image layer has re-loaded.
						appConfig.imageMapLayer.on( 'load', function () {
							$( '.image-column' ).removeClass( 'loading' );
						} );
						// Set the new image URL.
						appConfig.imageMapLayer.setUrl( result.imageSrc );
					},
					error: function () {
						OO.ui.alert( $.i18n( 'preview-error-occurred' ) );
						$( '.image-column' ).removeClass( 'loading' );
					},
					complete: function () {
						window.alreadyUpdating = false;
					}
				} );

				// Disable the upload image if there's nothing to translate
				uploadButtonWidget.setDisabled( !canUpload );
			};

		// Update the preview image on field blur and after two seconds of no typing.
		inputWiget.$input.on( 'blur', updatePreviewImage );
		inputWiget.on( 'change', OO.ui.debounce( updatePreviewImage, 2000 ) );
		inputWiget.on( 'change', function () {
			appConfig.unsaved = true;
		} );
		// Also update on initial page load, to catch any browser- or model-supplied changes.
		updatePreviewImage();
	} );
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
