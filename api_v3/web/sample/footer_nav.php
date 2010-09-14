<?php
	$pathInfo = pathinfo($_SERVER["PHP_SELF"]);
	$dirName = $pathInfo["dirname"];
	$subDir = (strpos($dirName, "sample") === (strlen($dirName) - strlen("sample")) ? false : true);
	if ($subDir)
		$backPath = "../";
	else
		$backPath = "";
?>
<ul class="footerNav">                      
	<li><a href="<?echo $backPath; ?>players/">Players</a></li>
	<li><a href="<?echo $backPath; ?>playlists/">Playlists</li>
	<li><a href="<?echo $backPath; ?>">UGC site</li>
	<li><a href="<?echo $backPath; ?>advanced_editor/player.php">Video Editor</li>
</ul>