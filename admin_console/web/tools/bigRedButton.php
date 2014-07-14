<?php 
if(!isset($_GET['partnerId']))
	die('partnerId must be supplied in query string');

$partnerId = $_GET['partnerId'];

$html5Version = 'v2.13.rc1';
?>
<!DOCTYPE html>
<!--[if lt IE 7]>      <html class="lt-ie10 lt-ie9 lt-ie8 lt-ie7"> <![endif]-->
<!--[if IE 7]>         <html class="lt-ie10 lt-ie9 lt-ie8"> <![endif]-->
<!--[if IE 8]>         <html class="lt-ie10 lt-ie9"> <![endif]-->
<!--[if lt IE 10]>     <html class="lt-ie10"> <![endif]-->
<!--[if gt IE 8]><!--> <html> <!--<![endif]-->
<head>
	<meta charset="utf-8">
	<meta http-equiv="X-UA-Compatible" content="IE=edge,chrome=1">
	
	<title>Big-Red-Button Demo</title>
	<link type="text/css" rel="stylesheet" href="/lib/css/kmc.css" />
	<style>
		#main .content .title h1 { font-size: 24px; font-weight: bold; }
		#main p { margin-bottom: 20px; font-size: 18px; }
	</style>
	
	<script src="/lib/js/jquery-1.8.3.min.js"></script>
	<script type="text/javascript" src="/html5/html5lib/<?php echo $html5Version; ?>/mwEmbedLoader.php"></script>
	<script type="text/javascript" src="swfobject.js"></script>
	<script>
		var partnerId = <?php echo $partnerId; ?>;
		var ks = null;
		var lastSyncPointTime = null;
		var lastSyncPointOffset = null;

		function loadPlayer(){
			var entryId = $('#txtEntryId').val();

			var hlsUrl = location.protocol + '//';
			hlsUrl += location.host;
			hlsUrl += '/p/' + partnerId + '/playManifest/format/applehttp';
			hlsUrl += '/entryId/' + entryId;

			var stitchedUrl = hlsUrl;

			hlsUrl += '/a/playlist.m3u8';
			stitchedUrl += '/usePlayServer/1/a/playlist.m3u8';

			var hdsUrl = location.protocol + '//';
			hdsUrl += location.host;
			hdsUrl += '/p/' + partnerId + '/playManifest/format/hds';
			hdsUrl += '/entryId/' + entryId;
			hdsUrl += '/a/a.f4m';

			var html = '<b>HLS URL:</b> <a href="' + hlsUrl + '" target="_tab">' + hlsUrl + '</a><br/>';
			html += '<b>Stitched URL:</b> <a href="' + stitchedUrl + '" target="_tab">' + stitchedUrl + '</a><br/>';
			html += '<b>HDS URL:</b> <a href="' + hdsUrl + '" target="_tab">' + hdsUrl + '</a>';
			$('#lblUrl').html(html);

			loadAdminPlayer(hdsUrl);
			loadUserPlayer(entryId, stitchedUrl);
			startSession();
		}

		function startSession(){
			var secret = $('#txtSecret').val();

			$.ajax(
				'/api_v3/index.php/service/session/action/start', {
				data: {
					format: 1,
					partnerId: partnerId,
					secret: secret,
					type: 2
				},
				error: function(jqXHR, textStatus, errorThrown){
					alert(errorThrown);
				},
				success: function(data, textStatus, jqXHR){
					if(data.code && data.message){
						alert(data.message);
						return;
					}
						
					ks = data;
				}
			});
		}

		function loadUserPlayer(entryId, url){
			mw.setConfig('Kaltura.LeadWithHTML5', true);
			mw.setConfig('EmbedPlayer.ForceKPlayer', true);
			mw.setConfig('LeadWithHLSOnFlash', true);

			mw.setConfig('EmbedPlayer.ReplaceSources', [{
				type : 'application/vnd.apple.mpegurl',
				src : url
			}]);
			
			kWidget.embed({
				sourceType: 'url', // TODO remove
				targetId: 'userPlayerContainer',
				wid: '_' + partnerId,
				uiconf_id: 11601127,
				entry_id: entryId,
				flashvars: {
					confFilePath: '{libPath}/modules/KalturaSupport/tests/confFiles/jsonConfig.json',
					autoPlay: true
				},
				cache_st: 1387835968
			});
		}

		function loadAdminPlayer(url){

            var swfVersionStr = '11.1.0';
            var xiSwfUrlStr = 'playerProductInstall.swf';
            
            var flashvars = {
            	url: url,
            	onSyncPoint: 'onSyncPoint'
            };
            
            var params = {
            	quality: 'high',
            	bgcolor: '#ffffff',
            	allowscriptaccess: 'sameDomain',
            	allowfullscreen: 'true'
            };
            
            var attributes = {
            	id: 'Player',
            	name: 'Player',
            	align: 'middle'
            };
            
            swfobject.embedSWF(
                'Player.swf', 'adminPlayerContainer', 
                '400', '333', 
                swfVersionStr, xiSwfUrlStr, 
                flashvars, params, attributes);
            swfobject.createCSS('#adminPlayerContainer', 'display:block;text-align:left;');
		}

		function onSyncPoint(metadata){
			var date = new Date();
			lastSyncPointTime = date.getTime();
			lastSyncPointOffset = metadata.offset;

			$('#btnSendAd').removeAttr('disabled');
			log('Ads Enabled last offset:' + lastSyncPointOffset);
		}
		
		function enableAds(){
			if(!ks){
				alert('kaltura API is not enabled');
				return;
			}

			var interval = $('#txtSyncPointInterval').val();
			var duration = $('#txtSyncPointDuration').val();
			var entryId = $('#txtEntryId').val();
			
			$.ajax(
				'/api_v3/index.php/service/liveStream/action/createPeriodicSyncPoints', {
				data: {
					format: 1,
					ks: ks,
					entryId: entryId,
					interval: interval,
					duration: duration
				},
				error: function(jqXHR, textStatus, errorThrown){
					alert(errorThrown);
				},
				success: function(data, textStatus, jqXHR){
					if(data && data.code && data.message){
						alert(data.message);
					}
				}
			});
		}

		function sendAd(){
			var date = new Date();
			var timeSinceLastSyncPoint = date.getTime() - lastSyncPointTime;
			var startTime = lastSyncPointOffset + timeSinceLastSyncPoint;

			var entryId = $('#txtEntryId').val();
			var adUrl = $('#txtAdUrl').val();
			var duration = $('#txtAdDuration').val();

			$.ajax(
				'/api_v3/index.php/service/cuePoint_cuePoint/action/add', {
				data: {
					format: 1,
					ks: ks,
					'cuePoint:objectType': 'KalturaAdCuePoint',
					'cuePoint:entryId': entryId,
					'cuePoint:startTime': startTime,
					'cuePoint:protocolType': 'VPAID',
					'cuePoint:sourceUrl': adUrl,
					'cuePoint:adType': 1, // VIDEO
					'cuePoint:title': 'Test Cue-Point',
					'cuePoint:duration': duration
				},
				error: function(jqXHR, textStatus, errorThrown){
					alert(errorThrown);
				},
				success: function(data, textStatus, jqXHR){
					if(data.code && data.message){
						alert(data.message);
						return;
					}

					log('Cue-Point created [' + data.id + '] startTime [' + startTime + '] timeSinceLastSyncPoint [' + timeSinceLastSyncPoint +']');
				}
			});
		}

		function log(log){
			$('#eraLog').val(log + '\n' + $('#eraLog').val());
		}
		
	</script>
