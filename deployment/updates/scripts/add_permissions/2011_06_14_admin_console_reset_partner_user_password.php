<?php
/**
 * @package deployment
 * @subpackage dragonfly.roles_and_permissions
 * 
 * Adds permission for admin console to reset a partner's user password and adds the permission to relevant admin conosle roles
 * 
 * No need to re-run after server code deploy
 */

$script = realpath(dirname(__FILE__) . '/../../../../') . '/scripts/utils/permissions/addPermissionsAndItems.php';
$config = realpath(dirname(__FILE__)) . '/configs/admin_console_reset_partner_user_password.ini';
passthru("php $script $config");

require_once(dirname(__FILE__).'/../../../bootstrap.php');

$roleNames = array(
	'Publishers Administrator',
	'Support manager',
	'Account manager',
);

foreach ($roleNames as $roleName)
{
	$role = getByNameAndPartnerId($roleName, -2);
	if (!$role) {
		KalturaLog::err('ERROR - Cannot find role with name ['.$roleName.']');
	}
	else {
		addResetUserPasswordPermissionToRole($role);
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

function addResetUserPasswordPermissionToRole($role)
{
	$currentPermissions = $role->getPermissionNames();
	if (in_array('SYSTEM_ADMIN_PUBLISHER_RESET_USER_PASSWORD', explode(',', $currentPermissions))) {
		KalturaLog::log('Role name ['.$role->getName().'] already has permission [SYSTEM_ADMIN_PUBLISHER_RESET_USER_PASSWORD]');
		return;
	}
	$currentPermissions .= ',SYSTEM_ADMIN_PUBLISHER_RESET_USER_PASSWORD';
	$role->setPermissionNames($currentPermissions);
	$role->save();
	KalturaLog::log('Added permission [SYSTEM_ADMIN_PUBLISHER_RESET_USER_PASSWORD] to role name ['.$role->getName().']');
}

