// ========================================
// Requires the jQuery
// ========================================
var VAMP = {
	init:function() {
	
		mw.ready(function(){
			$("#myVid").bind("play", function(e){
				VAMP.onPlayerPlayed();
			});	
		});
	
		// if(typeof(myKdp) != "undefined") {
			// // ======= Events =======
			// myKdp.addJsListener('playerPaused', VAMP.onPlayerPaused);
			// myKdp.addJsListener('playerPlayed', VAMP.onPlayerPlayed);
			// myKdp.addJsListener('playerUpdatePlayhead', VAMP.onPlayheadUpdate);
			// myKdp.addJsListener('playerUpdateNewAsset', VAMP.onPlayerUpdateNewAsset);
			// myKdp.addJsListener('playerProgress', VAMP.onPlayerProgress);
			// myKdp.addJsListener('playerPlayEnd', VAMP.onPlayerPlayEnd);
			// myKdp.addJsListener('hasOpenedFullScreen', VAMP.onFullScreen);
			// myKdp.addJsListener('hasCloseFullScreen', VAMP.closeFullScreen);
			// myKdp.addJsListener('captureThumbnailSuccess', VAMP.onCaptureThumbnailSuccess);
			// myKdp.addJsListener('captureThumbnailFailed', VAMP.onCaptureThumbnailFailed);
			// myKdp.addJsListener('endEntrySession', VAMP.onEndSession);
			
			// // ======= Actions =======
			// myKdp.addJsListener('doPause', VAMP.doPause);
			// myKdp.addJsListener('doPlay', VAMP.doPlay);
			// myKdp.addJsListener('doSeek', VAMP.doSeek);
			// myKdp.addJsListener('openFullScreen', VAMP.openFullScreen);
			// myKdp.addJsListener('closeFullScreen', VAMP.closeFullScreen);

			// //entryid = entry id or url to play
			// //entryversion = entry version id to play
			// //autoplay = true or false
			// myKdp.addJsListener('changeKshow', VAMP.changeKshow(entryid, entryversion, autoplay));
			
			// myKdp.addJsListener('volumeChange', VAMP.volumeChange(volumeLevel));	//volumeLevel = float (0-1)
			// myKdp.addJsListener('enableGui', VAMP.enableGui(playerState));			//playerState = bool
			// myKdp.addJsListener('captureThumbnail', VAMP.captureThumbnail);
			// myKdp.addJsListener('gigyaPopup', VAMP.gigyaPopup);
			// myKdp.addJsListener('fastForward', VAMP.fastForward(playbackSpeed));	//playbackSpeed = int(2, 4, 8)
			// myKdp.addJsListener('stopFastForward', VAMP.stopFastForward);
			// myKdp.addJsListener('playerReady', VAMP.playerReady);
			// myKdp.addJsListener('playerEmpty', VAMP.playerEmpty);
			// myKdp.addJsListener('closePopup', VAMP.closePopup);
			// myKdp.addJsListener('centerPopup', VAMP.centerPopup);
			// myKdp.addJsListener('showBottomBanner', VAMP.showBottomBanner);
			// myKdp.addJsListener('hideBottomBanner', VAMP.hideBottomBanner);
			// myKdp.addJsListener('showStartScreen', VAMP.showStartScreen);
			// myKdp.addJsListener('showEndScreen', VAMP.showEndScreen);
			// myKdp.addJsListener('showPlayScreen', VAMP.showPlayScreen);
			// myKdp.addJsListener('showPauseScreen', VAMP.showPauseScreen);
			// myKdp.addJsListener('setPlaylistUrl', VAMP.setPlaylistUrl(url));		//url = mRss url
		// }
	},

	// ======= Events =======
	//player entered pause state
	onPlayerPaused:function(e) {
	},

	//player entered play state
	onPlayerPlayed:function(e) {
		//alert("YES !!!");
	},

	//change in playhead time notification
	onPlayheadUpdate:function(e) {
	},

	//playhead reached another media asset (a mix is assembled from a sequence of media assets)
	onPlayerUpdateNewAsset:function(e) {
	},

	//monitors the download progress of single video streams and images
	onPlayerProgress:function(e) {
	},

	//playback ended, if there are post-playback modules like post-roll ad, post-playback modules are now being processed
	onPlayerPlayEnd:function(e) {
	},
	
	//player entered a full-screen mode
	onFullScreen:function(e) {
	},
	
	//player exit full-screen mode
	closeFullScreen:function(e) {
	},

	//thumbnail was successfully saved
	onCaptureThumbnailSuccess:function(e) {
	},

	//failed to save thumbnail
	onCaptureThumbnailFailed:function(e) {
	},

	//playback session fully ended, including post-playback modules (like post-roll ads)
	onEndSession:function(e) {
	},

	
	// ======= Actions =======
	//pause player
	doPause:function() {
	},
	
	//play player
	doPlay:function() {
	},
	
	//jump to a specific time in the playhead
	doSeek:function() {
	},
	
	//enter full-screen mode
	openFullScreen:function() {
	},
	
	//exit fullscreen mode
	closeFullScreen:function() {
	},
	
	//player volume level
	//Params: {Float: 0 - 1}
	volumeChange:function(volumeLevel) {
	},

	//set a new media to play
	//Params: {entryid: entry id or url to play, entryversion: entry version id to play, autoplay: true or false}
	changeKshow:function(entryid, entryversion, autoplay) {
	},
	
	//set enable / disable state for the player interface (buttons)
	//Params: {Boolean: true, false}
	enableGui:function(playerState) {
	},
	
	//set the currently presented image as the entry thumbnail
	captureThumbnail:function() {
	},

	//show the Share screen module (powered by Gigya)
	gigyaPopup:function() {
	},

	//fast forward playback
	//Params: {int: 2, 4, 8}
	fastForward:function(playbackSpeed) {
	},
	
	//stop fast forward and return to normal playback speed
	stopFastForward:function() {
	},
	
	//initialize process is complete, player can process commands
	//(register the rest of the commands events after this event), a media was loded and ready to be played
	playerReady:function() {
	},

	//initialize process is complete, player can process commands
	//(register the rest of the commands events after this event), no media was loaded
	playerEmpty:function() {
	},
	
	//close active popup window
	closePopup:function() {
	},
	
	//center active popup window
	centerPopup:function() {
	},
	
	//show bottom banner ad
	showBottomBanner:function() {
	},
	
	//bottom banner ad removed
	hideBottomBanner:function() {
	},
	
	//show the on-screen interface defined as the player start state screen on the uiConf
	showStartScreen:function() {
	},
	
	//show the on-screen interface defined as the end of playback screen on the uiConf
	showEndScreen:function() {
	},
	
	//show the on-screen interface defined as the playback screen on the uiConf
	showPlayScreen:function() {
	},
	
	//show the on-screen interface defined as the paused state screen on the uiConf
	showPauseScreen:function() {
	},
	
	//change the url to the playlist mRss provider
	//Params: {string: url to the mRss}
	setPlaylistUrl:function(url) {
	}
}
$(function() {
	VAMP.init();
	$('#play').click(function(){
		$("#myVid").get(0).play();
	});
});


if(typeof(console) == "undefined") {
	console		= new Object();
	console.log	= new Function();
}