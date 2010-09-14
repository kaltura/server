<?php

$error = null;

$back_url = "http" . ( @$_SERVER['HTTPS'] == "on" ? "s" : "" ) . "://" . $_SERVER['SERVER_NAME'] . $_SERVER['REQUEST_URI']; 

?>
<html>
<head>
<script type='text/javascript'>
function gotoCW ( kshow_id ) 
{  
	kalturaInitModalBox ( "./cw?uid=<? echo $uid ?>&kshow_id=" + kshow_id ) ;
}

function gotoEditor ( kshow_id ) 
{ 
	window.location = "./openEditor?kshow_id=" + kshow_id + "&back_url=" + window.location;
	//return;
}

</script>
	
</head>
<body>
<div style="font-family:verdana; font-size: 13px; width: 80%;">
<br />
<h2>Widget Page</h2>
<? echo $error ?>
Widget for <? echo $kshow_id ?>:<br /><br/>

<?
$widget_html = myKshowUtils::createGenericWidgetHtml ( $partner_id , $subp_id , $partner_name , requestUtils::getHost() ,  $kshow_id , $uid  ); 
?>
<div style="">
<? echo $widget_html ?>
</div>

</body>
</html>
