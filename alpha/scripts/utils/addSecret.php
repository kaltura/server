<?php

require_once(__DIR__ . '/../bootstrap.php');
error_reporting(E_ERROR | E_WARNING | E_PARSE);

KalturaLog::info('add admin secret start' . PHP_EOL);

if ($argc <= 1)
	die(PHP_EOL . 'php' . "Usage: $argv[0] <partnerId> [<replacePrimarySecret>] [<adminSecret>]". PHP_EOL .
		"<partnerId> - the Partner ID" . PHP_EOL .
		"<replacePrimarySecret> - OPTIONAL - if set and equal to true replace primary secret with new Secret" . PHP_EOL .
		"<adminSecret> - OPTIONAL -  if set And MD5 validation Pass will be the new secret
 									if not exist a new secret will be randomly generated, otherwise throws Exception" . PHP_EOL
	);

$replacePrimaryAdminSecret = false;

if (isset($argv[2]) && $argv[2] === 'true')
	$replacePrimaryAdminSecret = true;

if (isset($argv[3]))
{
	if (!preg_match('/^[a-f0-9]{32}$/', $argv[3]))
		die('Error ' .$argv[3] . ' ->  Not a valid MD5 hash' . PHP_EOL);
	$newSecret = $argv[3];
}
else
	$newSecret = md5(KCryptoWrapper::random_pseudo_bytes(16));




$partnerId = $argv[1];



$partner = PartnerPeer::retrieveByPK($partnerId);

//in case we need to replace primary admin secret
if ($replacePrimaryAdminSecret)
{
	$oldSecret = $partner->getAdminSecret();
	/** @var array $additionalEnableSecrets */
	$additionalEnableSecrets = $partner->getEnabledAdditionalAdminSecrets();
	array_unshift($additionalEnableSecrets, $oldSecret);
	$partner->setAdminSecret($newSecret);
	$partner->setEnabledAdditionalAdminSecrets($additionalEnableSecrets);
	KalturaLog::info('primary admin Secret replaced' . PHP_EOL);
}
else
{
	$additionalEnableSecrets = $partner->getEnabledAdditionalAdminSecrets();
	$additionalEnableSecrets[] = $newSecret;
	$partner->setEnabledAdditionalAdminSecrets($additionalEnableSecrets);
	KalturaLog::info('new secret was added to additional admin secrets'. PHP_EOL);
}

try
{
	$partner->save();
}
catch (PropelException $e)
{
	die($e->getMessage());
}

die(PHP_EOL . 'New secret were added to partner:  ' . $partnerId . PHP_EOL);
