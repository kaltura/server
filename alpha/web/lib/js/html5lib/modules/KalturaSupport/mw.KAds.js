/**
* Supports the parsing of ads
*/


//Global mw.addKAd manager
mw.addKalturaAds = function( embedPlayer, $adConfig, callback ) {
	embedPlayer.ads = new mw.KAds( embedPlayer, $adConfig, callback );
}

mw.sendBeaconUrl = function( beaconUrl ){
	$j('body').append( 
		$j( '<img />' ).attr({				
			'src' : beaconUrl,
			'width' : 0,
			'height' : 0
		})
	);
}

mw.KAds = function( embedPlayer, $adConfig, callback) {
	// Create a Player Manager
	return this.init( embedPlayer, $adConfig ,callback );
};

mw.KAds.prototype = {
	
	init: function( embedPlayer, $adConfig, callback ){
		var _this = this; 
		this.embedPlayer = embedPlayer;
		this.$adConfig = $adConfig;		
		// Load the Ads
		_this.loadAds( function(){
			mw.log("All ads have been loaded");
			callback();
		})			
	},
	
	// Load all the ads per the $adConfig
	loadAds: function( callback ){		
		var _this = this;
		// Get companion targets:
		var companionTargets = this._getCompanionTargets();
		
		// Get ad Configuration
		this._getAdConfigSet( function( adConfigSet){			
			var baseDisplayConf = {	
				'companionTargets' : companionTargets
			};
			
			// Get global timeout ( should be per adType ) 
			if( _this.$adConfig.attr('timeout') ){
				baseDisplayConf[ 'timeout' ] = _this.$adConfig.attr( 'timeout' ); 
			}
			
			// Merge in the companion targets and add to player timeline: 
			for( var adType in adConfigSet ){
				mw.addAdToPlayerTimeline(
					_this.embedPlayer, 
					adType,
					$j.extend( baseDisplayConf, adConfigSet[ adType ] ) // merge in baseDisplayConf
				);
			};
			// Run the callabck once all the ads have been loaded. 
			callback();
		});
	},
	/**
	 * Add ad configuration to timeline targets
	 */
	_getAdConfigSet: function( callback ){
		var _this = this;
		var namedAdTimelineTypes = [ 'preroll', 'postroll', 'postroll', 'overlay' ];
		// Maps ui-conf ads to named types
		var adAttributeMap = {
				"Interval": 'frequency',
				"StartAt": 'start'
		};
		var adConfigSet = {};
		var loadQueueCount = 0;
		// Add the ad to the ad set and check if loading is done
		var addAdCheckLoadDone = function( adType, adConf ){
			adConfigSet[ adType ] = adConf;
			if( loadQueueCount == 0 ){
				callback( adConfigSet );
			}
		};
		// Add timeline events: 	
		$j(namedAdTimelineTypes).each( function( na, adTypePrefix ){		
			var adConf = {};

			$j.each(adAttributeMap, function( adAttributeName,  displayConfName ){
				if( _this.$adConfig.attr( adTypePrefix + adAttributeName ) ){
					adConf[ displayConfName ] = _this.$adConfig.attr( adTypePrefix + adAttributeName );
				}
			});
			
			if( _this.$adConfig.attr( adTypePrefix + 'Url' ) ){
				loadQueueCount++;
				// Load and parse the adXML into displayConf format
				mw.AdLoader.load( _this.$adConfig.attr( adTypePrefix + 'Url' ) , function( adDisplayConf ){
					// Make sure we have a valid callback: 
					if( !adDisplayConf ){
						adDisplayConf = {};
					};
					loadQueueCount--;
					addAdCheckLoadDone( adTypePrefix,  $j.extend( adConf, adDisplayConf ) );
				});
			} else {
				// No async request
				addAdCheckLoadDone( adTypePrefix, adConf )
			}
		});										
		// Check if no ads had to be loaded ( no ads in _this.$adConfig )
		if( loadQueueCount == 0 ){
			callback();
		}
	},
	// Parse the rather odd ui-conf companion format
	_getCompanionTargets: function(){
		var _this = this;
		var companionTargets = [];
		var addCompanions = function( companionType, companionString ){
			var companions = companionString.split(';');
			for( var i=0;i < companions.length ; i++ ){
				companionTargets.push( 
					_this._getCompanionObject( companionType, companions[i]  )
				);
			}
		}
		if( this.$adConfig.attr( 'htmlCompanions' ) ) {
			addCompanions( 'html',  this.$adConfig.attr( 'htmlCompanions' ) );			
		} else if( this.$adConfig.attr( 'flashCompanions' ) ){
			addCompanions( 'flash', this.$adConfig.attr( 'flashCompanions' ) )
		}
		return companionTargets;
	},
	_getCompanionObject: function( companionType, companionString  ){
		var companionParts = companionString.split(':');
		return {
			'elementid' : companionParts[0],
			'type' :  companionType,
			'width' :  companionParts[1],
			'height' :  companionParts[2]
		};
	}
}
