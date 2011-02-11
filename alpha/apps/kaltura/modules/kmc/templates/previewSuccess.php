<?php 

if( $uiConf ) {
    echo $uiConf->width;
    echo '<pre>';
    print_r($uiConf);
    exit();
}

// Create swf url
$swfUrl = "http://". $partner_host ."/index.php/kwidget";
$swfUrl .= "/cache_st/" . time()+(60*15);
$swfUrl .= "/wid/_" . $partner_id;
$swfUrl .= "/uiconf_id/" . $uiconf_id;
$swfUrl .= "/entry_id/" . $entry_id;

$thumbnailUrl = "http://". $partner_cdnHost ."/p/". $partner_id ."/sp/". $partner_id ."00/thumbnail". $entry_id ."/width/120/height/90/bgcolor/000000/type/2";
?>
<!doctype html>
<html>
<head>
	<meta charset="utf-8">
	<meta http-equiv="X-UA-Compatible" content="IE=edge,chrome=1">
	<meta name="viewport" content="width=device-width, initial-scale=1.0">
	<title>Kaltura Player</title>
	<script src="/html5/html5lib/v<?php echo $html5_version; ?>/mwEmbedLoader.php"></script>
</head>
<body>
	<div id="main">

		<div class="content">
			<div class="title">
				<h2>Kaltura Player</h2>
			</div>
			<div class="contwrap">
<?php /*
			<div style="width:300px;">
				<span style="font-size: 16px;">Entry id:</span> <input style="width: 100px;" id="kentryid" value="<?php echo $sf_params->get('e');?>" /> 
				<input style="width: 100px; font-size: 12px;" id="showVideo" type="button" value="Change entry" disabled="true">
			</div><br />
*/?>
			<div id="videoContainer" style="width:400px">
			    <object id="kaltura_player" name="kaltura_player" type="application/x-shockwave-flash" 
				    allowFullScreen="true" allowNetworking="all" allowScriptAccess="always" height="333" width="400" 
				    xmlns:dc="http://purl.org/dc/terms/" 
				    xmlns:media="http://search.yahoo.com/searchmonkey/media/" 
				    rel="media:video" 
				    resource="<?php echo $swfUrl; ?>" 
				    data="<?php echo $swfUrl; ?>">
				<param name="allowFullScreen" value="true" />
				<param name="allowNetworking" value="all" />
				<param name="allowScriptAccess" value="always" />
				<param name="bgcolor" value="#000000" />
				<param name="flashVars" value="&" />
				<param name="movie" value="<?php echo $swfUrl; ?>" />
				<a href="http://corp.kaltura.com">video platform</a> 
				<a href="http://corp.kaltura.com/video_platform/video_management">video management</a> 
				<a href="http://corp.kaltura.com/solutions/video_solution">video solutions</a> 
				<a href="http://corp.kaltura.com/video_platform/video_publishing">video player</a> 
				<a rel="media:thumbnail" href="<?php echo $thumbnailUrl; ?>"></a>
				<span property="dc:description" content=""></span>
				<span property="media:title" content="Kaltura Video"></span>
				<span property="media:width" content="400"></span>
				<span property="media:height" content="333"></span> 
				<span property="media:type" content="application/x-shockwave-flash"></span> 
			   </object>
			</div>

			</div><!-- end contwrap -->
		</div><!-- end content -->
	</div><!-- end #main -->
<?php /*
			<iframe src="/html5/html5lib/v1.2/mwEmbedFrame.php/entry_id//wid/_<?php echo $sf_params->get('partner_id'); ?>/uiconf_id/<?php echo $sf_params->get('u'); ?>/p/<?php echo $sf_params->get('p'); ?>" width="400" height="300" frameborder="0"></iframe>
<script type="text/javascript">
var partnerId = <?php echo $sf_params->get('p'); ?>;
var uiConf = <?php echo $sf_params->get('u'); ?>;

mw.ready( function(){

	mw.setConfig( 'EmbedPlayer.OverlayControls', false );
	
	$j('#showVideo').attr('disabled', null);
	
	$j('#showVideo').click( function(){		
		var entryId =  $j( '#kentryid' ).val();
		kalturaIframeEmbed('videoContainer', { 
			entry_id: entryId, 
			wid: '_' + partnerId,
			uiconf_id: uiConf,
			p: partnerId 
		}, {
			hash: mw.getKalturaIframeHash(), 
			width: 400,
			height: 300 
		} );
	});

	$j('#showVideo').trigger('click');
	
} );
</script>
	*/ ?>
</body>
</html>