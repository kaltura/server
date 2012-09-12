<?php
	
/* @var $entry entry */
/* @var $widget widget */
/* @var $uiConf uiConf */

?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" lang="en" xml:lang="en">
<head prefix="og: http://ogp.me/ns# fb: http://ogp.me/ns/fb# video: http://ogp.me/ns/video#">

<meta http-equiv="Content-Type" content="text/html; charset=UTF-8"/>
<!-- Open Graph meta tags -->
<meta property="og:type" content="video.movie"> 
<meta property="og:title" content="<?php echo $entry->getName(); ?>" />
<meta property="og:description" content="<?php echo $entry->getDescription(); ?>"  />
<meta property="og:image" content="<?php echo $entryThumbUrl; ?>"/>
<meta property="og:image:secure_url" content="<?php echo $entryThumbSecureUrl; ?>" />
<meta property="og:video" content="<?php echo $widgetUrl; ?>" />
<meta property="og:video:secure_url" content="<?php echo $widgetSecureUrl; ?>" />
<meta property="og:height" content="<?php echo $uiConf->getHeight(); ?>" />
<meta property="og:width" content="<?php echo $uiConf->getWidth(); ?>" />
<meta property="og:video:type" content="application/x-shockwave-flash" />

    </head>
	<body>

<div id="player_container" ></div> 
<script src="/lib/js/flashembed.js"></script> 
<script type="text/javascript"> 

//Define Player Parameters
  var playerConfig = {
	'partnerId' : '<?php echo $widget->getPartnerId(); ?>', 
	'uiconfId' : '<?php echo $uiConf->getId(); ?>', 
	'entryId' : '<?php echo $entry->getId(); ?>', 
	'playerWidth' : <?php echo $uiConf->getWidth(); ?>,
	'playerHeight' : <?php echo $uiConf->getHeight(); ?>
	
  }

//Include HTML5 library
var includeUrl = "http://<?php echo kConf::get('www_host'); ?>/p/"+playerConfig['partnerId']+"/sp/"+playerConfig['partnerId']+"00/embedIframeJs/uiconf_id/"+playerConfig['uiconfId']+"/partner_id/"+playerConfig['partnerId'];
document.write('<scr' + 'ipt src="'+includeUrl+'"></scr' + 'ipt>');
//build embed code
var embedCodeConfig = {
	//This is the base config
	'src' : "http://<?php echo kConf::get('www_host'); ?>/index.php/kwidget/cache_st/1322734888791/wid/_" + playerConfig.partnerId + "/uiconf_id/" + playerConfig.uiconfId + "/entry_id/"  + playerConfig.entryId, 
	'flashvars' : {	
		externalInterfaceDisabled : "false"
		}
};
// No need to modify anything beyond this point
</script> 
<script type="text/javascript"> 
		flashembed("player_container",
				{	// attributes and params:
					id :				"kaltura_player",
					src : 				embedCodeConfig.src, 
					height :			playerConfig.playerHeight,
					width :				playerConfig.playerWidth,
					bgcolor :			"#000000",
					allowNetworking : 	"all",
					version :			[10,0],
					expressInstall :	"http://cdn.kaltura.org/apis/seo/expressinstall.swf"
				},
				embedCodeConfig.flashvars
		);
</script> 
</body> 
</html>
