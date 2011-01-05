<?php

/**
 * This script adds a 'kmc' tag to all user roles which should be displayed in the KMC.
 */

$dryRun = true; //TODO: change for real run
if($argc > 1 && $argv[1] == 'realrun')
	$dryRun = false;
	
//------------------------------------------------------

require_once(dirname(__FILE__).'/../../../../bootstrap.php');

//------------------------------------------------------

set_time_limit(0);

$roleLimitEachLoop = 1000;
$stopFile = 'stop_role_migration';

// stores the last handled admin kuser id, helps to restore in case of crash
$lastRoleFile = 'last_user_role';
$lastRole = 0;
if(file_exists($lastRoleFile)) {
	$lastRole = file_get_contents($lastRoleFile);
	KalturaLog::log('last role file already exists with value - '.$lastRole);
}
if(!$lastRole)
	$lastRole = 0;

$roles = getRoles($lastRole, $roleLimitEachLoop);

while(count($roles))
{
	foreach($roles as $role)
	{
		if (file_exists($stopFile)) {
			die('STOP FILE CREATED');
		}

		$lastRole = $role->getId();
		KalturaLog::log('-- role id ' . $lastRole);
		
		if ($role->getStrId() != UserRoleId::BASE_USER_SESSION_ROLE && $role->getStrId() != UserRoleId::NO_SESSION_ROLE) {
			$role->setTags('kmc');
		}
		else {
			KalturaLog::log('Skipping role id ['.$lastRole.'] with str id ['.$role->getStrId().']');
		}

		if (!$dryRun) {
			KalturaLog::log('Saving role id ['.$role->getId().'] with tags ['.$role->getTags().']');		
			$role->save(); // save
		}
		else {
			KalturaLog::log('DRY RUN ONLY - Saving role id ['.$role->getId().'] with tags ['.$role->getTags().']');
		}		
				
		file_put_contents($lastRoleFile, $lastRole);
	}
	
	$roles = getRoles($lastRole, $roleLimitEachLoop);
}

$msg = 'Done ' . ($dryRun ? 'DRY RUN!' : 'REAL RUN!');
KalturaLog::log($msg);
echo $msg;



function getRoles($lastRole, $roleLimitEachLoop)
{
	UserRolePeer::clearInstancePool();
	UserRolePeer::setDefaultCriteriaFilter(false);
	$c = new Criteria();
	$c->add(UserRolePeer::ID, $lastRole, Criteria::GREATER_THAN);
	$c->addAscendingOrderByColumn(UserRolePeer::ID);
	$c->setLimit($roleLimitEachLoop);
	return UserRolePeer::doSelect($c);
}
