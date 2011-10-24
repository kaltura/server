<?php
function formatDescription($description)
{
	$description = preg_replace('/{@link\s+\?object=(.+?)\s+(.+?)}/', '<a href="#" onclick="KDoc.openObject(\'\\1\')">\\2</a>', $description);
	$description = preg_replace('/{@link\s+(.+?)\s+(.+?)}/', '<a href="\\1" target="_blank">\\2</a>', $description);
	$description = nl2br($description);
	return $description;
}
