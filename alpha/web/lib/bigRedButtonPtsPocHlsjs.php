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
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <!-- The above 3 meta tags *must* come first in the head; any other head content must come *after* these tags -->
    <title>Kaltura</title>

    <!-- Bootstrap -->
    <link href="css/main.css" rel="stylesheet">	
	<link href='https://fonts.googleapis.com/css?family=Lato:400,700,300' rel='stylesheet' type='text/css'>
	
	<script src="/lib/js/jquery-1.8.3.min.js"></script>
	<!--script type="text/javascript" src="/html5/html5lib/<?php echo $html5Version; ?>/mwEmbedLoader.php"></script-->
	<script type="text/javascript" src="http://kgit.html5video.org/tags/v2.42.rc10/mwEmbedLoader.php"></script>
	<script type="text/javascript" src="swfobject.js"></script>
	<script>
		var partnerId = <?php echo $partnerId; ?>;
		var ks = null;
		var currentTime = null;
		var userTime = null;
		var queuedArray = [];

		var second = 1000;
		var minute = second * 60;
		var hour = minute * 60;
		var day = hour *24;

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

		function userPlayerUrl(){
			log('url', '#cpLog');
			var entryId = $('#txtEntryId').val();
			var uiConfId = $('#txtUiConfId').val();
			var url = location.protocol + '//';
			url += location.host + '/admin_console/tools/bigRedButtonPtsPocUserPlayer.php?partnerId='
			url += partnerId + '&entryId=0_oyfjxo3a' + '&uiConfId=15098051' + '&playerVersion=v2.39';
			log(url, '#cpLog');
			return url;
		}
		
		function loadUserPlayer(entryId, uiConfId){
            kWidget.embed({
                    targetId: 'userPlayerContainer',
                    wid: '_' + partnerId,
                    "uiconf_id": uiConfId,
                    "flashvars": {
                             "hlsjs": {
								"plugin": true
							},
							"LeadWithHLSOnJs": true,
                            "autoPlay": true,
							"Kaltura.Protocol":"http"
                    },
                    "cache_st": 1410340114,
                    "entry_id": entryId,
					"readyCallback": function( ) {
						var userVideo = $("#userPlayerContainer_ifp").contents().find(".persistentNativePlayer")[0];
						setInterval( function() {
							onUserTime(userVideo.currentTime);
						}, 500);
					}
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
							 "hlsjs": {
								 "plugin": true
							 },
						 	"LeadWithHLSOnJs": true
					 },
					 "cache_st": 1410340114,
					 "entry_id": entryId,
					 "readyCallback": function( ) {
						 var adminVideo = $("#adminPlayerContainer_ifp").contents().find(".persistentNativePlayer")[0];
						 setInterval( function() {
							 onAdminTime(adminVideo.currentTime);
						 }, 500);
					 }
			 });
		}
		
		function onAdminTime(t){
			if(t > 0){
				currentTime = t*1000;

				$('#btnSendAd').removeAttr('disabled');
				document.getElementById("adminTime").innerHTML = currentTime;
			}
		}

		function onUserTime(t) {
			if(t > 0){
				userTime = t*1000;
				document.getElementById("userTime").innerHTML = userTime;

				var timeDiff = -1;
				var historyList = document.getElementById("historyList");

				for (var i=queuedArray.length;i>0; i--) {
					var nextAdStartTime = queuedArray[i-1];
					timeDiff = nextAdStartTime - userTime;
					if ( timeDiff < 0 ) {
						queuedArray.pop();
						//move to history list
						historyList.appendChild(document.getElementById(nextAdStartTime));
					} else {
						//TODO we might need to multiply by 90 if player fix the PTS time
						var timeDiffInMS = Math.floor(timeDiff) ;
						document.getElementById("nextAdTime").innerHTML = getCountdownString(timeDiffInMS)
						break;
					}
				}
				if ( timeDiff < 0 )
					document.getElementById("nextAdTime").innerHTML = "..."
			}
		}

		function getCountdownString(millisecs)
		{
			var hours = Math.floor( (millisecs % day ) / hour );
			var minutes = Math.floor( (millisecs % hour) / minute );
			var seconds = Math.floor( (millisecs % minute) / second );

			return hours + ":" + minutes + ":" + seconds;
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

					var ul = document.getElementById("queueList");
					var li = document.createElement("li");
					li.id = startTime;
					var timeSpan = document.createElement('span')
					timeSpan.innerHTML = "Start time " + startTime;
					li.appendChild(document.createTextNode("Cue point created"));
					li.appendChild(timeSpan);
					ul.appendChild(li);

					queuedArray.unshift(startTime);
				}
			})
		}

	</script>
