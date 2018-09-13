<?php
require_once(__DIR__ . '/../bootstrap.php');
error_reporting(E_ERROR | E_WARNING | E_PARSE);

KalturaLog::info('add admin secret start' . PHP_EOL);

if ($argc !== 3)
	die(PHP_EOL . 'php' . "Usage: $argv[0] <partnerId> <secretToDisable>". PHP_EOL .
		"<partnerId> - the Partner ID" . PHP_EOL .
		"<secretToDisable> - the secret to disable **disabled secret cannot be main admin secret**" . PHP_EOL
	);


$partnerId = $argv[1];
$toDisable = $argv[2];

$partner = PartnerPeer::retrieveByPK($partnerId);
/** @var array $additionalSecrets */
$additionalSecrets = $partner->getEnabledAdditionalAdminSecrets();

if (!in_array($toDisable, $additionalSecrets, true))
	die("$toDisable does not exist in enabledAdditionalSecretArray on partner: $partnerId" . PHP_EOL);

$toDisableArray = array($toDisable);
$additionalSecrets = array_diff($additionalSecrets, $toDisableArray);
$partner->setEnabledAdditionalAdminSecrets($additionalSecrets);

try
{
	$partner->save();
}
catch (PropelException $e)
{
	die($e->getMessage());
}

die(PHP_EOL . 'Secret were disabled in partner:  ' . $partnerId . PHP_EOL);