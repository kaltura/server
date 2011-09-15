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
$config = realpath(dirname(__FILE__)) . '/../../../../plugins/system_partner/config/system_partner_permissions_part_4.ini';
passthru("php $script $config");


require_once(dirname(__FILE__).'/../../../bootstrap.php');

$permissionRoleMap = array(
	'Support manager' => 'SYSTEM_ADMIN_PARTNER_CONFIGURATION_VIEW',
	'Publishers Administrator' => 'SYSTEM_ADMIN_PARTNER_CONFIGURATION_VIEW',
);

foreach ($permissionRoleMap as $roleName => $permissionList)
{
	echo "get  permissions $roleName" . PHP_EOL;
	$role = getByNameAndPartnerId($roleName, -2);
	if (!$role) {
		KalturaLog::err('ERROR - Cannot find role with name ['.$roleName.']');
	}
	else {
		echo "add permissions to $roleName" . PHP_EOL;
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