</head>
<body>
<div id="main" style="position: static;">
	<div class="content">
		<div class="title">
			<h1>Big-Red-Button Demo</h1>
		</div>
		<div class="contwrap">
			<p></p>
			<table>
				<thead>
					<tr>
						<th>Admin Player</th>
						<th></th>
						<th>User Player</th>
				</thead>
				<tbody>
					<tr>
						<td><div id="adminPlayerContainer" style="width: 400px; height: 333px;" /></td>
						<td><textarea id="eraLog" style="width: 400px; height: 333px;"></textarea></td>
						<td><div id="userPlayerContainer" style="width: 400px; height: 333px;" /></td>
					</tr>
				</tbody>
			</table>
		</div><!-- end contwrap -->
	</div><!-- end content -->
	<div id="lblUrl"></div>
	<br/>
	<table>
		<tr>
			<td>Admin Secret:</td>
			<td><input type="text" id="txtSecret" value="<?php echo isset($_GET['secret']) ? $_GET['secret'] : ''; ?>" />
		</td>
		<tr>
			<td>Entry Id:</td>
			<td><input type="text" id="txtEntryId" value="<?php echo isset($_GET['entryId']) ? $_GET['entryId'] : ''; ?>" />
		</td>
		<tr>
			<td colspan="2">
				<input type="button" onclick="loadPlayer()" value="Load Player" />
			</td>
		</tr>
		<tr>
			<td colspan="2"><br/><br/></td>
		</tr>
		<tr>
			<td>Ad URL:</td>
			<td><input type="text" id="txtAdUrl" value="http://search.spotxchange.com/vast/2.00/79391?VPAID=1&content_page_url=[please_put_dynamic_page_url]&cb=[random_number]" />
		</td>
		<tr>
			<td>Ad Duration (milliseconds):</td>
			<td><input type="text" id="txtAdDuration" value="15000" />
		</td>
		<tr>
			<td colspan="2">
				<input id="btnSendAd" type="button" onclick="sendAd()" disabled="disabled" value="Big Red Button" style="background-color: red; height: 50px; font-size: 15pt;" />
			</td>
		</tr>
		<tr>
			<td colspan="2"><br/><br/></td>
		</tr>
		<tr>
			<td>Sync-Point Interval (seconds):</td>
			<td><input type="text" id="txtSyncPointInterval" value="30" />
		</td>
		<tr>
			<td>Sync-Point Duration (seconds):</td>
			<td><input type="text" id="txtSyncPointDuration" value="150" />
		</td>
		<tr>
			<td colspan="2">
				<input type="button" onclick="enableAds()" value="Enable Ads (Send Sync-Points)" />
			</td>
		</tr>
	</table>
</div><!-- end #main -->
</body>
</html>
