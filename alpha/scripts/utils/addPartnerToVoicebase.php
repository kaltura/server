<?php
	if($argc < 4)
	{
		die("Usage: php addVoicebaseParamsToPartner [kaltura base directory] [partner id] [apiKey] [apiPassword]" . PHP_EOL);
	}
	
	$currentWorkingEnv = $argv[1];
	if(!file_exists($currentWorkingEnv))
		die("input kaltura base directory \"$currentWorkingEnv\" does not exists");
	
	require_once($currentWorkingEnv . '/alpha/scripts/bootstrap.php');
	
	$currentWorkingEnv = $argv[1];
	$partnerId = $argv[2];
	$apiKey = $argv[3];
	$apiPassword = $argv[4];
	
	$plugin = new VoicebasePlugin();
	$options = new VoicebaseOptions($apiKey, $apiPassword);
	$plugin->setPartnerVoicebaseOptions($partnerId, $options);
	
