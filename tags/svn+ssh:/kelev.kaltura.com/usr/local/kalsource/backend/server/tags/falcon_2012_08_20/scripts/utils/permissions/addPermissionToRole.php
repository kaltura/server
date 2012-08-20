<?php

/*
	'Publisher Administrator' => 'CONTENT_MANAGE_CATEGORY_USERS,CONTENT_MANAGE_ENTRY_USERS,ADMIN_USER_BULK',
	'Manager' => 'CONTENT_MANAGE_CATEGORY_USERS,CONTENT_MANAGE_ENTRY_USERS'.
	'Content Uploader' => 'CONTENT_MANAGE_ENTRY_USERS'.
*/
require_once(dirname(__FILE__).'/../../../alpha/config/sfrootdir.php');
require_once(dirname(__FILE__).'/../../../api_v3/bootstrap.php');
KalturaLog::setLogger(new KalturaStdoutLogger());

$partnerId = null;
$page = 500;

$dryRun = false;
if($argc == 1 || strtolower($argv[1]) != 'realrun')
{
	$dryRun = true;
	KalturaLog::debug('Using dry run mode');
}

if($argc > 2)
{
	$roleName = $argv[1];
	$parmissionName = $argv[2];
}else{
	echo 'usage: php ' . $_SERVER ['SCRIPT_NAME'] . ' {role_name} {permission_name}' . PHP_EOL;
	die;
}
	
	
$criteria = new Criteria();
$criteria->addAnd(UserRolePeer::NAME, $roleName, Criteria::EQUAL);
$criteria->addAscendingOrderByColumn(UserRolePeer::ID);
$criteria->setLimit($page);

$userRoles = UserRolePeer::doSelect($criteria);
	
while(count($userRoles))
{
	
	KalturaLog::info("[" . count($userRoles) . "] user roles .");
	foreach($userRoles as $userRole)
	{
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



