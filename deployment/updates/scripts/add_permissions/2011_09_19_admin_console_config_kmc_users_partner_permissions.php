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
$config = realpath(dirname(__FILE__)) . '/../../../../plugins/system_partner/config/system_partner_permissions_part_5.ini';
passthru("php $script $config");


require_once(dirname(__FILE__).'/../../../bootstrap.php');

$publisherAdminRole = getBySystemNameAndPartnerId('Publishers Administrator', -2);
if ($publisherAdminRole) {
	$publisherAdminRole->setName('Publisher Administrator');
	$publisherAdminRole->setSystemName('Publisher Administrator');
}

$permissionRoleMap = array(
	'Support manager' => 'systemPartner.SYSTEM_ADMIN_PUBLISHER_CONFIG_ADMIN_KMC_USERS',
	'Publisher Administrator' => 'SYSTEM_ADMIN_PUBLISHER_RESET_USER_PASSWORD,'.
								'systemPartner.SYSTEM_ADMIN_PUBLISHER_CONFIG_GROUP_OPTIONS,'. 
								'systemPartner.SYSTEM_ADMIN_PUBLISHER_CONFIG_TECH_DATA,systemPartner.SYSTEM_ADMIN_PUBLISHER_CONFIG_ACCOUNT_INFO,systemPartner.SYSTEM_ADMIN_PUBLISHER_CONFIG_GENERAL_INFORMATION,systemPartner.SYSTEM_ADMIN_PUBLISHER_CONFIG_GROUP_OPTIONS,systemPartner.SYSTEM_ADMIN_PUBLISHER_CONFIG_PACKAGES_SERVICE,systemPartner.SYSTEM_ADMIN_PUBLISHER_CONFIG_OPTIONS_MONITOR_USAGE,'.
								'SYSTEM_ADMIN_PARTNER_CONFIGURATION_VIEW,'.
								'SYSTEM_ADMIN_WIDGET,'. 
								'systemPartner.SYSTEM_ADMIN_PUBLISHER_CONFIG_ADMIN_KMC_USERS'
);

foreach ($permissionRoleMap as $roleName => $permissionList)
{
	echo "get  permissions $roleName" . PHP_EOL;
	$role = getBySystemNameAndPartnerId($roleName, -2);
	if (!$role) {
		KalturaLog::err('ERROR - Cannot find role with name ['.$roleName.']');
	}
	else {
		echo "add permissions to $roleName" . PHP_EOL;
		addPermissionsToRole($role, $permissionList);
	}
}

function getBySystemNameAndPartnerId($roleName, $partnerId)
{
	$c = new Criteria();
	$c->addAnd(UserRolePeer::PARTNER_ID, $partnerId, Criteria::EQUAL);
	$c->addAnd(UserRolePeer::SYSTEM_NAME, $roleName, Criteria::EQUAL);
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

