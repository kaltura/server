<?php

require_once(__DIR__ . '/../bootstrap.php');

if ($argc < 3)
	die("Usage: " . basename(__FILE__) . " <user-role ids, comma separated or all> <permission_names> [realrun / dryrun]\n");

// input parameters
$userRoleIds = $argv[1];
$permissionNames = trim($argv[2]);
$dryRun = (!isset($argv[3]) || $argv[3] != 'realrun');
KalturaLog::debug("Dry Run [" . $dryRun . "]");
KalturaStatement::setDryRun($dryRun);

KalturaLog::debug("Setting permission names to [$permissionNames]");
$userRoleIdsArr = explode(',',$userRoleIds);
$userRoles = UserRolePeer::retrieveByPKs($userRoleIdsArr);
foreach ($userRoles as $userRole)
{
	/* @var UserRole $userRole */
	KalturaLog::debug("Editing userrole [".$userRole->getId()."]");
	$userRole->setPermissionNames($permissionNames);
	$userRole->save();
}


KalturaLog::debug("Finished");


?>