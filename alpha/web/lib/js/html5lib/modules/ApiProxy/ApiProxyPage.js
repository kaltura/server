/**
* mwProxy js2 page system.
*
* Invokes the apiProxy system
*/

/*
 * Since this is proxy server set a pre-append debug flag to know which debug msgs are coming from where
 */

mw.setConfig( 'Mw.LogPrepend', 'Proxy:');

// The default allowable domain list is stored in the loader.js configuration.

mw.ready( function() {
	mw.log( 'load ApiProxy' );
	mw.load( 'ApiProxy', function() {
		// Clear out the page content ( not needed for iframe proxy )
		$j( 'body' ).html( '' );
		mw.ApiProxy.server();
	} );
} );
