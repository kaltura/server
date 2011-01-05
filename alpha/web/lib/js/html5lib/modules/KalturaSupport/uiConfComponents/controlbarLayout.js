
	$j( mw ).bind( 'newEmbedPlayerEvent', function( event, embedPlayer ){
		$j( embedPlayer ).bind( 'KalturaSupport.checkUiConf', function( event, $uiConf, callback ){
			
			var disabled = [];
			
			// Check if the ui conf layout supports play/pause button
			// <button id="playBtnControllerScreen" command="play" buttontype="iconButton" focusrectpadding="0" icon="playIcon" overicon="playIcon" downicon="playIcon" disabeledicon="playIcon" selectedupicon="pauseIcon" selectedovericon="pauseIcon" selecteddownicon="pauseIcon" selecteddisabledicon="pauseIcon" tooltip="" uptooltip="Pause" selectedtooltip="Play" k_buttontype="buttonIconControllerArea" color1="14540253" color2="16777215" color3="3355443" color4="10066329" color5="16777215" font="Arial"></button>
			if( !$uiConf.find( 'button #playBtnControllerScreen' ).length ){
				// mdale: turned off for now ( seems to be the wrong target ) flash does not match html5 player
				//disabled.push( 'pause' );
			}
			
			// Check if the ui conf layout supports timer text
			// <timer id="timerControllerScreen1" width="40" stylename="timerProgressLeft" format="mm:ss" height="12" dynamiccolor="true" timertype="forwards" color1="14540253"></timer>
			if( !$uiConf.find( 'timer' ).length ){
				disabled.push( 'timeDisplay' );
			}			
			
			// Check if the ui conf layout supports scrubber
			// <vbox id="scrubberContainer" width="100%" height="30" verticalalign="middle" verticalgap="-3" notvisible="{mediaProxy.isLive}">
			if( !$uiConf.find( 'vbox #scrubberContainer' ).length ){
				disabled.push( 'playHead' );
			}

			// Check if the ui conf layout supports volume control
			// <volumebar id="volumeBar" stylename="volumeBtn" width="20" buttontype="iconButton" tooltip="Change volume" color1="14540253" color2="16777215" color3="3355443" color4="10066329" color5="16777215" font="Arial"></volumebar>
			if( !$uiConf.find( 'volumebar' ).length ){
				disabled.push( 'volumeControl' );
			}
			
			
			// Check if the ui conf layout supports play/pause button
			// <button id="fullScreenBtnControllerScreen" command="fullScreen" buttontype="iconButton" height="22" stylename="controllerScreen" icon="openFullScreenIcon" selectedupicon="closeFullScreenIcong" selectedovericon="closeFullScreenIcon" selecteddownicon="closeFullScreenIcon" selecteddisabledicon="closeFullScreenIcon" focusrectpadding="0" allowdisable="false" tooltip="Toggle fullscreen" k_buttontype="buttonIconControllerArea" color1="14540253" color2="16777215" color3="3355443" color4="10066329" color5="16777215" font="Arial"></button>
			if( !$uiConf.find( 'button #fullScreenBtnControllerScreen' ).length ){
				//disabled.push( 'fullscreen' );
			}
			
			// Check if the ui conf layout supports play/pause button
			// <button id="onVideoPlayBtnStartScreen" command="play" buttontype="onScreenButton" minwidth="60" labelplacement="top" label="Play" stylename="onScreenBtn" upicon="playIcon" overicon="playIcon" downicon="playIcon" disabeledicon="playIcon" selectedupicon="playIcon" selectedovericon="playIcon" selecteddownicon="playIcon" selecteddisabledicon="playIcon" k_buttontype="buttonIconControllerArea" tooltip="Play video" color1="14540253" color2="16777215" color3="3355443" color4="10066329" color5="16777215" font="Arial"></button>
			if( !$uiConf.find( 'button #onVideoPlayBtnStartScreen' ).length ){
				disabled.push( 'playButtonLarge' );
			}			
			
			controlbarLayout( embedPlayer, disabled );
			
			// Continue trigger event regardless of if ui-conf is found or not
			callback();
			
		});
	});
	var controlbarLayout = function( embedPlayer, disabled ){
		$j( embedPlayer ).bind( 'updateFeatureSupportEvent', function( e, supports ){
			for( var i = 0; i < disabled.length ; i++ ){
				var comm = disabled[i];
				supports[comm] = false;
			}
		});
	}