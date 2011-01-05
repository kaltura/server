/**
* PyMediaSubsConfig hooks into the pyMedia html5 library
*/
mw.plyMediaConfig = {
	bindPlayer: function( embedPlayer ){
		// add the pymedia ui to the player: 
		mw.ready(function(){
			embedPlayer.$interface.append( 
				'<div style="position:absolute;top:10px;right:10px;background-color:blue">PlyMedia Menu here</div>'
			);
		});
		
		// add any play event actions :
		$j( embedPlayer ).bind( 'play', function(){

		})
		
		// add any pause event actions :
		$j( embedPlayer ).bind( 'pause', function(){
			
		})
		
		// Add any time monitor event actions ( happens about 4 times a second )
		// if you need finer grain control you can setup your own timer  		
		$j( embedPlayer ).bind( 'monitorEvent', function(){
			// display something current time: 
			var currentTime = embedPlayer.currentTime;
			if( embedPlayer.$interface.find( '.plyMediaTimedText').length == 0 ) {
				embedPlayer.$interface.append( 
				'<div class="plyMediaTimedText" style="position:absolute;bottom:40px;right:150px;"></div> ')
			}
			embedPlayer.$interface.find( '.plyMediaTimedText').text('plyMedia Timed Text at ' +  Math.floor( currentTime ) );
		});
	
	}		
}