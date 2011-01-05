<?php

$dryRun = true; //TODO: change for real run
if($argc > 1 && $argv[1] == 'realrun')
	$dryRun = false;
	
$stopFile = dirname(__FILE__).'/stop_puser_id_migration'; // creating this file will stop the script
$userLimitEachLoop = 10000;

//------------------------------------------------------

set_time_limit(0);

require_once(dirname(__FILE__).'/../../../bootstrap.php');

// stores the last handled admin kuser id, helps to restore in case of crash
$lastPuserKuserFile = 'last_puser_kuser';
$lastPuserKuser = 0;
if(file_exists($lastPuserKuserFile)) {
	$lastPuserKuser = file_get_contents($lastPuserKuserFile);
	KalturaLog::log('last user file already exists with value - '.$lastPuserKuser);
}
if(!$lastPuserKuser)
	$lastPuserKuser = 0;

$puserKusers = getPuserKusers($lastPuserKuser, $userLimitEachLoop);


while(count($puserKusers))
{
	foreach($puserKusers as $puserKuser)
	{
		if (file_exists($stopFile)) {
			die('STOP FILE CREATED');
		}

		$lastPuserKuser = $puserKuser->getId();
		KalturaLog::log('-- kuser id ' . $lastPuserKuser);
			
		
		$kuserId = $puserKuser->getKuserId();
		$partnerId = $puserKuser->getPartnerId();
		$puserId = $puserKuser->getPuserId();
		
		kuserPeer::setUseCriteriaFilter(false);
		$kuser = kuserPeer::retrieveByPK($kuserId);
		
		if (!$kuser)
		{
			KalturaLog::alert('ERROR - Kuser id ['.$kuserId.'] not found but pointed from puserKuser ['.$lastPuserKuser.']');
			continue;
		}
		
		if ($kuser->getPartnerId() != $partnerId)
		{
			KalturaLog::alert('ERROR - Partner IDs are not the same for puserKuser ['.$lastPuserKuser.'] with partnerId ['.$partnerId.'] and kuser ['.$kuser->getId().'] with partnerId ['.$kuser->getPartnerId().']');
			continue;
		}
				
		if (!$kuser->getPuserId())
		{
			if ($puserId)
			{
				$c = new Criteria();
				$c->addAnd(kuserPeer::PUSER_ID, $puserId, Criteria::EQUAL);
				$c->addAnd(kuserPeer::PARTNER_ID, $partnerId, Criteria::EQUAL);
				$otherKusers = kuserPeer::doSelect($c);
				if (count($otherKusers) > 0)
				{
					KalturaLog::alert('ERROR - Partner ['.$partnerId.'] already has a different kuser with puserId ['.$puserId.'] but puserKuser ['.$lastPuserKuser.'] is pointing to kuser ['.$kuserId.']');
					continue;
				}
				else
				{
					$kuser->setPuserId($puserId);
				}
			}
			else
			{
				KalturaLog::alert('ERROR - No puserId is set for puserKuser ['.$lastPuserKuser.'] or kuser ['.$kuserId.']');
				continue;
			}
		}
		else
		{
			if ($kuser->getPuserId() != $puserId)
			{
				KalturaLog::alert('ERROR - Puser ids are not the same for puserKuser ['.$lastPuserKuser.'] with puserId ['.$puserId.'] and kuser ['.$kuserId.'] with puserId ['.$kuser->getPuserId().']');
				continue;
			}	
		}
		
		
		if (!$dryRun)
		{
			KalturaLog::log('Saving kuser ['.$kuser->getId().'] of partner ['.$kuser->getPartnerId().'] with puserId ['.$kuser->getPuserId().']');
			$kuser->save();
		}
		else
		{
			KalturaLog::log('DRY RUN ONLY - Saving kuser ['.$kuser->getId().'] of partner ['.$kuser->getPartnerId().'] with puserId ['.$kuser->getPuserId().']');
		}		
				
		file_put_contents($lastPuserKuserFile, $lastPuserKuser);
	}
	
	kuserPeer::clearInstancePool();
	
	
	$puserKusers = getPuserKusers($lastPuserKuser, $userLimitEachLoop);
}

$msg = 'Done' . ($dryRun ? 'DRY RUN!' : 'REAL RUN!');
KalturaLog::log($msg);
echo $msg;

function getPuserKusers($lastPuserKuser, $userLimitEachLoop)
{
	PuserKuserPeer::clearInstancePool();
	$c = new Criteria();
	$c->add(PuserKuserPeer::ID, $lastPuserKuser, Criteria::GREATER_THAN);
	$c->addAscendingOrderByColumn(PuserKuserPeer::ID);
	$c->setLimit($userLimitEachLoop);
	PuserKuserPeer::setUseCriteriaFilter(false);
	$puserKusers =  PuserKuserPeer::doSelect($c);
	PuserKuserPeer::setUseCriteriaFilter(true);
	return $puserKusers;
}