</head>
<body onload="loadPlayer()">
   <nav class="navbar navbar-default navbar-static-top">
      <div class="container">
        <div class="row">
          <div class="col col-xs-8">
            <span class="navbar-brand">SERVER SIDE AD INSERTION DEMO</span>
          </div>
          <div class="col col-xs-4 text-right">
            <img class="logo" src="images/kaltura.png">
          </div>
        </div>
      </div>
    </nav>
	<div class="videos-wrapper">
      <div class="container">
        <div class="row">
          <div class="col col-sm-6 video-col">
            <div class="video">
              <div id="adminPlayerContainer" class="video-content">
               </div>
            </div>
			<p><strong>Admin</strong> <span>PTS </span><span id="adminTime"> </span></p>
          </div>
          <div class="col col-sm-6 video-col">
            <div class="video">
              <div id="userPlayerContainer" class="video-content">
              </div>
            </div>
			  <p><strong>Audience</strong> <span>PTS </span><span id="userTime"> </span></p>
          </div>
        </div>
      </div>


		<div class="row buttons-row">
			<div class="col-xs-6">
				<a class="btn btn-primary btn-rounded btn-lg" id="btnSendAd" disabled="disabled" onclick="sendAd()">+ Insert Ad</a>
			</div>
			<div class="col-xs-6">
				<span class="btn btn-default btn-rounded next-ad-label btn-lg">Next ad in <span id="nextAdTime"> ... </span></span>
			</div>
		</div>

    </div>

   <div class="container">
	   <div class="row">
		   <div class="col col-sm-offset-1 col-sm-5">
			   <p class="section-title">Queue</p>
			   <ul id="queueList" class="list">
			   </ul>
		   </div>
		   <div class="col col-sm-5">
			   <p class="section-title">History</p>
			   <ul id="historyList" class="list">
			   </ul>
		   </div>
	   </div>
   </div>
<div id="main" style="position: static;">
	  	<table>
		<tr style="display: none; ">
			<td>Admin Secret:</td>
			<td><input type="text" id="txtSecret" value="<?php echo isset($_GET['secret']) ? $_GET['secret'] : ''; ?>" />
		</td>
		<tr style="display: none; ">
			<td>Entry Id:</td>
			<td><input type="text" id="txtEntryId" value="<?php echo isset($_GET['entryId']) ? $_GET['entryId'] : ''; ?>" />
		</td>
		<tr style="display: none; ">
        	<td>Admin uiConf Id:</td>
            <td><input type="text" id="txtAdminUiConfId" value="<?php echo isset($_GET['adminUiConfId']) ? $_GET['adminUiConfId'] : ''; ?>" />
		</td>
		<tr style="display: none; ">
        	<td>User uiConf Id:</td>
            <td><input type="text" id="txtUiConfId" value="<?php echo isset($_GET['uiConfId']) ? $_GET['uiConfId'] : ''; ?>" />
		</td>
		<tr style="display: none; ">
			<td colspan="2">
				<input type="button" onclick="loadPlayer()" value="Load Player" />
			</td>
		</tr>
		<tr style="display: none; ">
			<td colspan="2"><br/><br/></td>
		</tr>
		<tr style="display: none; ">
			<td>Ad URL:</td>
			<td><input type="text" id="txtAdUrl" value="http://projects.kaltura.com/vast/vast11.xml" />
		</td>
		<tr style="display: none; ">
			<td>Ad Duration (milliseconds):</td>
			<td><input type="text" id="txtAdDuration" value="30000" />
		</td>
	</table>
</div><!-- end #main -->
</body>
</html>
