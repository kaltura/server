<?php

$dryRun = true; //TODO: change for real run
if($argc > 1 && $argv[1] == 'realrun')
	$dryRun = false;
	
$stopFile = dirname(__FILE__).'/stop_user_migration'; // creating this file will stop the script
$userLimitEachLoop = 20;
$admin_console_partner_id = -2;

//------------------------------------------------------

set_time_limit(0);

ini_set("memory_limit","700M");

chdir(dirname(__FILE__));

define('ROOT_DIR', realpath(dirname(__FILE__) . '/../../../../'));
require_once(ROOT_DIR . '/infra/bootstrap_base.php');
require_once(ROOT_DIR . '/infra/KAutoloader.php');

KAutoloader::addClassPath(KAutoloader::buildPath(KALTURA_ROOT_PATH, "vendor", "propel", "*"));
KAutoloader::addClassPath(KAutoloader::buildPath(KALTURA_ROOT_PATH, "plugins", "*"));
KAutoloader::addClassPath(KAutoloader::buildPath(KALTURA_ROOT_PATH, "admin_console", "lib", "Kaltura"));
KAutoloader::setClassMapFilePath('../../../cache/classMap.cache');
KAutoloader::register();

date_default_timezone_set(kConf::get("date_default_timezone")); // America/New_York

KalturaLog::setLogger(new KalturaStdoutLogger());

DbManager::setConfig(kConf::getDB());
DbManager::initialize();


// stores the last handled admin kuser id, helps to restore in case of crash
$lastUserFile = 'last_admin_user';
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
		KalturaLog::log('-- admin kuser id ' . $lastUser);
		
		$new_kuser = new kuser();
		$new_login_data = new UserLoginData();
		
		list($firstName, $lastName) = kString::nameSplit($user->getFullName());
		
		$c = new Criteria();
		$c->addAnd(UserLoginDataPeer::LOGIN_EMAIL, $user->getEmail());
		$existing_login_data = UserLoginDataPeer::doSelectOne($c, $con);
		
		if ($existing_login_data)
		{
			KalturaLog::alert('!!! Existing login data found with id ['.$existing_login_data->getId().'] - skipping user id ['.$lastUser.']! !!!');
			echo '!!! Existing login data found with id ['.$existing_login_data->getId().'] - skipping user id ['.$lastUser.']! !!!';
			continue;
		}
		
		$new_login_data->setConfigPartnerId($user->getPartnerId());
		$new_login_data->setLoginEmail($user->getEmail());
		$new_login_data->setFirstName($firstName);
		$new_login_data->setLastName($lastName);
		$new_login_data->setSalt($user->getSalt());
		$new_login_data->setSha1Password($user->getSha1Password());
		$new_login_data->setCreatedAt($user->getCreatedAt());
		$new_login_data->setUpdatedAt($user->getUpdatedAt());
		$new_login_data->setLoginBlockedUntil($user->getLoginBlockedUntil());
		$new_login_data->setLoginAttempts($user->getLoginAttempts());
		$new_login_data->setPasswordHashKey($user->getPasswordHashKey());
		$new_login_data->setPasswordUpdatedAt($user->getPasswordUpdatedAt());
		$new_login_data->setPreviousPasswords($user->getPreviousPasswords());
		$new_login_data->setLastLoginPartnerId($user->getPartnerId());

		// check for existing kusers for this admin_kuser
		$c = new Criteria();
		$c->addAnd(kuserPeer::PUSER_ID, '__ADMIN__' . $user->getId());
		$existing_kusers = kuserPeer::doSelect($c, $con);
		
		if ($existing_kusers)
		{
			foreach ($existing_kusers as $kuser)
			{
				$kuser->setFirstName($firstName);
				$kuser->setLastName($lastName);
				$kuser->setEmail($user->getEmail());
				$kuser->setIsAdmin(true);
				$kuser->setIsRootUser(true);
			}
		}
		else
		{
			$new_kuser->setEmail($user->getEmail());
			$new_kuser->setScreenName($user->getScreenName());
			$new_kuser->setPartnerId($user->getPartnerId());
			$new_kuser->setFirstName($firstName);
			$new_kuser->setLastName($lastName);
			$new_kuser->setStatus(kuser::KUSER_STATUS_ACTIVE);
			$new_kuser->setIcon($user->getIcon());
			$new_kuser->setPicture($user->getPicture());
			$new_kuser->setPuserId('__ADMIN__'.$user->getId());
			$new_kuser->setIsAdmin(true);
			$new_kuser->setIsRootUser(true);
			if ($new_kuser->getPartnerId() == $admin_console_partner_id) {
				$partnerData = new Kaltura_AdminConsoleUserPartnerData();
				$partnerData->isPrimary = null;
 				$partnerData->role = null;
 				$new_kuser->setPartnerData(serialize($partnerData));
			}
		}
		
		if (!$dryRun) {
			KalturaLog::log('Saving new user_login_data with the following parameters: ');
			KalturaLog::log(print_r($new_login_data, true));
			$new_login_data->save(); // save
			
			if ($existing_kusers)
			{
				foreach ($existing_kusers as $kuser)
				{
					$kuser->setLoginDataId($new_login_data->getId());
					KalturaLog::log('Saving EXISTING kuser with the following parameters: ');
					KalturaLog::log(print_r($kuser, true));			
					$kuser->save(); // save
				}
			}
			else
			{
				$new_kuser->setLoginDataId($new_login_data->getId());
				KalturaLog::log('Saving NEW kuser with the following parameters: ');
				KalturaLog::log(print_r($new_kuser, true));			
				$new_kuser->save(); // save
			}
		}
		else {
			KalturaLog::log('DRY RUN - records are not being saved: ');
			KalturaLog::log('New user_login_data with the following parameters: ');
			KalturaLog::log(print_r($new_login_data, true));
			KalturaLog::log('Newkuser with the following parameters (login_data_id unknown): ');
			KalturaLog::log(print_r($new_kuser, true));
		}		
				
		file_put_contents($lastUserFile, $lastUser);
	}
	
	adminKuserPeer::clearInstancePool();
	kuserPeer::clearInstancePool();
	PartnerPeer::clearInstancePool();
	UserLogindataPeer::clearInstancePool();
	
	$users = getUsers($con, $lastUser, $userLimitEachLoop);
}

KalturaLog::log('Done');


function getUsers($con, $lastUser, $userLimitEachLoop)
{
	$c = new Criteria();
	$c->add(adminKuserPeer::ID, $lastUser, Criteria::GREATER_THAN);
	$c->addAscendingOrderByColumn(adminKuserPeer::ID);
	$c->setLimit($userLimitEachLoop);
	return adminKuserPeer::doSelect($c, $con);
}