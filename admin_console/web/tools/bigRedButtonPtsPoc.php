<?php 
if(!isset($_GET['partnerId']))
	die('partnerId must be supplied in query string');
	
if(!isset($_GET['playerVersion']))
	die('html5 lib version must be supplied in query string');

$partnerId = $_GET['partnerId'];

$html5Version = $_GET['playerVersion'];
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
		var currentTime = null;

		function loadPlayer(){
			var entryId = $('#txtEntryId').val();
			var uiConfId = $('#txtUiConfId').val();
			var adminUiConfId = $('#txtAdminUiConfId').val();

			mw.setConfig('Kaltura.LeadWithHTML5', true);
			loadAdminPlayer(entryId, adminUiConfId);
			loadUserPlayer(entryId, uiConfId);
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

		function loadUserPlayer(entryId, uiConfId){
            kWidget.embed({
                    targetId: 'userPlayerContainer',
                    wid: '_' + partnerId,
                    "uiconf_id": uiConfId,
                    "flashvars": {
                            "streamerType": "auto",
                            "autoPlay": true,
                            "LeadWithHLSOnFlash": true
                    },
                    "cache_st": 1410340114,
                    "entry_id": entryId
            });
		}

		function loadAdminPlayer(entryId, uiConfId){
		     kWidget.embed({
					 targetId: 'adminPlayerContainer',
					 wid: '_' + partnerId,
					 "uiconf_id": uiConfId,
					 "flashvars": {
							 "streamerType": "auto",
							 "autoPlay": true,
							 "hlsLogs": true,
							 "LeadWithHLSOnFlash": true
					 },
					 "cache_st": 1410340114,
					 "entry_id": entryId
			 });
		}
		
		function onCurrentTime(t, isAbsolute){
			if(t > 0){
				currentTime = t*1000;
				$('#btnSendAd').removeAttr('disabled');
			}
			log("current time: " + currentTime, '#eraLog');
		}

		function sendAd(){			
			var entryId = $('#txtEntryId').val();
			var adUrl = $('#txtAdUrl').val();
			var duration = $('#txtAdDuration').val();

			var startTime  = currentTime;

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

					log('Cue-Point created [' + data.id + '] startTime [' + startTime + ']', '#cpLog');
				}
			})
		}

		function log(log, element){
			$(element).val(log + '\n' + $(element).val());
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
						<td><textarea id="cpLog" style="width: 400px; height: 333px;"></textarea></td>
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
        	<td>Admin uiConf Id:</td>
            <td><input type="text" id="txtAdminUiConfId" value="<?php echo isset($_GET['adminUiConfId']) ? $_GET['adminUiConfId'] : ''; ?>" />
		</td>
		<tr>
        	<td>User uiConf Id:</td>
            <td><input type="text" id="txtUiConfId" value="<?php echo isset($_GET['uiConfId']) ? $_GET['uiConfId'] : ''; ?>" />
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
			<td><input type="text" id="txtAdUrl" value="http://projects.kaltura.com/vast/vast10.xml" />
		</td>
		<tr>
			<td>Ad Duration (milliseconds):</td>
			<td><input type="text" id="txtAdDuration" value="5000" />
		</td>
		<tr>
			<td colspan="2">
				<input id="btnSendAd" type="button" disabled="disabled" onclick="sendAd()" value="Insert Ad" style="background-color: red; height: 50px; font-size: 15pt;" />
			</td>
		</tr>
		<tr>
			<td colspan="2"><br/><br/></td>
		</tr>
		<tr>
			<td><textarea id="eraLog" style="width: 500px; height: 333px;"></textarea></td>
		</tr>

	</table>
</div><!-- end #main -->
</body>
</html>
