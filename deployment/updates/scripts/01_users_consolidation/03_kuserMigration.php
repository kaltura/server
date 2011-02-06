<?php

$dryRun = true; //TODO: change for real run
if($argc > 1 && $argv[1] == 'realrun')
	$dryRun = false;
	
$stopFile = dirname(__FILE__).'/stop_user_migration'; // creating this file will stop the script
$userLimitEachLoop = 1000;

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
$loginPartnerIds = getLoginPartners();

while(count($users))
{
	foreach($users as $user)
	{
		if (file_exists($stopFile)) {
			die('STOP FILE CREATED');
		}

		$lastUser = $user->getId();
		KalturaLog::log('-- kuser id ' . $lastUser);
			
		list($firstName, $lastName) = kString::nameSplit($user->getFullName());
		$user->setFirstName($firstName);
		$user->setLastName($lastName);
		
		$user->setIsAdmin(false);
		
		$new_login_data = null;
		
		if ($user->getSalt() && in_array($user->getPartnerId(), $loginPartnerIds) )
		{
			 // user can login - add a user_login_data record
			$existingLoginData = UserLoginDataPeer::getByEmail($user->getEmail());
			if (!$existingLoginData) {
				$new_login_data = new UserLoginData();
				$new_login_data->setConfigPartnerId($user->getPartnerId());
				$new_login_data->setLoginEmail($user->getEmail()); 
				$new_login_data->setFirstName($user->getFirstName());
				$new_login_data->setLastName($user->getLastName());
				$new_login_data->setSalt($user->getSalt());
				$new_login_data->setSha1Password($user->getSha1Password());
				$new_login_data->setCreatedAt($user->getCreatedAt());
				$new_login_data->setUpdatedAt($user->getUpdatedAt());
			}
			else {
				if ($existingLoginData->getSalt() != $user->getSalt() || $existingLoginData->getSha1Password() != $user->getSha1Password()) {
					KalturaLog::alert('ERROR - login data for the same email ['.$user->getEmail().'] already exists with a different password - kuser ['.$user->getId().'] will not be able to login!');
					echo 'ERROR - login data for the same email ['.$user->getEmail().'] already exists with a different password - kuser ['.$user->getId().'] will not be able to login!';
					continue;
				}
				if (kuserPeer::getByLoginDataAndPartner($existingLoginData->getId(), $user->getPartnerId())) {
					KalturaLog::alert('ERROR - another kuser with the same login data id ['.$existingLoginData->getId().'] already exists for partner ['.$user->getPartnerId().']');
					echo 'ERROR - another kuser with the same login data id ['.$existingLoginData->getId().'] already exists for partner ['.$user->getPartnerId().']';
					continue;
				}
				KalturaLog::log('Kuser ['.$user->getId().'] is set with existing login data id ['.$existingLoginData->getId().']');
				$user->setLoginDataId($existingLoginData->getId());
			}
		}				
		
		if (!$dryRun) {
			if ($new_login_data) {
				KalturaLog::log('Saving new user_login_data with the following parameters: '.PHP_EOL);
				KalturaLog::log(print_r($new_login_data, true));
				$new_login_data->save(); // save
				$user->setLoginDataId($new_login_data->getId());
			}
			else {
				KalturaLog::log('User ['.$user->getId().'] has no login data'.PHP_EOL);
			}
			KalturaLog::log('Saving new kuser with the following parameters: '.PHP_EOL);
			KalturaLog::log(print_r($user, true));			
			$user->save(); // save
		}
		else {
			KalturaLog::log('DRY RUN - records are not being saved: '.PHP_EOL);
			KalturaLog::log('New user_login_data with the following parameters: '.PHP_EOL);
			KalturaLog::log(print_r($new_login_data, true));
			KalturaLog::log('Newkuser with the following parameters (login_data_id unknown): '.PHP_EOL);
			KalturaLog::log(print_r($user, true));
		}		
				
		file_put_contents($lastUserFile, $lastUser);
	}
	
	
	
	UserLoginDataPeer::clearInstancePool();
	
	$users = getUsers($lastUser, $userLimitEachLoop);
}

$msg = 'Done - ' . ($dryRun ? 'DRY RUN!' : 'REAL RUN!');
KalturaLog::log($msg);
echo $msg;

function getUsers($lastUser, $userLimitEachLoop)
{
	kuserPeer::clearInstancePool();
	$c = new Criteria();
	$c->add(kuserPeer::ID, $lastUser, Criteria::GREATER_THAN);
	$c->addAscendingOrderByColumn(kuserPeer::ID);
	$c->setLimit($userLimitEachLoop);
	kuserPeer::setUseCriteriaFilter(false);
	$users =  kuserPeer::doSelect($c);
	kuserPeer::setUseCriteriaFilter(true);
	return $users;
}

function getLoginPartners()
{
	PartnerPeer::clearInstancePool();
	
	$c = new Criteria();
	$c1 = $c->getNewCriterion(PartnerPeer::SERVICE_CONFIG_ID, 'services-paramount-mobile.ct');
	$c2 = $c->getNewCriterion(PartnerPeer::SERVICE_CONFIG_ID, 'services-disney-mediabowl.ct');
	
	$c1->addOr($c2);
	
	$c->add($c1);
	PartnerPeer::setUseCriteriaFilter(false);
	$partners = PartnerPeer::doSelect($c);
	PartnerPeer::setUseCriteriaFilter(true);
	$ids = array();
	foreach ($partners as $par) {
		$ids[] = $par->getId();
	}
	return $ids;
}