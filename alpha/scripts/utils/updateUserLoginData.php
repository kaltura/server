<?php

chdir(__DIR__ . '/../');
require_once(__DIR__ . '/../bootstrap.php');

// add google authenticator library to include path
set_include_path(get_include_path() . PATH_SEPARATOR.KALTURA_ROOT_PATH . '/vendor/phpGangsta/');
require_once 'GoogleAuthenticator.php';

$criteria = KalturaCriteria::create(kuserPeer::OM_CLASS);
$criteria->add(kuserPeer::PARTNER_ID, -2);

$kusers = kuserPeer::doSelect($criteria);
foreach ($kusers as $kuser)
{
	/*@var $kuser kuser */
	$userLoginData = $kuser->getLoginData();
	KalturaLog::info ("setting user hash for user: " . $kuser->getPuserId());
	$userLoginData->setSeedFor2FactorAuth(GoogleAuthenticator::createSecret());
	$userLoginData->save();
	
}
