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

$users = getAdminUsers($lastUser, $userLimitEachLoop);

while(count($users))
{
	foreach($users as $user)
	{
		if (file_exists($stopFile)) {
			die('STOP FILE CREATED');
		}

		$lastUser = $user->getId();
		KalturaLog::log('-- kuser id ' . $lastUser);
		
		$partner = PartnerPeer::retrieveByPK($user->getPartnerId());
		
		if (!$partner)
		{
			KalturaLog::alert('ERROR - cannot find partner id ['.$user->getPartnerId().'] defined for kuser id ['.$lastUser.'] - skipping user');
			continue;
		}
		
		if ($partner->getId() == -2)
		{
			KalturaLog::log('Skipping partner -2 users... will be migrated in a later script');
			continue;
		}
		
		
		
		$adminRoleId = $partner->getAdminSessionRoleId();
		$user->setRoleIds($adminRoleId);

		if (!$dryRun) {
			KalturaLog::log('Setting kuser id ['.$user->getId().'] admin ['.$user->getIsAdmin().'] with role id ['.$adminRoleId.']');		
			$user->save(); // save
		}
		else {
			KalturaLog::log('DRY RUN ONLY - Setting kuser id ['.$user->getId().'] admin ['.$user->getIsAdmin().'] with role id ['.$adminRoleId.']');
		}		
				
		file_put_contents($lastUserFile, $lastUser);
	}
	
	$users = getAdminUsers($lastUser, $userLimitEachLoop);
}

$msg = 'Done - ' . ($dryRun ? 'DRY RUN!' : 'REAL RUN!');
KalturaLog::log($msg);
echo $msg;

function getAdminUsers($lastUser, $userLimitEachLoop)
{
	kuserPeer::clearInstancePool();
	UserRolePeer::clearInstancePool();
	$c = new Criteria();
	$c->addAnd(kuserPeer::ID, $lastUser, Criteria::GREATER_THAN);
	$c->addAnd(kuserPeer::IS_ADMIN, true, Criteria::EQUAL);
	$c->addAscendingOrderByColumn(kuserPeer::ID);
	$c->setLimit($userLimitEachLoop);
	kuserPeer::setUseCriteriaFilter(false);
	$users = kuserPeer::doSelect($c);
	kuserPeer::setUseCriteriaFilter(true);
	return $users;
}
