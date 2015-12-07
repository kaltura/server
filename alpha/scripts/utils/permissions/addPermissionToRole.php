<?php

/*
	'Publisher Administrator' => 'CONTENT_MANAGE_CATEGORY_USERS,CONTENT_MANAGE_ENTRY_USERS,ADMIN_USER_BULK',
	'Manager' => 'CONTENT_MANAGE_CATEGORY_USERS,CONTENT_MANAGE_ENTRY_USERS'.
	'Content Uploader' => 'CONTENT_MANAGE_ENTRY_USERS'.
*/
require_once(dirname(__FILE__).'/../../bootstrap.php');

if($argc < 3)
{
	echo 'usage: php ' . $_SERVER ['SCRIPT_NAME'] . ' {partner_id / \'null\'} {role_name} {permission_names} {realrun / dryrun}' . PHP_EOL;
	exit(-1);
}

$page = 500;

$dryRun = true;
if(in_array('realrun', $argv))
{
	$dryRun = false;
	KalturaLog::debug('Using real run mode');
}
else
	KalturaLog::debug('Using dry run mode');
	
KalturaStatement::setDryRun($dryRun);

$partnerId = $argv[1] == 'null' ? null : $argv[1];
$roleName = $argv[2];
$parmissionNames = explode(',', $argv[3]);
	
	
$criteria = new Criteria();
if(!is_null($partnerId))
	$criteria->addAnd(UserRolePeer::PARTNER_ID, $partnerId, Criteria::EQUAL);
$criteria->addAnd(UserRolePeer::NAME, $roleName, Criteria::EQUAL);
$criteria->addAscendingOrderByColumn(UserRolePeer::ID);
$criteria->setLimit($page);

$userRoles = UserRolePeer::doSelect($criteria);
	
while(count($userRoles))
{
	
	KalturaLog::info("[" . count($userRoles) . "] user roles .");
	foreach($userRoles as $userRole)
	{
		foreach($parmissionNames as $parmissionName)
			addPermissionsToRole($userRole, $parmissionName);
	}
	kMemoryManager::clearMemory();

	$nextCriteria = clone $criteria;
	$nextCriteria->add(UserRolePeer::ID, $userRole->getId(), Criteria::GREATER_THAN);
	$userRoles = UserRolePeer::doSelect($nextCriteria);
	usleep(100);
}


KalturaLog::info("Done");


function addPermissionsToRole($role, $permissionList)
{
	$currentPermissions = $role->getPermissionNames(false, true);
	
	if(UserRole::ALL_PARTNER_PERMISSIONS_WILDCARD ==  $currentPermissions)
		return;
		
	$currentPermissionsArray = explode(',', $currentPermissions);
	$permissionsToAddArray = explode(',', $permissionList);
	$tempArray = array();
	foreach ($permissionsToAddArray as $perm)
	{
		if (in_array($perm, $currentPermissionsArray)) {
			KalturaLog::log('Role name ['.$role->getName().'] already has permission ['.$perm.']');
		}
		else {
			$tempArray[] = $perm;
		}
	}
	
	$tempString = trim(implode(',', $tempArray), ',');
	$currentPermissions .= ','.$tempString;
	$role->setPermissionNames($currentPermissions);
	$role->save();
	
	KalturaLog::log('Added permission ['.$tempString.'] to role name ['.$role->getName().']');
}



