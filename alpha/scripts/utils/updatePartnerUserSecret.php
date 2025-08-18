<?php

require_once(__DIR__ . '/../bootstrap.php');
error_reporting(E_ERROR | E_WARNING | E_PARSE);

KalturaLog::info('update user secret start' . PHP_EOL);

if ($argc !== 3)
	die(PHP_EOL . 'php' ."Usage: $argv[0] <partnerId> <newUserSecret>". PHP_EOL .
		"<partnerId> - the Partner ID" . PHP_EOL .
		"<newUserSecret> - the new user secret to set (32 character MD5 hash)" . PHP_EOL
	);

$partnerId = $argv[1];
$newUserSecret = $argv[2];

// Validate MD5 format
if (!preg_match('/^[a-f0-9]{32}$/', $newUserSecret))
	die('Error: ' . $newUserSecret . ' is not a valid MD5 hash' . PHP_EOL);

$partner = PartnerPeer::retrieveByPK($partnerId);
if (!$partner)
	die("Partner $partnerId not found" . PHP_EOL);

$oldUserSecret = $partner->getSecret();

if ($oldUserSecret === $newUserSecret)
	die("User secret already set to: $newUserSecret" . PHP_EOL);

// Check if new secret conflicts with admin secrets
$adminSecret = $partner->getAdminSecret();
$additionalAdminSecrets = $partner->getEnabledAdditionalAdminSecrets();

if ($newUserSecret === $adminSecret || in_array($newUserSecret, $additionalAdminSecrets, true))
	die("Error: $newUserSecret conflicts with existing admin secrets" . PHP_EOL);

$partner->setSecret($newUserSecret);

try
{
	$partner->save();
}
catch (PropelException $e)
{
	die($e->getMessage());
}

die(PHP_EOL . 'User secret updated for partner: ' . $partnerId . PHP_EOL);
