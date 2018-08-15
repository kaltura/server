<?php

require_once(__DIR__ . '/../bootstrap.php');
error_reporting(E_ERROR | E_WARNING | E_PARSE);

echo 'add admin secret start' . PHP_EOL;

if ($argc <= 1)
	die('first argument must exist and be equal  partner ID' . PHP_EOL);

ob_start();
$replacePrimaryAdminSecret = false;

if (isset($argv[2]) && $argv[2] === 'true')
	$replacePrimaryAdminSecret = true;

$partnerId = $argv[1];

$newSecret = md5(KCryptoWrapper::random_pseudo_bytes(16));

$partner = PartnerPeer::retrieveByPK($partnerId);

//in case we need to replace primary admin secret
if ($replacePrimaryAdminSecret)
{
	$oldSecret = $partner->getAdminSecret();
	$additionalEnableSecrets = $partner->getEnabledAdditionalAdminSecrets();
	array_unshift($additionalEnableSecrets, $oldSecret);
	$partner->setEnabledAdditionalAdminSecrets($additionalEnableSecrets);
	$partner->setAdminSecret($newSecret);
	ob_end_clean();
	echo 'primary admin Secret replaced' . PHP_EOL;
}
else
{
	$additionalEnableSecrets = $partner->getEnabledAdditionalAdminSecrets();
	$additionalEnableSecrets[] = $newSecret;
	$partner->setEnabledAdditionalAdminSecrets($additionalEnableSecrets);
	ob_end_clean();
	echo 'new secret was added to additional admin secrets'. PHP_EOL;
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
