<?php
/**
 * @package deployment
 * @subpackage dragonfly.admin_roles_and_permissions
 * 
 * Adds base admin console role
 * Change mograted role to new created role
 * 
 * No need to re-run after server code deploy
 */


$dryRun = true; //TODO: change for real run
if($argc > 1 && $argv[1] == 'realrun')
	$dryRun = false;
	
//------------------------------------------------------

require_once(dirname(__FILE__).'/../../../bootstrap.php');
define('ADMIN_CONSOLE_PARTNER_ID', Partner::ADMIN_CONSOLE_PARTNER_ID);

//------------------------------------------------------

$userRoles = array();

$role = new UserRole();
$role->setName('System Administrator');
$role->setDescription('Full permissions to all functionalities');
$role->setPermissionNames(UserRole::ALL_PARTNER_PERMISSIONS_WILDCARD);
$role->setStatus(UserRoleStatus::ACTIVE);
$role->setTags('admin_console');
$userRoles['admin'] = $role;

$role = new UserRole();
$role->setName('Support manager');
$role->setDescription('Support manager');
$permissions = 'SYSTEM_ADMIN_BASE,SYSTEM_ADMIN_PUBLISHER_BASE,SYSTEM_ADMIN_PUBLISHER_KMC_ACCESS,SYSTEM_ADMIN_PUBLISHER_CONFIG,SYSTEM_ADMIN_PUBLISHER_BLOCK,SYSTEM_ADMIN_PUBLISHER_ADD,SYSTEM_ADMIN_PUBLISHER_USAGE,SYSTEM_ADMIN_DEVELOPERS_TAB,SYSTEM_ADMIN_BATCH_CONTROL,SYSTEM_ADMIN_CONTENT_DISTRIBUTION_BASE,SYSTEM_ADMIN_CONTENT_DISTRIBUTION_MODIFY';
$role->setPermissionNames($permissions);
$role->setStatus(UserRoleStatus::ACTIVE);
$role->setTags('admin_console');
$userRoles['ps'] = $role;

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
$userRoles['guest'] = $role;


//------------------------------------------------------

foreach ($userRoles as $key => $newRole)
{
	$newRole->setPartnerId(-2); // set admin console partner id (-2)
	if ($dryRun) {
		KalturaLog::log('DRY RUN - Adding new role - '.print_r($newRole, true));
	}
	else {
		KalturaLog::log('Adding new role - '.print_r($newRole, true));
		$newRole->save();
		if ($key === 'admin') {
			$partner = PartnerPeer::retrieveByPk(-2);
			$partner->setAdminSessionRoleId($newRole->getId());
			$partner->save();
		}
	}
}

//------------------------------------------------------

// get users

$c = new Criteria();
$c->addAscendingOrderByColumn(kuserPeer::ID);
$c->addAnd(kuserPeer::PARTNER_ID, ADMIN_CONSOLE_PARTNER_ID, Criteria::EQUAL);
kuserPeer::clearInstancePool();
kuserPeer::setUseCriteriaFilter(false);
$users = kuserPeer::doSelect($c);
kuserPeer::setUseCriteriaFilter(true);


// set relevant role to each user

foreach ($users as $user)
{
	KalturaLog::log('Current user id ['.$user->getId().']');
	
	$partnerData = $user->getPartnerData();
	if ($partnerData) {
		$partnerData = unserialize($partnerData);
		$oldRole = $partnerData->role;
	}
	else {
		$oldRole = 'guest';
	}
	
	$newRole = getNewRole($oldRole, $userRoles);
	if (!$newRole) {
		KalturaLog::alert('ERROR - Critical error occured - skipping kuser id ['.$user->getId().'] email ['.$user->getEmail().']!');
		continue;
	}
	
	$user->setRoleIds($newRole->getId());
	
	if ($dryRun)
	{
		KalturaLog::log('DRY RUN! - Setting user ['.$user->getId().'] with old role ['.$oldRole.'] to new role id ['.$newRole->getId().'] name ['.$newRole->getName().']');
	}
	else
	{
		KalturaLog::log('Setting user ['.$user->getId().'] with old role ['.$oldRole.'] to new role id ['.$newRole->getId().'] name ['.$newRole->getName().']');
		$user->save();
	}	
}


$msg = 'Done - ' . ($dryRun ? 'DRY RUN!' : 'REAL RUN!');
KalturaLog::log($msg);
echo $msg;



//------------------------------------------------------

function getNewRole($oldRoleName, $userRoles)
{
	if (!$oldRoleName) {
		$oldRoleName = 'guest';
	}
	
	if (!isset($userRoles[$oldRoleName]))
	{
		KalturaLog::alert('New role name was not found for old role name ['.$oldRoleName.']');
		return null;
	}
	
	$c = new Criteria();
	$c->addAnd(UserRolePeer::PARTNER_ID, ADMIN_CONSOLE_PARTNER_ID, Criteria::EQUAL);
	$c->addAnd(UserRolePeer::ID, $userRoles[$oldRoleName]->getId(), Criteria::EQUAL);
	$c->addAnd(UserRolePeer::TAGS, '%admin_console%', Criteria::LIKE);
	UserRolePeer::clearInstancePool();
	UserRolePeer::setUseCriteriaFilter(false);
	$newRole = UserRolePeer::doSelectOne($c);
	UserRolePeer::setUseCriteriaFilter(true);
	
	if (!$newRole) {
		KalturaLog::alert('Role with id ['.$userRoles[$oldRoleName]->getId().'] was not found in DB!');
		return null;
	}
	
	return $newRole;
}

//------------------------------------------------------