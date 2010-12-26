<?php

$defaultLoginUsersQuota = 5; // must be set!
$dryRun = true; //TODO: change for real run
if($argc > 1 && $argv[1] == 'realrun')
	$dryRun = false;
	
$stopFile = dirname(__FILE__).'/stop_partner_migration'; // creating this file will stop the script
$partnerLimitEachLoop = 1000;

//------------------------------------------------------


if (!$defaultLoginUsersQuota) {
	die('Must set $defaultLoginUsersQuota');
}

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
$lastPartnerFile = 'last_partner';
$lastPartner = -10;
if(file_exists($lastPartnerFile)) {
	$lastPartner = file_get_contents($lastPartnerFile);
	KalturaLog::log('last partner file already exists with value - '.$lastPartner);
}
if(!$lastPartner)
	$lastPartner = 0;

$con = myDbHelper::getConnection(myDbHelper::DB_HELPER_CONN_PROPEL2);
	
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
		
		$partner->setAdminLoginUsersQuota($defaultLoginUsersQuota);
		if ($partner->getId() == -2) {
			$partner->setAdminLoginUsersQuota(-1);
		}
		
		if (!$dryRun) {
			KalturaLog::log('SAVED - partner ['.$partner->getId().'] set with login users quota value of '.$partner->getAdminLoginUsersQuota());
			$partner->save();			
		}
		else {
			KalturaLog::log('DRY RUN only - partner ['.$partner->getId().'] set with login users quota value of '.$partner->getAdminLoginUsersQuota());	
		}		
				
		file_put_contents($lastPartnerFile, $lastPartner);
	}
	
	PartnerPeer::clearInstancePool();
	
	$partners = getPartners($con, $lastPartner, $partnerLimitEachLoop);
}

KalturaLog::log('Done' . $dryRun ? 'REAL RUN!' : 'DRY RUN!');
echo 'Done' . $dryRun ? 'REAL RUN!' : 'DRY RUN!';


function getPartners($con, $lastPartner, $partnerLimitEachLoop)
{
	$c = new Criteria();
	$c->add(PartnerPeer::ID, $lastPartner, Criteria::GREATER_THAN);
	$c->addAscendingOrderByColumn(PartnerPeer::ID);
	$c->setLimit($partnerLimitEachLoop);
	return PartnerPeer::doSelect($c, $con);
}

