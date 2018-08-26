<?php

require_once(__DIR__ . '/../bootstrap.php');
error_reporting(E_ERROR | E_WARNING | E_PARSE);

KalturaLog::info('add admin secret start' . PHP_EOL);

if ($argc !== 3)
	die(PHP_EOL . 'php' ."Usage: $argv[0] <partnerId> <secretToSetAsPrimary>". PHP_EOL .
		"<partnerId> - the Partner ID" . PHP_EOL .
		"<secretToSetAsPrimary> - the secret to set as primary **Must exist in enabled Additional Secrets**" . PHP_EOL
	);

$partnerId = $argv[1];
$newPrimarySecret = $argv[2];

$partner = PartnerPeer::retrieveByPK($partnerId);

$oldPrimarySecret = $partner->getAdminSecret();

if ($oldPrimarySecret === $newPrimarySecret)
	die("Primary secret already set to : $newPrimarySecret" . PHP_EOL);

/** @var array $additionalSecrets */
$additionalSecrets = $partner->getEnabledAdditionalAdminSecrets();

if (!in_array($newPrimarySecret, $additionalSecrets,true))
	die("$newPrimarySecret does not exist in enabledAdditionalSecretArray on partner: $partnerId" . PHP_EOL);

$partner->setAdminSecret($newPrimarySecret);
$newPrimarySecretArray = array($newPrimarySecret);
$additionalSecrets = array_diff($additionalSecrets, $newPrimarySecretArray);
array_unshift($additionalSecrets, $oldPrimarySecret);
$partner->setEnabledAdditionalAdminSecrets($additionalSecrets);

try
{
	$partner->save();
}
catch (PropelException $e)
{
	die($e->getMessage());
}

die(PHP_EOL . 'Secret were moved in partner:  ' . $partnerId . PHP_EOL);

