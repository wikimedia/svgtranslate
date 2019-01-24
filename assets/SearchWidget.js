/**
 * SVG file search widget.
 *
 * @class
 * @extends OO.ui.TextInputWidget
 * @mixins OO.ui.mixin.LookupElement
 * @constructor
 * @param {Object} [config] Configuration options
 */
App.SearchWidget = function AppSearchWidget( config ) {
	OO.ui.SearchInputWidget.parent.call( this, config );
	OO.ui.mixin.LookupElement.call( this );
};
OO.inheritClass( App.SearchWidget, OO.ui.SearchInputWidget );
OO.mixinClass( App.SearchWidget, OO.ui.mixin.LookupElement );

/**
 * Get a new request object of the current lookup query value.
 * Note that the default result count limit of the API is 10.
 * @link https://www.mediawiki.org/wiki/API:Search
 *
 * @protected
 * @method
 * @return {jQuery.Promise} jQuery AJAX object, or promise object with an .abort() method
 */
App.SearchWidget.prototype.getLookupRequest = function () {
	var val = this.getValue();
	if ( val.indexOf( 'File:' ) !== -1 ) {
		// Strip any 'File:' prefix, including if a URL has been supplied.
		val = val.substring( val.indexOf( 'File:' ) + 'File:'.length );
	}
	return $.ajax( {
		url: appConfig.wikiUrl,
		dataType: 'jsonp',
		data: {
			format: 'json',
			action: 'query',
			list: 'search',
			srnamespace: 6,
			srsearch: val + ' filetype:drawing'
		}
	} );
};

/**
 * Pre-process data returned by the request from #getLookupRequest.
 *
 * The return value of this function will be cached, and any further queries for the given value
 * will use the cache rather than doing API requests.
 *
 * @protected
 * @method
 * @param {Mixed} response Response from server
 * @return {Mixed} Cached result data
 */
App.SearchWidget.prototype.getLookupCacheDataFromResponse = function ( response ) {
	if ( response.query === undefined || response.query.search === undefined ) {
		return [];
	}
	return response.query.search;
};

/**
 * Get a list of menu option widgets from the (possibly cached) data returned by
 * #getLookupCacheDataFromResponse.
 *
 * @protected
 * @method
 * @param {Mixed} data Cached result data, usually an array
 * @return {OO.ui.MenuOptionWidget[]} Menu items
 */
App.SearchWidget.prototype.getLookupMenuOptionsFromData = function ( data ) {
	var items = [],
		i, svgFilename;
	for ( i = 0; i < data.length; i++ ) {
		// Strip 'File:' prefix.
		svgFilename = String( data[ i ].title ).substring( 'File:'.length );
		items.push( new OO.ui.MenuOptionWidget( {
			data: svgFilename,
			label: svgFilename
		} ) );
	}

	return items;
};
