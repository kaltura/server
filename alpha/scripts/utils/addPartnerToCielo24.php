<?php
	if($argc < 4)
		die("Usage: php " . basename(__FILE__) . " [partner id] [username] [password] {baseUrl} <[transformDfxp]>" . PHP_EOL);

	require_once(__dir__ . '/../bootstrap.php');	
	
	$partnerId = $argv[1];
	$username = $argv[2];
	$password = $argv[3];
	$baseUrl = isset($argv[4]) ? $argv[4] : null;
	$transformDfxp = isset($argv[5]) ? (bool)$argv[5] : null;
	$priority = isset($argv[6]) ? $argv[6] : null;
	$defaultParams = isset($argv[7]) ? $argv[7] : null;

	$partner = PartnerPeer::retrieveByPK($partnerId);

	if($partner->getKsMaxExpiryInSeconds() < kIntegrationFlowManager::THREE_DAYS_IN_SECONDS)
	{
		$partner->setKsMaxExpiryInSeconds(kIntegrationFlowManager::THREE_DAYS_IN_SECONDS);
		$partner->save();
	}

	$options = Cielo24Plugin::getPartnerCielo24Options($partnerId);
	KalturaLog::debug('Current options: '.json_encode($options));

	if (is_null($options)) {
		$options = new Cielo24Options($username, $password, $baseUrl);
	}
	$options->username = $username;
	$options->password = $password;

	if (!is_null($baseUrl)) {
		$options->baseUrl = $baseUrl;
	}

	if (!is_null($transformDfxp)) {
		$options->transformDfxp = $transformDfxp;
	}

	if (!is_null($priority))
	{
		$options->priority = $priority;
	}

	if (!is_null($defaultParams)) {
		$options->defaultParams = $defaultParams;
	}

	KalturaLog::debug('Setting options to: '.json_encode($options));
	Cielo24Plugin::setPartnerCielo24Options($partnerId, $options);
