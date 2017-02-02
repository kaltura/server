<?php
	if($argc < 4)
		die("Usage: php " . basename(__FILE__) . " [partner id] [username] [password] {baseUrl} <[transformDfxp]>" . PHP_EOL);

	require_once(__dir__ . '/../bootstrap.php');	
	
	$partnerId = $argv[1];
	$username = $argv[2];
	$password = $argv[3];
	$baseUrl = isset($argv[4]) ? $argv[4] : null;
	$transformDfxp = isset($argv[5]) ? (bool)$argv[5] : false;
	$defaultParams = isset($argv[6]) ? $argv[6] : "";

	$partner = PartnerPeer::retrieveByPK($partnerId);

	if($partner->getKsMaxExpiryInSeconds() < kIntegrationFlowManager::THREE_DAYS_IN_SECONDS)
	{
		$partner->setKsMaxExpiryInSeconds(kIntegrationFlowManager::THREE_DAYS_IN_SECONDS);
		$partner->save();
	}
	
	$options = new Cielo24Options($username, $password, $baseUrl);
	$options->transformDfxp = $transformDfxp;
	$options->defaultParams = $defaultParams;
	Cielo24Plugin::setPartnerCielo24Options($partnerId, $options);
	
