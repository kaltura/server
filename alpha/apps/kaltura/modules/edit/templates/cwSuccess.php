<html>
<head>

<style type="text/css">
	html, body{ margin:0; padding:0; }
</style>
<script type='text/javascript'>
function addentry ()
{
	finished( '1' );
}

function finished ( modified )
{
	if ( modified == '0' ) 
	{ 
		window.top.kalturaCloseModalBox();
	}
	else 
	{ 
		window.top.kalturaRefreshTop();
	}
	return;
}


</script>
</head>
<body>
<?

	$domain = $widget_host;
	if ( strpos ( $domain , "localhost"  ) !== false )		$host = 2;
	elseif ( strpos ( $domain , "kaldev" ) !== false ) 		$host = 0;
	else													$host = 1;

	$swf_url = "/swf/ContributionWizard.swf";
		
    $lang = "en" ;
	$height = 360;
	$width = 680;
	$flashvars = 		'userId=' . $uid .
						'&sessionId=' . $ks. 
						'&partnerId=' . $partner_id .
						'&subPartnerId=' . $subp_id . 
						'&kshow_id=' . $kshow_id . 
						'&host=' . $host . //$domain; it's an enum
						'&afterAddentry=addentry' .
						'&close=finished' .
						'&lang=' . $lang . 
						'&terms_of_use=http://www.kaltura.com/index.php/static/tandc' ;

	$str = "";
							
    $extra_links  = "<a href='javascript:addentry()'>addentry<a><br> " ;
    
					
    $widget = '<object id="kaltura_contribution_wizard" type="application/x-shockwave-flash" allowScriptAccess="always" allowNetworking="all" height="' . $height . '" width="' . $width . '" data="'.$domain. $swf_url . '">'.
			'<param name="allowScriptAccess" value="always" />'.
			'<param name="allowNetworking" value="all" />'.
			'<param name="bgcolor" value=#000000 />'.
			'<param name="movie" value="'.$domain. $swf_url . '"/>'.
    		'<param name="flashVars" value="' . $flashvars . '" />' .
			'</object>';
			
	echo $widget;

	//echo "<pre style='color:white'>" . print_r ( explode ( "&" , $flashvars ) , true ) . "</pre>";
?>			
</body>
</html>
