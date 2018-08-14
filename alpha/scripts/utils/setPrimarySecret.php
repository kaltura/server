<?php

require_once(__DIR__ . '/../bootstrap.php');
error_reporting(E_ERROR | E_WARNING | E_PARSE);

echo 'add admin secret start' . PHP_EOL;

if ($argc !== 2)
	die('scripts must have two arguments  1 -> partner ID , 2 -> secret to set as primary' . PHP_EOL);

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
$additionalSecrets = array_diff($additionalSecrets,$newPrimarySecretArray);
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

