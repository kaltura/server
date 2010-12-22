<?php

$dryRun = true; //TODO: change for real run
if($argc > 1 && $argv[1] == 'realrun')
	$dryRun = false;
	
$stopFile = dirname(__FILE__).'/stop_user_migration'; // creating this file will stop the script
$userLimitEachLoop = 20;

//------------------------------------------------------

set_time_limit(0);

ini_set("memory_limit","700M");

chdir(dirname(__FILE__));

define('ROOT_DIR', realpath(dirname(__FILE__) . '/../../../../'));
require_once(ROOT_DIR . '/infra/bootstrap_base.php');
require_once(ROOT_DIR . '/infra/KAutoloader.php');

KAutoloader::addClassPath(KAutoloader::buildPath(KALTURA_ROOT_PATH, "vendor", "propel", "*"));
KAutoloader::addClassPath(KAutoloader::buildPath(KALTURA_ROOT_PATH, "plugins", "*"));
KAutoloader::setClassMapFilePath('../../../cache/classMap.cache');
KAutoloader::register();

date_default_timezone_set(kConf::get("date_default_timezone")); // America/New_York

KalturaLog::setLogger(new KalturaStdoutLogger());

DbManager::setConfig(kConf::getDB());
DbManager::initialize();


// stores the last handled admin kuser id, helps to restore in case of crash
$lastUserFile = 'last_kuser';
$lastUser = 0;
if(file_exists($lastUserFile)) {
	$lastUser = file_get_contents($lastUserFile);
	KalturaLog::log('last user file already exists with value - '.$lastUser);
}
if(!$lastUser)
	$lastUser = 0;

$con = myDbHelper::getConnection(myDbHelper::DB_HELPER_CONN_PROPEL2);
	
$users = getUsers($con, $lastUser, $userLimitEachLoop);

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
			KalturaLog::log('Setting kuser id ['.$user->getId().'] admin ['.$user->getIsAdmin().'] with role id ['.$user->getUserRoleIds().']');		
			$user->save(); // save
		}
		else {
			KalturaLog::log('DRY RUN ONLY - Setting kuser id ['.$user->getId().'] admin ['.$user->getIsAdmin().'] with role id ['.$user->getUserRoleIds().']');
		}		
				
		file_put_contents($lastUserFile, $lastUser);
	}
	
	kuserPeer::clearInstancePool();
	UserRolePeer::clearInstancePool();
	
	$users = getUsers($con, $lastUser, $userLimitEachLoop);
}

KalturaLog::log('Done' . $dryRun ? 'REAL RUN!' : 'DRY RUN!');
echo 'Done' . $dryRun ? 'REAL RUN!' : 'DRY RUN!';

function getUsers($con, $lastUser, $userLimitEachLoop)
{
	$c = new Criteria();
	$c->add(kuserPeer::ID, $lastUser, Criteria::GREATER_THAN);
	$c->addAscendingOrderByColumn(kuserPeer::ID);
	$c->setLimit($userLimitEachLoop);
	return kuserPeer::doSelect($c, $con);
}
