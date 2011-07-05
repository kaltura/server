<?php
/**
 * @package deployment
 * @subpackage dragonfly.roles_and_permissions
 * 
 * Adds permissions for admin console partner configuration page and add the permissions to the appropriate roles
 * 
 * No need to re-run after server code deploy
 */

$script = realpath(dirname(__FILE__) . '/../../../../') . '/scripts/utils/permissions/addPermissionsAndItems.php';
$config = realpath(dirname(__FILE__)) . '/../../../../plugins/system_partner/config/system_partner_permissions.ini';
passthru("php $script $config");


require_once(dirname(__FILE__).'/../../../bootstrap.php');

$permissionRoleMap = array(
	'Support manager' => 'systemPartner.SYSTEM_ADMIN_PUBLISHER_CONFIG_ACCOUNT_INFO,systemPartner.SYSTEM_ADMIN_PUBLISHER_CONFIG_GROUP_OPTIONS',
	'Publishers Administrator' => 'systemPartner.SYSTEM_ADMIN_PUBLISHER_CONFIG_GROUP_OPTIONS',
);


foreach ($permissionRoleMap as $roleName => $permissionList)
{
	$role = getByNameAndPartnerId($roleName, -2);
	if (!$role) {
		KalturaLog::err('ERROR - Cannot find role with name ['.$roleName.']');
	}
	else {
		addPermissionsToRole($role, $permissionList);
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

function addPermissionsToRole($role, $permissionList)
{
	$currentPermissions = $role->getPermissionNames();
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

