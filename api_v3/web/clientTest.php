<?php

if (array_key_exists('sleepTime', $_REQUEST))
{
	$sleepTime = $_REQUEST['sleepTime'];
	sleep(min($sleepTime, 3));
}

if (!array_key_exists('responseBuffer', $_REQUEST))
        die('responseBuffer parameter not specified !');

$responseBuffer = $_REQUEST['responseBuffer'];
if (strlen($responseBuffer) > 300)
	die('responseBuffer too big !');

if ($responseBuffer == '<xml>' || 
	(substr($responseBuffer, 0, 13) == '<xml><result>' && substr($responseBuffer, -15) == '</result></xml>'))
	echo htmlspecialchars($responseBuffer);
