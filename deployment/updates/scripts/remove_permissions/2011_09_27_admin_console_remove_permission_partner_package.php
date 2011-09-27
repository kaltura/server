<?php
/**
 * @package deployment
 * @subpackage dragonfly.roles_and_permissions
 * 
 * remove permissions for admin console partner configuration page
 * 
 * No need to re-run after server code deploy
 */


require_once(dirname(__FILE__).'/../../../bootstrap.php');

$permissionToRemove = 'systemPartner.SYSTEM_ADMIN_PUBLISHER_CONFIG_PACKAGES_SERVICE,systemPartner.SYSTEM_ADMIN_PUBLISHER_CONFIG_OPTIONS_MONITOR_USAGE';
$userRoleNameToKeepPermission = 'System Administrator';


$userRoles = getUserRolesToRemovePermission($userRoleNameToKeepPermission, -2);
foreach ($userRoles as $userRole)
{
	echo "remove permissions to " . $userRole->getName() .  ' role id: ' . $userRole->getId() . PHP_EOL;
	removePermissionsToRole($userRole, $permissionToRemove);
}

function getUserRolesToRemovePermission($userRoleNameToKeepPermission, $partnerId)
{
	$c = new Criteria();
	$c->addAnd(UserRolePeer::PARTNER_ID, $partnerId, Criteria::EQUAL);
	$c->addAnd(UserRolePeer::NAME, $userRoleNameToKeepPermission, Criteria::NOT_EQUAL);
	UserRolePeer::setUseCriteriaFilter(false);
	$userRole = UserRolePeer::doSelect($c);
	UserRolePeer::setUseCriteriaFilter(true);
	return $userRole;
}

function removePermissionsToRole($role, $permissionToRemove)
{
	$currentPermissions = $role->getPermissionNames();
	$currentPermissionsArray = explode(',', $currentPermissions);
	$tempArray = array();
	foreach ($currentPermissionsArray as $key => $perm)
	{
		if ($perm == '')
		{
			unset($currentPermissionsArray[$key]);
		}
		elseif ($perm == $permissionToRemove) 
		{
			unset($currentPermissionsArray[$key]);
		}
	}
	
	$currentPermissions = implode(',', $currentPermissionsArray);
	$role->setPermissionNames($currentPermissions);
	$role->save();
	KalturaLog::log('Added permission ['.$currentPermissions.'] to role name ['.$role->getName().']');
}

