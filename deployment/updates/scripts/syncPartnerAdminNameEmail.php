<?php

$dryRun = true; //TODO: change for real run
if($argc > 1 && $argv[1] == 'realrun')
	$dryRun = false;
	
$stopFile = dirname(__FILE__).'/stop_partner_migration'; // creating this file will stop the script
$partnerLimitEachLoop = 1000;

//---------------------------------------------------------------
// This script sets the admin name and email for all partners where its missing
//---------------------------------------------------------------

require_once(dirname(__FILE__).'/../../bootstrap.php');

// stores the last handled admin kuser id, helps to restore in case of crash
$lastPartnerFile = 'last_'.basename(__FILE__);
$lastPartner = 1;
if(file_exists($lastPartnerFile)) {
	$lastPartner = file_get_contents($lastPartnerFile);
	KalturaLog::log('last partner file already exists with value - '.$lastPartner);
}
if(!$lastPartner)
	$lastPartner = 1;
	
$partners = getPartners($lastPartner, $partnerLimitEachLoop);


while(count($partners))
{
	foreach($partners as $partner)
	{
		if (file_exists($stopFile)) {
			die('STOP FILE CREATED');
		}

		KalturaLog::log('-- partner id ' . $partner->getId());
		
		// just refresh the account owner kuser id parameter, to set the missing admin name & email
		$ownerKuserId = $partner->getAccountOwnerKuserId();
		$partner->setAccountOwnerKuserId($ownerKuserId);
		
		if (!$dryRun) {
			KalturaLog::log('SAVED - partner ['.$partner->getId().'] owner kuser id ['.$ownerKuserId.']');
			$partner->save();			
		}
		else {
			KalturaLog::log('DRY RUN only - partner ['.$partner->getId().'] owner kuser id ['.$ownerKuserId.']');	
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
	$c->add(PartnerPeer::ADMIN_EMAIL, null, Criteria::EQUAL);
	$c->add(PartnerPeer::ADMIN_NAME, null, Criteria::EQUAL);
	$c->addAscendingOrderByColumn(PartnerPeer::ID);
	$c->setLimit($partnerLimitEachLoop);
	PartnerPeer::setUseCriteriaFilter(false);
	$partners = PartnerPeer::doSelect($c);
	PartnerPeer::setUseCriteriaFilter(true);
	return $partners;
}

