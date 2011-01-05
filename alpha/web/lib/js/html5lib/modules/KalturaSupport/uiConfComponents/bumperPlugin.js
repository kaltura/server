/**
* Adds bumper support
*/
$j( mw ).bind( 'newEmbedPlayerEvent', function( event, embedPlayer ){
	$j( embedPlayer ).bind( 'KalturaSupport.checkUiConf', function( event, $uiConf, callback ){
		
		var $bumbPlug = $uiConf.find("uiVars var[key='bumper.plugin']");
		
		if(  $bumbPlug.attr('value') == 'true' ){
			// Build the bumper object
			var bumper = {};			
			$uiConf.find("uiVars var").each(function(inx, node ){
				var bumpIndex = $j(node).attr('key').indexOf('bumper.');
				if(  bumpIndex !== -1 ){					
					// string to boolean
					var bumperValue = ( $j(node).attr('value') == "true" )? true : $j(node).attr('value');
					bumperValue = ( bumperValue == "false" )? false: bumperValue;
					bumper[  $j(node).attr('key').replace('bumper.', '') ] =  bumperValue;
				}
			})			
			embedPlayer.bumperPlayCount = 0;
			// Get the bumper entryid				
			if( bumper.bumperEntryID ){
				mw.log( "KWidget:: checkUiConf: get sources for " + bumper.bumperEntryID);
				mw.getEntryIdSourcesFromApi( bumper.bumperEntryID, function( sources ){						
					// Add to the bumper per entry id:						
					$j( embedPlayer ).bind('play', function(){							
						if( bumper.playOnce && embedPlayer.bumperPlayCount >= 1){
							return true;
						}	
						embedPlayer.bumperPlayCount++;
						// Call the special insertAndPlaySource function 
						// ( used for ads / video inserts ) 
						embedPlayer.insertAndPlaySource( sources[0].src, bumper );									
					})
					// run 
					callback();
				});
			}
		}
	})
})