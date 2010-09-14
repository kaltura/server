<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="en" lang="en">
<head>
	<meta http-equiv="Content-Type" content="text/html; charset=utf-8">
	<title>Kaltura Editor</title>
	<script type="text/javascript">
	<!--
		var kshow_id = '<? echo $kshow_id ?>';
		var entry_id = '<? echo $entry_id ?>';
		var backUrl = <? echo json_encode($backUrl); ?>;
		
		function getKshowId()
		{
			return kshow_id;
		}
		
		function getEntryId()
		{
			return entry_id;
		}
		
		function getPartnerInfo ()
		{
			obj = new Object;
<? 
foreach ( $vars as $name => $value  )	
{ 
	echo "			obj[\"$name\"]=\"$value\";"  . "\n" ;
}
?>
			return 	obj;
		}	
	
		
		function browseBack( modified )
		{
<? if ( $back_url ) { ?>
			modified_str = ""; 
			if ( ! modified ) modified_str = "<? echo strpos ( $back_url , "?" ) > 0 ? "&" : "?" ?>kaltura_modified=<? echo $kshow_id ?>";
		 			
<? if ( $navigate_top ) { ?>
		window.top.location='<? echo  $back_url ?>' + modified_str;
<? } else { ?>
			document.location='<? echo  $back_url ?>' + modified_str;
<? } ?>				
<? } else { ?>		
			if (kshow_id)
				document.location = "/index.php/browse?kshow_id=" + kshow_id;
			else
				document.location = "/";
<? } ?>				
		}
	//-->
	</script>
</head>
<body>
	<object classid="clsid:D27CDB6E-AE6D-11cf-96B8-444553540000"
		id="keditor" width="100%" height="100%"
		type="application/x-shockwave-flash"
		data="http://cdn.kaltura.com/flash/kae/v1.0.3/keditor.swf"
		codebase="http://fpdownload.macromedia.com/get/flashplayer/current/swflash.cab">
		<param name=movie value="http://cdn.kaltura.com/flash/kae/v1.0.3/keditor.swf" />
		<param name="quality" value="best" />
		<param name="bgcolor" value="#000000" />
		<param name="allowScriptAccess" value="Always" />
		<param name="allowFullScreen" value="true" />
		<param name="flashVars" value="OnCreationCompleteF=onLoadMovie&host=<?= $host ?>" />
		<embed 
			src="http://cdn.kaltura.com/flash/kae/v1.0.3/keditor.swf" 
			bgcolor="#000000"
			width="100%" 
			height="100%" 
			name="keditor"
			id="keditor"
			align="middle"
			play="true"
			loop="false"
			quality="best"
			allowScriptAccess="Always"
			allowFullScreen="true"
			flashVars="OnCreationCompleteF=onLoadMovie&host=<?= $host ?>"
			type="application/x-shockwave-flash"
			pluginspage="http://www.adobe.com/go/getflashplayer">
		</embed>
	</object>
</body>
</html>