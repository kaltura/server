<?php

if($argc != 2)
{
	echo "Arguments missing.\n\n";
	echo "Usage: php {$argv[0]} <vendor account id>\n";
	exit;
}

$vendorAccountId = $argv[1];

require_once(__DIR__ . '/../bootstrap.php');

$zoomIntegration = ZoomHelper::getZoomIntegrationByAccountId($vendorAccountId);
ZoomHelper::verifyZoomIntegration($zoomIntegration);
$accessToken = kZoomOauth::getValidAccessToken($zoomIntegration);
echo $accessToken;