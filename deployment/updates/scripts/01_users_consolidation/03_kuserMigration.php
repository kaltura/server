<?php
/**
 * @package deployment
 * @subpackage dragonfly.user_consolidation
 * 
 * 1. Splits name into first name and last name
 * 2. Set is admin to false to all kusers
 * 3. Copy login data to user_login_data table if salt and sha1_password are not null and user.login allowed for the partner
 * 
 * Requires re-run after server code depoloy
 * Touch stop_user_migration to stop execution
 */

$dryRun = true; //TODO: change for real run
if($argc > 1 && $argv[1] == 'realrun')
	$dryRun = false;
	
$stopFile = dirname(__FILE__).'/stop_user_migration'; // creating this file will stop the script
$userLimitEachLoop = 500;

//------------------------------------------------------

set_time_limit(0);

require_once(dirname(__FILE__).'/../../../bootstrap.php');

// stores the last handled admin kuser id, helps to restore in case of crash
$lastUserFile = '03.last_kuser';
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
		
		if ($user->getPartnerId() == PartnerPeer::GLOBAL_PARTNER) {
			KalturaLog::log('Skipping partner 0');
			continue;
		}
			
		list($firstName, $lastName) = kString::nameSplit($user->getFullName());
		$user->setFirstName($firstName);
		$user->setLastName($lastName);
		
		$user->setIsAdmin(false);
		
		$new_login_data = null;
		
		if ($user->getSalt() && $user->getSha1Password() && in_array($user->getPartnerId(), $loginPartnerIds) )
		{
			$newTempEmail = $user->getEmail();
			
			$c = new Criteria();
			$c->addAnd(adminKuserPeer::EMAIL, $newTempEmail, Criteria::EQUAL);
			$adminKuser = adminKuserPeer::doSelectOne($c);
			
			if ($adminKuser) {
				if ($user->getPartnerId() === $adminKuser->getPartnerId() && $user->getPuserId() === '__ADMIN__' . $adminKuser->getId()) {
					continue;
				}
				$newTempEmail = 'kuser_'.$user->getId().'_'.$user->getEmail();
				$msg = 'NOTICE - kuser ['.$lastUser.'] of partner ['.$user->getPartnerId().'] is set with email ['.$user->getEmail().'] already used by admin_kuser id ['.$adminKuser->getId().'] of partner ['.$adminKuser->getPartnerId().'] - setting kusers login email to ['.$newTempEmail.']!';
				KalturaLog::notice($msg);
			}
			
			if (!kString::isEmailString($user->getEmail())) {
				$newTempEmail = 'kuser_'.$user->getId().'_'.$user->getEmail();
				$msg = 'NOTICE - kuser ['.$lastUser.'] of partner ['.$user->getPartnerId().'] is set with invalid email ['.$user->getEmail().'] - setting kusers login email to ['.$newTempEmail.']!'; 
				KalturaLog::notice($msg);
			}
			
			
			// user can login - add a user_login_data record
			$existingLoginData = UserLoginDataPeer::getByEmail($newTempEmail);
			if ($existingLoginData) {
				$msg = 'NOTICE - login data for the same email ['.$newTempEmail.'] partner id ['.$existingLoginData->getConfigPartnerId().'] already exists - setting kusers login email to';
				
				$newTempEmail = 'kuser_'.$user->getId().'_'.$user->getEmail();
				while ($temp = UserLoginDataPeer::getByEmail($newTempEmail)) {
					$newTempEmail = '_'.$newTempEmail;
				}
				
				$msg .= ' ['.$newTempEmail.']!';
				KalturaLog::notice($msg);
			}
			

			$new_login_data = new UserLoginData();
			$new_login_data->setConfigPartnerId($user->getPartnerId());
			$new_login_data->setLoginEmail($newTempEmail); 
			$new_login_data->setFirstName($user->getFirstName());
			$new_login_data->setLastName($user->getLastName());
			$new_login_data->setSalt($user->getSalt());
			$new_login_data->setSha1Password($user->getSha1Password());
			$new_login_data->setCreatedAt($user->getCreatedAt());
			$new_login_data->setUpdatedAt($user->getUpdatedAt());
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