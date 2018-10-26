/**
 * The language settings dialog window.
 * @param {Object} [config] Configuration options
 * @constructor
 */
App.LanguageDialog = function AppLanguageDialog( config ) {
	App.LanguageDialog.super.call( this, config );
	this.$element.attr( 'id', 'lang-dialog' );
	this.interfaceLang = config.interfaceLang;
};
// Inheritance.
OO.inheritClass( App.LanguageDialog, OO.ui.ProcessDialog );

// Dialog name.
App.LanguageDialog.static.name = 'languageDialog';

/**
 * Called each time the window is opened.
 * @param {Object} [data] Dialog opening data
 * @param {jQuery|string|Function|null} [data.title] Dialog title.
 * @param {Object[]} [data.actions] List of configuration options for each action.
 * @return {OO.ui.Process} Setup process
 */
App.LanguageDialog.prototype.getSetupProcess = function ( data ) {
	// Define title and actions here (rather than as static member variables as is normally done)
	// because we need i18n to have already been loaded.
	data = $.extend( {
		title: $.i18n( 'language-settings' ),
		actions: [
			{ label: $.i18n( 'done' ), flags: [ 'primary', 'progressive' ], action: 'done' },
			{ label: $.i18n( 'cancel' ), flags: 'safe' }
		]
	}, data );
	return App.LanguageDialog.super.prototype.getSetupProcess.call( this, data );
};

/**
 * Initialize the dialog window.
 */
App.LanguageDialog.prototype.initialize = function () {
	var panel, interfaceLangField, preferredLangsField;
	App.LanguageDialog.super.prototype.initialize.apply( this, arguments );

	// Tool language field.
	this.interfaceLangButton = new App.InterfaceLangButton( { interfaceLang: this.interfaceLang } );
	interfaceLangField = new OO.ui.FieldLayout(
		this.interfaceLangButton,
		{
			align: 'top',
			label: $.i18n( 'interface-lang-field-label' ),
			help: $.i18n( 'interface-lang-field-help' ),
			helpInline: true
		}
	);

	// Preferred languages field.
	this.preferredLangsWidget = new App.UlsTagMultiselectWidget();
	this.preferredLangsButton = new OO.ui.ButtonInputWidget( {
		label: $.i18n( 'add-preferred-lang' ),
		indicator: 'down'
	} );
	// Set up ULS on the 'add' button.
	this.preferredLangsButton.$element.uls( {
		ulsPurpose: 'preferredLangs',
		onSelect: this.preferredLangsWidget.onUlsSelect.bind( this.preferredLangsWidget ),
		top: this.calculateUlsTop.bind( this.preferredLangsButton.$element )
	} );
	preferredLangsField = new OO.ui.ActionFieldLayout(
		this.preferredLangsWidget,
		this.preferredLangsButton,
		{
			align: 'top',
			label: $.i18n( 'preferred-langs-field-label' ),
			help: $.i18n( 'preferred-langs-field-help' ),
			helpInline: true
		}
	);

	// Put it all together.
	panel = new OO.ui.PanelLayout( { padded: true, expanded: false } );
	panel.$element.append(
		interfaceLangField.$element,
		preferredLangsField.$element
	);
	this.$body.append( panel.$element );
};

/**
 * Get the required top offset (in pixels) for a ULS menu.
 * Should be bound to the button element in question.
 * @HACK: ULS doesn't know its position relative to the button because of OOUI.
 * @return {int}
 */
App.LanguageDialog.prototype.calculateUlsTop = function () {
	return $( this ).offset().top - ( $( this ).outerHeight() / 2 );
};

/**
 * Handle actions.
 * @param {string} [action] Symbolic name of action
 * @return {OO.ui.Process} Action process
 */
App.LanguageDialog.prototype.getActionProcess = function ( action ) {
	var dialog = this;
	if ( action === 'done' ) {
		return new OO.ui.Process( function () {
			var newUrl,
				cookieVal = {
					interfaceLang: dialog.interfaceLangButton.currentLang,
					preferredLangs: dialog.preferredLangsWidget.getValue()
				},
				Cookies = require( 'js-cookie' );
			// Store the new cookie value.
			// This is the only place the SVG Translate cookie is written.
			Cookies.set( 'svgtranslate', cookieVal );
			if ( cookieVal.interfaceLang !== dialog.interfaceLang ) {
				// Redirect back to where we currently are (without the #lang-settings fragment),
				// in order to reload the interface in the new language.
				newUrl = window.location.href.slice( 0, window.location.href.indexOf( '#' ) );
				window.location.href = newUrl;
			}
			dialog.close( { action: action } );
		} );
	}
	// Hide any still-visible ULS dialogs whenever closing this main dialog.
	$( '.uls-menu' ).hide();
	return App.LanguageDialog.super.prototype.getActionProcess.call( this, action );
};
