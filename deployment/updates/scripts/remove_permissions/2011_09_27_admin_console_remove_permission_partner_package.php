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

$permissionRoleMap = array(
	'Support manager' => 'systemPartner.SYSTEM_ADMIN_PUBLISHER_CONFIG_PACKAGES_SERVICE',
	'Publishers Administrator' => 'systemPartner.SYSTEM_ADMIN_PUBLISHER_CONFIG_PACKAGES_SERVICE'
);

foreach ($permissionRoleMap as $roleName => $permissionList)
{
	echo "get  permissions $roleName" . PHP_EOL;
	$role = getByNameAndPartnerId($roleName, -2);
	if (!$role) {
		KalturaLog::err('ERROR - Cannot find role with name ['.$roleName.']');
	}
	else {
		echo "remove permissions to $roleName" . PHP_EOL;
		removePermissionsToRole($role, $permissionList);
	}
}

function getByNameAndPartnerId($roleName, $partnerId)
{
	$c = new Criteria();
	$c->addAnd(UserRolePeer::PARTNER_ID, $partnerId, Criteria::EQUAL);
	$c->addAnd(UserRolePeer::NAME, $roleName, Criteria::EQUAL);
	UserRolePeer::setUseCriteriaFilter(false);
	$userRole = UserRolePeer::doSelectOne($c);
	UserRolePeer::setUseCriteriaFilter(true);
	return $userRole;
}

function removePermissionsToRole($role, $permissionList)
{
	$currentPermissions = $role->getPermissionNames();
	$currentPermissionsArray = explode(',', $currentPermissions);
	$permissionsToRemoveArray = explode(',', $permissionList);
	$tempArray = array();
	foreach ($currentPermissionsArray as $key => $perm)
	{
		if ($perm == '')
		{
			unset($currentPermissionsArray[$key]);
		}
		elseif (in_array($perm, $permissionsToRemoveArray)) 
		{
			unset($currentPermissionsArray[$key]);
		}
		else 
		{
			KalturaLog::log('Role name ['.$role->getName().'] does nto have permission ['.$perm.']');
		}
	}
	
	$currentPermissions = implode(',', $currentPermissionsArray);
	$role->setPermissionNames($currentPermissions);
	$role->save();
	KalturaLog::log('Added permission ['.$currentPermissions.'] to role name ['.$role->getName().']');
}

