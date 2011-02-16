<?php
/**
 * @package deployment
 * @subpackage dragonfly.user_consolidation
 * 
 * Adds new partner custom data AdminLoginUsersQuota attribute with default value 3
 * Requires re-run after server code depoloy
 * Touch stop_partner_migration to stop execution
 */

$defaultLoginUsersQuota = 3; // must be set!
$dryRun = true; //TODO: change for real run
if($argc > 1 && $argv[1] == 'realrun')
	$dryRun = false;
	
$stopFile = dirname(__FILE__).'/stop_partner_migration'; // creating this file will stop the script
$partnerLimitEachLoop = 500;

//------------------------------------------------------


if (!$defaultLoginUsersQuota) {
	die('Must set $defaultLoginUsersQuota');
}

require_once(dirname(__FILE__).'/../../../bootstrap.php');


// stores the last handled admin kuser id, helps to restore in case of crash
$lastPartnerFile = '01.last_partner';
$lastPartner = -10;
if(file_exists($lastPartnerFile)) {
	$lastPartner = file_get_contents($lastPartnerFile);
	KalturaLog::log('last partner file already exists with value - '.$lastPartner);
}
if(!$lastPartner)
	$lastPartner = 0;

$partners = getPartners($lastPartner, $partnerLimitEachLoop);


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
	
	
	
	$partners = getPartners($lastPartner, $partnerLimitEachLoop);
}

$msg = 'Done - ' . ($dryRun ? 'DRY RUN!' : 'REAL RUN!');
KalturaLog::log($msg);
echo $msg;


function getPartners($lastPartner, $partnerLimitEachLoop)
{
	PartnerPeer::clearInstancePool();
	$c = new Criteria();
	$c->add(PartnerPeer::ID, $lastPartner, Criteria::GREATER_THAN);
	$c->addAscendingOrderByColumn(PartnerPeer::ID);
	$c->setLimit($partnerLimitEachLoop);
	PartnerPeer::setUseCriteriaFilter(false);
	$partners = PartnerPeer::doSelect($c);
	PartnerPeer::setUseCriteriaFilter(true);
	return $partners;
}

