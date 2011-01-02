<?php

$dryRun = true; //TODO: change for real run
if($argc > 1 && $argv[1] == 'realrun')
	$dryRun = false;
	
$admin_console_partner_id = -2;
$stopFile = dirname(__FILE__).'/stop_user_migration'; // creating this file will stop the script
$userLimitEachLoop = 1000; //TODO: change

//------------------------------------------------------

require_once(dirname(__FILE__).'/../../../bootstrap.php');

// stores the last handled admin kuser id, helps to restore in case of crash
$lastUserFile = 'last_system_user';
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
		KalturaLog::log('-- system user id ' . $lastUser);
		
		$new_kuser = new kuser();
		$new_login_data = new UserLoginData();
				
		$new_kuser->setEmail($user->getEmail());
		$new_kuser->setCreatedAt($user->getCreatedAt());
		$new_kuser->setUpdatedAt($user->getUpdatedAt());
		$new_kuser->setScreenName($user->getFirstName().' '.$user->getLastName());
		$new_kuser->setPartnerId($admin_console_partner_id);
		$new_kuser->setFirstName($user->getFirstName());
		$new_kuser->setLastName($user->getLastName());
		
		if ($user->getStatus() == SystemUser::SYSTEM_USER_ACTIVE) {
			$new_kuser->setStatus(KuserStatus::ACTIVE);
		}
		else {
			$new_kuser->setStatus(KuserStatus::BLOCKED);
		}
		
		$new_kuser->setPuserId($user->getEmail());
		$new_kuser->setIsAdmin(true);
		$partnerData = new Kaltura_AdminConsoleUserPartnerData();
		$partnerData->isPrimary = $user->getIsPrimary();
 		$partnerData->role = $user->getRole();
 		$new_kuser->setPartnerData(serialize($partnerData));
		
		$c = new Criteria();
		$c->addAnd(UserLoginDataPeer::LOGIN_EMAIL, $user->getEmail());
		$existing_login_data = UserLoginDataPeer::doSelectOne($c);
		
		if ($existing_login_data)
		{
			KalturaLog::log('Existing user_login_data record with same email found with id ['.$existing_login_data->getId().']');
			if ($existing_login_data->getSalt() == $user->getSalt() && $existing_login_data->getSha1Password() == $user->getSha1Password()) {
				$new_kuser->setLoginDataId($existing_login_data->getId());
			}
			else {
				KalturaLog::alert('!!! Existing user_login_data record with different password found with id ['.$existing_login_data->getId().'] skipping user id ['.$lastUser.']');
				echo '!!! Existing user_login_data record with different password found with id ['.$existing_login_data->getId().'] skipping user id ['.$lastUser.']';
				continue;
			}
		}
		else {
			$new_login_data->setConfigPartnerId($admin_console_partner_id);
			$new_login_data->setLoginEmail($user->getEmail());
			$new_login_data->setFirstName($user->getFirstName());
			$new_login_data->setLastName($user->getLastName());
			$new_login_data->setSalt($user->getSalt());
			$new_login_data->setSha1Password($user->getSha1Password());
			$new_login_data->setCreatedAt($user->getCreatedAt());
			$new_login_data->setUpdatedAt($user->getUpdatedAt());
			$new_login_data->setLoginBlockedUntil(null);
			$new_login_data->setLoginAttempts(0);
			$new_login_data->setPasswordUpdatedAt(time());
			$new_login_data->setLastLoginPartnerId($admin_console_partner_id);
		}

		
		if (!$dryRun) {
			if (!$existing_login_data) {
				KalturaLog::log('Saving new user_login_data with the following parameters: ');
				KalturaLog::log(print_r($new_login_data, true));
				$new_login_data->save(); // save		
				$new_kuser->setLoginDataId($new_login_data->getId());
			}

			KalturaLog::log('Saving new kuser with the following parameters: ');
			KalturaLog::log(print_r($new_kuser, true));			
			$new_kuser->save(); // save
		}
		else {
			KalturaLog::log('DRY RUN - records are not being saved: ');
			if (!$new_kuser->getLoginDataId())
			{
				KalturaLog::log('New user_login_data with the following parameters: ');
				KalturaLog::log(print_r($new_login_data, true));
			}
			KalturaLog::log('Newkuser with the following parameters (login_data_id unknown if not yet exists): ');
			KalturaLog::log(print_r($new_kuser, true));
		}		
				
		file_put_contents($lastUserFile, $lastUser);
	}
	
	SystemUserPeer::clearInstancePool();
	kuserPeer::clearInstancePool();
	PartnerPeer::clearInstancePool();
	UserLogindataPeer::clearInstancePool();
	
	$users = getUsers($lastUser, $userLimitEachLoop);
}

KalturaLog::log('Done' . $dryRun ? 'DRY RUN!' : 'REAL RUN!');
echo 'Done' . $dryRun ? 'DRY RUN!' : 'REAL RUN!';

function getUsers($lastUser, $userLimitEachLoop)
{
	$c = new Criteria();
	$c->add(SystemUserPeer::ID, $lastUser, Criteria::GREATER_THAN);
	$c->addAscendingOrderByColumn(SystemUserPeer::ID);
	$c->setLimit($userLimitEachLoop);
	return SystemUserPeer::doSelect($c);
}