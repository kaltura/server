<?php

$dryRun = true; //TODO: change for real run
if($argc > 1 && $argv[1] == 'realrun')
	$dryRun = false;
	
$stopFile = dirname(__FILE__).'/stop_partner_migration'; // creating this file will stop the script
$partnerLimitEachLoop = 1000;

//---------------------------------------------------------------
// This script enables analytics tab for all partners with KMC 3
//---------------------------------------------------------------

require_once(dirname(__FILE__).'/../../bootstrap.php');

// stores the last handled admin kuser id, helps to restore in case of crash
$lastPartnerFile = 'last_partner_analytics';
$lastPartner = 0;
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
		
		$kmcVersion = $partner->getKmcVersion();
		
		if ($kmcVersion != 3) {
			KalturaLog::log('Partner ['.$lastPartner.'] has kmc version ['.$kmcVersion.'] - skipping!');
			continue;
		}

		$partner->setEnableAnalyticsTab(true);
		
		if (!$dryRun) {
			KalturaLog::log('SAVED - partner ['.$lastPartner.'] set with enabled analytics tab!');
			$partner->save();			
		}
		else {
			KalturaLog::log('DRY RUN only - partner ['.$lastPartner.'] set with enabled analytics tab!');	
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

