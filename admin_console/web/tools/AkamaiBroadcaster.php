<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="en" lang="en">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1" />
<title>AkamaiBroadcaster_v1.1</title>
<script language="javascript">AC_FL_RunContent = 0;</script>
<script src="../js/AC_RunActiveContent.js" language="javascript"></script>
</head>
<body bgcolor="#ffffff">
<!--url's used in the movie-->
<!--text used in the movie-->
<!-- saved from url=(0013)about:internet -->
<script language="javascript">

	
	var flashVars = 'cpcode=<?php echo $_GET['streamUsername']; ?>';
	flashVars += '&passwd=<?php echo $_GET['streamPassword']; ?>';
	flashVars += '&streamname=<?php echo $_GET['entryId']; ?>@<?php echo $_GET['streamRemoteId']; ?>';
	flashVars += '&primaryep=p.ep<?php echo $_GET['streamRemoteId']; ?>.i.akamaientrypoint.net';
	flashVars += '&backupep=b.ep<?php echo $_GET['streamRemoteBackupId']; ?>.i.akamaientrypoint.net';
	

	if (AC_FL_RunContent == 0) {
		alert("This page requires AC_RunActiveContent.js. In Flash, run \"Apply Active Content Update\" in the Commands menu to copy AC_RunActiveContent.js to the HTML output folder.");
	} else {
		AC_FL_RunContent(
			'codebase', 'http://download.macromedia.com/pub/shockwave/cabs/flash/swflash.cab#version=7,0,0,0',
			'width', '760',
			'height', '744',
			'src', 'AkamaiBroadcaster_v1.1',
			'quality', 'best',
			'pluginspage', 'http://www.macromedia.com/go/getflashplayer',
			'align', 'middle',
			'play', 'true',
			'loop', 'false',
			'scale', 'showall',
			'wmode', 'window',
			'devicefont', 'false',
			'id', 'AkamaiBroadcaster_v1.1',
			'bgcolor', '#ffffff',
			'name', 'AkamaiBroadcaster_v1.1',
			'menu', 'true',
			'allowScriptAccess','sameDomain',
			'movie', 'AkamaiBroadcaster_v1.1',
			'salign', '',
			'flashvars', flashVars
			); //end AC code
	}
</script>
</body>
</html>
