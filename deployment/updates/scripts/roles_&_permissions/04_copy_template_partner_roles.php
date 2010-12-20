<?php

$dryRun = true; //TODO: change for real run
if($argc > 1 && $argv[1] == 'realrun')
	$dryRun = false;
	
$stopFile = dirname(__FILE__).'/stop_role_copy'; // creating this file will stop the script
$partnerLimitEachLoop = 20;

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
$lastPartnerFile = 'role_copy_last_partner';
$lastPartner = -10;
if(file_exists($lastPartnerFile)) {
	$lastPartner = file_get_contents($lastPartnerFile);
	KalturaLog::log('last partner file already exists with value - '.$lastPartner);
}
if(!$lastPartner)
	$lastPartner = 0;

$con = myDbHelper::getConnection(myDbHelper::DB_HELPER_CONN_PROPEL2);

$templatePartnerId = kConf::get('template_partner_id');

$c = new Criteria();
$c->addAnd(UserRolePeer::PARTNER_ID, $templatePartnerId, Criteria::EQUAL);
$templatePartnerRoles = UserRolePeer::doSelect($c);

$partners = getPartners($con, $lastPartner, $partnerLimitEachLoop);

while(count($partners))
{
	foreach($partners as $partner)
	{
		if (file_exists($stopFile)) {
			die('STOP FILE CREATED');
		}

		$lastPartner = $partner->getId();
		KalturaLog::log('-- partner id ' . $lastPartner);
		
		if ($lastPartner == PartnerPeer::GLOBAL_PARTNER || $lastPartner == $templatePartnerId) {
			KalturaLog::log('Skipping partner ['.$lastPartner.']');
			continue;
		}
		
		$newPartnerRoles = array();
		
		foreach ($templatePartnerRoles as $role)
		{
			$newRole = $role->copyToPartner($partner->getId());
			$newPartnerRoles[] = $newRole;
		}
		
		if (!$dryRun) {
			foreach ($newPartnerRoles as $newRole) {
				KalturaLog::log('Saving role name ['.$newRole->getName().'] for partner ['.$partner->getId().']');
				$newRole->save();
			}
		}
		else {
			// dry run - no saving!
			foreach ($newPartnerRoles as $newRole) {
				KalturaLog::log('DRY RUN ONLY - Saving role name ['.$newRole->getName().'] for partner ['.$partner->getId().']');
		
			}
		}
	
		file_put_contents($lastPartnerFile, $lastPartner);
	}
	
	PartnerPeer::clearInstancePool();
	UserRolePeer::clearInstancePool();
	
	$partners = getPartners($con, $lastPartner, $partnerLimitEachLoop);
}

KalturaLog::log('Done');


function getPartners($con, $lastPartner, $partnerLimitEachLoop)
{
	$c = new Criteria();
	$c->add(PartnerPeer::ID, $lastPartner, Criteria::GREATER_THAN);
	$c->addAscendingOrderByColumn(PartnerPeer::ID);
	$c->setLimit($partnerLimitEachLoop);
	return PartnerPeer::doSelect($c, $con);
}

