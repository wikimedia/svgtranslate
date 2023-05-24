/**
 * The language settings dialog window.
 *
 * @param {Object} [config] Configuration options
 * @constructor
 */
App.InterfaceLangButton = function AppInterfaceLangButton( config ) {
	this.interfaceLang = config.interfaceLang;
	this.currentLang = this.interfaceLang;
	config = $.extend( {
		indicator: 'down',
		label: appConfig.languages[ this.interfaceLang ]
	}, config );
	App.InterfaceLangButton.super.call( this, config );
};
// Inheritance.
OO.inheritClass( App.InterfaceLangButton, OO.ui.ButtonWidget );

/**
 * Handles mouse click events.
 *
 * @protected
 * @param {jQuery.Event} e Mouse click event
 * @fires click
 */
// eslint-disable-next-line no-unused-vars
App.InterfaceLangButton.prototype.onClick = function ( e ) {
	this.$element.uls( {
		ulsPurpose: 'interfaceLang',
		languages: appConfig.languages,
		onSelect: this.onUlsSelect.bind( this ),
		top: App.LanguageDialog.prototype.calculateUlsTop.bind( this.$element )
	} );
};

/**
 * Called when the user selects a language in the ULS.
 *
 * @param {string} language The code of the selected language.
 */
App.InterfaceLangButton.prototype.onUlsSelect = function ( language ) {
	this.setLabel( appConfig.languages[ language ] );
	this.currentLang = language;
};
