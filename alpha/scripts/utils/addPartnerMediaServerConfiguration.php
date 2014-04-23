<?php
/**
 * add sub partner to existing partner group (as adding it to the master partner's group). 
 */

require_once(__DIR__ . '/../bootstrap.php');

if ($argc < 3)
{
	die ("Script usage: php addPartnerMediaServerConfiguration.php <partner id> <configuration ini file location>");
}

$partnerId = $argv[1];
$partner = PartnerPeer::retrieveByPK($partnerId);

if (!$partner)
{
	die ("Partner with id [$partnerId] not found");
}

$configFilePath = $argv[2];
$configuration = parse_ini_file($configFilePath, true);

if (!$configuration)
{
	die ("Error parsing config file at [$configFilePath]");
}


$partner->setMediaServersConfiguration($configuration);
$partner->save();