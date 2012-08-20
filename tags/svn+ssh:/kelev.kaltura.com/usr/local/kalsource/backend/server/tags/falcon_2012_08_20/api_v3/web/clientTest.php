<?php

if (array_key_exists('sleepTime', $_REQUEST))
{
	$sleepTime = $_REQUEST['sleepTime'];
	sleep(min($sleepTime, 3));
}

if (!array_key_exists('responseBuffer', $_REQUEST))
        die('responseBuffer parameter not specified !');

echo $_REQUEST['responseBuffer'];
