<?php 
if( ! $sf_params->has('p') || $sf_params->has['e'] ) { 
	die('Missing entry id or partner id');	
}
?>
<!doctype html>
<html>
<head>
	<meta charset="utf-8">
	<meta http-equiv="X-UA-Compatible" content="IE=edge,chrome=1">
	<meta name="viewport" content="width=device-width, initial-scale=1.0">
	<title>Kaltura Player</title>
</head>
<body>
	<div id="main">

		<div class="content">
			<div class="title">
				<h2>Kaltura Player</h2>
			</div>
			<div class="contwrap">
			
			<div style="width:300px;">
				<span style="font-size: 16px;">Entry id:</span> <input style="width: 100px;" id="kentryid" value="<?php echo $sf_params->get('e');?>" /> 
				<input style="width: 100px; font-size: 12px;" id="showVideo" type="button" value="Change entry" disabled="true">
			</div><br />

			<div id="videoContainer" style="width:400px">
				
			</div>

			</div><!-- end contwrap -->
		</div><!-- end content -->
	</div><!-- end #main -->

<!-- <script type="text/javascript" src="http://html5.kaltura.org/js"></script> -->
<script src="/html5/html5lib/v1.0/mwEmbedLoader.php"></script> 
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
</body>
</html>