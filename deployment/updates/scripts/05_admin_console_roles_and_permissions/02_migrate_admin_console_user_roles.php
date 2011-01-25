<?php

$dryRun = true; //TODO: change for real run
if($argc > 1 && $argv[1] == 'realrun')
	$dryRun = false;
	
//------------------------------------------------------

require_once(dirname(__FILE__).'/../../../bootstrap.php');
define('ADMIN_CONSOLE_PARTNER_ID', -2);

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
	
	$newRole = getNewRole($oldRole);
	if (!$newRole) {
		KalturaLog::alert('Critical error occured - skipping to next user!');
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

function getNewRole($oldRoleName)
{
	$roleNameMap = array (
		'guest' => 'Guest',
		'admin' => 'System Administrator',
		'ps'    => 'Support manager',
	);
	
	if (!isset($roleNameMap[$oldRoleName]))
	{
		KalturaLog::alert('New role name was not found for old role name ['.$oldRoleName.']');
		return null;
	}
	
	$newRoleName = $roleNameMap[$oldRoleName];
			
	$c = new Criteria();
	$c->addAnd(UserRolePeer::PARTNER_ID, ADMIN_CONSOLE_PARTNER_ID, Criteria::EQUAL);
	$c->addAnd(UserRolePeer::NAME, $newRoleName, Criteria::EQUAL);
	$c->addAnd(UserRolePeer::TAGS, '%admin_console%', Criteria::LIKE);
	UserRolePeer::clearInstancePool();
	UserRolePeer::setUseCriteriaFilter(false);
	$newRole = UserRolePeer::doSelectOne($c);
	UserRolePeer::setUseCriteriaFilter(true);
	
	if (!$newRole) {
		KalturaLog::alert('Role with name ['.$newRoleName.'] was not found in DB!');
		return null;
	}
	
	return $newRole;
}

//------------------------------------------------------
