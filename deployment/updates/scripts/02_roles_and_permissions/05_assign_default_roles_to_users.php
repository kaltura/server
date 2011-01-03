<?php

$dryRun = true; //TODO: change for real run
if($argc > 1 && $argv[1] == 'realrun')
	$dryRun = false;
	
$stopFile = dirname(__FILE__).'/stop_user_migration'; // creating this file will stop the script
$userLimitEachLoop = 20;

//------------------------------------------------------

set_time_limit(0);

require_once(dirname(__FILE__).'/../../../bootstrap.php');

// stores the last handled admin kuser id, helps to restore in case of crash
$lastUserFile = 'last_kuser';
$lastUser = 0;
if(file_exists($lastUserFile)) {
	$lastUser = file_get_contents($lastUserFile);
	KalturaLog::log('last user file already exists with value - '.$lastUser);
}
if(!$lastUser)
	$lastUser = 0;

$users = getUsers($lastUser, $userLimitEachLoop);

while(count($users))
{
	foreach($users as $user)
	{
		if (file_exists($stopFile)) {
			die('STOP FILE CREATED');
		}

		$lastUser = $user->getId();
		KalturaLog::log('-- kuser id ' . $lastUser);
			
		$userRole = UserRolePeer::getDefaultRoleForUser($user);
		$user->setUserRoles($userRole->getId());

		if (!$dryRun) {
			KalturaLog::log('Setting kuser id ['.$user->getId().'] admin ['.$user->getIsAdmin().'] with role id ['.$userRole->getId().']');		
			$user->save(); // save
		}
		else {
			KalturaLog::log('DRY RUN ONLY - Setting kuser id ['.$user->getId().'] admin ['.$user->getIsAdmin().'] with role id ['.$userRole->getId().']');
		}		
				
		file_put_contents($lastUserFile, $lastUser);
	}
	
	kuserPeer::clearInstancePool();
	UserRolePeer::clearInstancePool();
	
	$users = getUsers($lastUser, $userLimitEachLoop);
}

$msg = 'Done' . ($dryRun ? 'DRY RUN!' : 'REAL RUN!');
KalturaLog::log($msg);
echo $msg;

function getUsers($lastUser, $userLimitEachLoop)
{
	$c = new Criteria();
	$c->add(kuserPeer::ID, $lastUser, Criteria::GREATER_THAN);
	$c->addAscendingOrderByColumn(kuserPeer::ID);
	$c->setLimit($userLimitEachLoop);
	return kuserPeer::doSelect($c);
}
