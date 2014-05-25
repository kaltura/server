<?php
require_once(dirname(__FILE__).'/../bootstrap.php');

if ($argc < 3)
	die ("Script usage: php setAlwaysAllowedPartnerPermissions.php <partnerId> <comma-separated permission names list>");

$partnerId = $argv[1];
$permissionNames = $argv[2];

$partner = PartnerPeer::retrieveByPK($partnerId);
if (!$partner)
{
	die ("Partner with id [$partnerId] not found");
}

$partner->setAlwaysAllowedPermissionNames($permissionNames);
$partner->save();
