<?php

$clientConfig = null;
/* @var $clientConfig KalturaConfiguration */

require_once __DIR__ . '/lib/init.php';

$adminUrl = $clientConfig->serviceUrl . 'admin_console';
$adminHtmlContent = file_get_contents($adminUrl);
if(!$adminHtmlContent)
{
	echo "Fetching URL [$adminUrl] failed\n";
	exit(-1);
}

exit(0);