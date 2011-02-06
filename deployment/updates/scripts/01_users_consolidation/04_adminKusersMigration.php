<?php

$dryRun = true; //TODO: change for real run
if($argc > 1 && $argv[1] == 'realrun')
	$dryRun = false;
	
$stopFile = dirname(__FILE__).'/stop_user_migration'; // creating this file will stop the script
$userLimitEachLoop = 1000;
$admin_console_partner_id = -2;

//------------------------------------------------------

require_once(dirname(__FILE__).'/../../../bootstrap.php');

// stores the last handled admin kuser id, helps to restore in case of crash
$lastUserFile = 'last_admin_user';
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
		KalturaLog::log('-- admin kuser id ' . $lastUser);
		
		$new_kuser = new kuser();
		$new_login_data = new UserLoginData();
		$partner = PartnerPeer::retrieveByPK($user->getPartnerId());
		if (!$partner) {
			KalturaLog::alert('!!! ERROR - Partner ID ['.$user->getPartnerId().'] not found on DB but set for admin user id ['.$lastUser.'] !!!');
			echo '!!! ERROR - Partner ID ['.$user->getPartnerId().'] not found on DB but set for admin user id ['.$lastUser.'] !!!';
			continue;
		}
		
		list($firstName, $lastName) = kString::nameSplit($user->getFullName());
		
		$c = new Criteria();
		$c->addAnd(UserLoginDataPeer::LOGIN_EMAIL, $user->getEmail());
		$existing_login_data = UserLoginDataPeer::doSelectOne($c);
		
		if ($existing_login_data)
		{
			KalturaLog::alert('!!! ERROR - Existing login data found with id ['.$existing_login_data->getId().'] - skipping user id ['.$lastUser.']! !!!');
			echo '!!! ERROR - Existing login data found with id ['.$existing_login_data->getId().'] - skipping user id ['.$lastUser.']! !!!';
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
		$c->addAnd(kuserPeer::PUSER_ID, '__ADMIN__' . $user->getId(), Criteria::EQUAL);
		$c->addAnd(kuserPeer::PARTNER_ID, $user->getPartnerId(), Criteria::EQUAL);
		$existing_kuser = kuserPeer::doSelectOne($c);
		
		if ($existing_kuser)
		{
			$existing_kuser->setFirstName($firstName);
			$existing_kuser->setLastName($lastName);
			$existing_kuser->setEmail($user->getEmail());
			$existing_kuser->setIsAdmin(true);
		}
		else
		{
			$new_kuser->setEmail($user->getEmail());
			$new_kuser->setScreenName($user->getScreenName());
			$new_kuser->setPartnerId($user->getPartnerId());
			$new_kuser->setFirstName($firstName);
			$new_kuser->setLastName($lastName);
			$new_kuser->setStatus(KuserStatus::ACTIVE);
			$new_kuser->setIcon($user->getIcon());
			$new_kuser->setPicture($user->getPicture());
			$new_kuser->setPuserId('__ADMIN__'.$user->getId());
			$new_kuser->setIsAdmin(true);
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
			
			if ($existing_kuser)
			{
				$existing_kuser->setLoginDataId($new_login_data->getId());
				KalturaLog::log('Saving EXISTING kuser with the following parameters: ');
				KalturaLog::log(print_r($existing_kuser, true));			
				$existing_kuser->save(); // save
				$partner->setAccountOwnerKuserId($existing_kuser->getId(), false);
			}
			else
			{
				$new_kuser->setLoginDataId($new_login_data->getId());
				KalturaLog::log('Saving NEW kuser with the following parameters: ');
				KalturaLog::log(print_r($new_kuser, true));			
				$new_kuser->save(); // save
				$partner->setAccountOwnerKuserId($new_kuser->getId(), false);
			}			
			KalturaLog::log('Saving partner ['.$partner->getId().'] with account owner kuser ID ['.$partner->getAccountOwnerKuserId().']');
			$partner->save();
		}
		else {
			KalturaLog::log('DRY RUN - records are not being saved: ');
			KalturaLog::log('New user_login_data with the following parameters: ');
			KalturaLog::log(print_r($new_login_data, true));
			KalturaLog::log('Newkuser with the following parameters (login_data_id unknown): ');
			KalturaLog::log(print_r($new_kuser, true));
			KalturaLog::log('DRY RUN - saving partner ['.$partner->getId().'] with account owner kuser ID ['.$partner->getAccountOwnerKuserId().']');
			
		}		
				
		file_put_contents($lastUserFile, $lastUser);
	}
	
	
	kuserPeer::clearInstancePool();
	PartnerPeer::clearInstancePool();
	UserLogindataPeer::clearInstancePool();
	
	$users = getUsers($lastUser, $userLimitEachLoop);
}

$msg = 'Done - ' . ($dryRun ? 'DRY RUN!' : 'REAL RUN!');
KalturaLog::log($msg);
echo $msg;

function getUsers($lastUser, $userLimitEachLoop)
{
	adminKuserPeer::clearInstancePool();
	$c = new Criteria();
	$c->add(adminKuserPeer::ID, $lastUser, Criteria::GREATER_THAN);
	$c->addAscendingOrderByColumn(adminKuserPeer::ID);
	$c->setLimit($userLimitEachLoop);
	adminKuserPeer::setUseCriteriaFilter(false);
	$users = adminKuserPeer::doSelect($c);
	adminKuserPeer::setUseCriteriaFilter(true);
	return $users;
}