global.App = require( './App.js' );
global.OO = require( '../../node_modules/oojs/dist/oojs.js' );

/**
 * @constructor
 * @param {Object} translations All existing translation strings.
 */
App.Model = function appModel( translations ) {
	OO.EventEmitter.call( this );
	this.translations = translations;
	this.sourceLang = null;
};

OO.mixinClass( App.Model, OO.EventEmitter );

App.Model.prototype.setSourceLang = function ( lang ) {
	if ( lang !== this.sourceLang ) {
		this.sourceLang = lang;
		this.emit( 'sourceLangSet' );
	}
};

App.Model.prototype.getSourceLang = function () {
	return this.sourceLang;
};

/**
 * @param {string} nodeId
 * @return {Object} With properties 'label' (string) and 'exists' (bool). If exists is false then
 * the label will be a message informing the user of this.
 */
App.Model.prototype.getSourceTranslation = function ( nodeId ) {
	// If there's no translation for this node ID, return an error message.
	if ( this.translations[ nodeId ][ this.sourceLang ] === undefined ) {
		return {
			label: this.translations[ nodeId ].fallback.text,
			exists: false
		};
	}
	return {
		label: this.translations[ nodeId ][ this.sourceLang ].text,
		exists: true
	};
};
