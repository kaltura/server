<?php

$dryRun = true; //TODO: change for real run
if($argc > 1 && $argv[1] == 'realrun')
	$dryRun = false;
	
//------------------------------------------------------

require_once(dirname(__FILE__).'/../../../bootstrap.php');

//------------------------------------------------------

$userRoles = array();

$role = new UserRole();
$role->setName('System Administrator');
$role->setDescription('Full permissions to all functionalities');
$role->setPermissionNames(UserRole::ALL_PARTNER_PERMISSIONS_WILDCARD);
$role->setStatus(UserRoleStatus::ACTIVE);
$role->setTags('admin_console');
$userRoles[] = $role;

$role = new UserRole();
$role->setName('Support manager');
$role->setDescription('Support manager');
$permissions = 'SYSTEM_ADMIN_BASE,SYSTEM_ADMIN_PUBLISHER_BASE,SYSTEM_ADMIN_PUBLISHER_KMC_ACCESS,SYSTEM_ADMIN_PUBLISHER_CONFIG,SYSTEM_ADMIN_PUBLISHER_BLOCK,SYSTEM_ADMIN_PUBLISHER_ADD,SYSTEM_ADMIN_PUBLISHER_USAGE,SYSTEM_ADMIN_DEVELOPERS_TAB,SYSTEM_ADMIN_BATCH_CONTROL';
$role->setPermissionNames($permissions);
$role->setStatus(UserRoleStatus::ACTIVE);
$role->setTags('admin_console');
$userRoles[] = $role;

$role = new UserRole();
$role->setName('Publishers Administrator');
$role->setDescription('Publishers Administrator');
$permissions = 'SYSTEM_ADMIN_BASE,SYSTEM_ADMIN_PUBLISHER_BASE,SYSTEM_ADMIN_PUBLISHER_KMC_ACCESS,SYSTEM_ADMIN_PUBLISHER_CONFIG,SYSTEM_ADMIN_PUBLISHER_BLOCK,SYSTEM_ADMIN_PUBLISHER_ADD,SYSTEM_ADMIN_PUBLISHER_USAGE,SYSTEM_ADMIN_DEVELOPERS_TAB';
$role->setPermissionNames($permissions);
$role->setStatus(UserRoleStatus::ACTIVE);
$role->setTags('admin_console');
$userRoles[] = $role;

$role = new UserRole();
$role->setName('Guest');
$role->setDescription('Guest');
$permissions = 'SYSTEM_ADMIN_BASE';
$role->setPermissionNames($permissions);
$role->setStatus(UserRoleStatus::ACTIVE);
$role->setTags('admin_console');
$userRoles[] = $role;


//------------------------------------------------------

foreach ($userRoles as $newRole)
{
	$newRole->setPartnerId(-2); // set admin console partner id (-2)
	if ($dryRun) {
		KalturaLog::log('DRY RUN - Adding new role - '.print_r($newRole, true));
	}
	else {
		KalturaLog::log('Adding new role - '.print_r($newRole, true));
		$newRole->save();
	}
}

$msg = 'Done - ' . ($dryRun ? 'DRY RUN!' : 'REAL RUN!');
KalturaLog::log($msg);
echo $msg;