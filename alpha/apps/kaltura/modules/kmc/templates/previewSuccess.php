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
	<script src="/html5/html5lib/v1.2/mwEmbedLoader.php"></script>
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
			<div id="videoContainer" style="width:400px"></div>
			<iframe src="/html5/html5lib/v1.2/mwEmbedFrame.php/entry_id/<?php echo $sf_params->get('e');?>/wid/_<?php echo $sf_params->get('p'); ?>/uiconf_id/<?php echo $sf_params->get('u'); ?>/p/<?php echo $sf_params->get('p'); ?>" width="400" height="300" frameborder="0"></iframe>

			</div><!-- end contwrap -->
		</div><!-- end content -->
	</div><!-- end #main -->
<?php /*
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