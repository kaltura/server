<?php
	require_once(__dir__ . '/../bootstrap.php');	

	if($argc < 4)
	{
		die("Usage: php " . basename(__FILE__) . " [partner id] [apiKey] [apiPassword]" . PHP_EOL);
	}
	
	$partnerId = $argv[1];
	$apiKey = $argv[2];
	$apiPassword = $argv[3];
	
	$options = new VoicebaseOptions($apiKey, $apiPassword);
	VoicebasePlugin::setPartnerVoicebaseOptions($partnerId, $options);
	
