<?php
function formatDescription($description)
{
	$description = preg_replace('/{@link\s+(.+?)\s+(.+?)}/', '<a href="\\1">\\2</a>', $description);
	$description = nl2br($description);
	return $description;
}

function extractPluginNameFromPackage($package)
{ 
	if(!is_string($package))
		return null;
		
	$packages = explode('.', $package, 2);
	if(count($packages) != 2 || $packages[0] != 'plugins')
		return null;
		
	return $packages[1];
}

?>