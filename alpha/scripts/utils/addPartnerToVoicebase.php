<?php
	require_once(__dir__ . '/../bootstrap.php');

	if($argc < 4)
	{
		die("Usage: php " . basename(__FILE__) . " [partner id] [apiKey] [apiPassword] <[transformDfxp]>" . PHP_EOL);
	}

	$partnerId = $argv[1];
	$apiKey = $argv[2];
	$apiPassword = $argv[3];
	$transformDfxp = isset($argv[4]) ? (bool)$argv[4] : false;

	$options = new VoicebaseOptions($apiKey, $apiPassword);
	$options->transformDfxp = $transformDfxp;
	VoicebasePlugin::setPartnerVoicebaseOptions($partnerId, $options);
	
