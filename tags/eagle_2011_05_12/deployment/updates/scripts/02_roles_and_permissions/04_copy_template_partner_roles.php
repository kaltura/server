<?php
/**
 * @package deployment
 * @subpackage dragonfly.roles_and_permissions
 * 
 * Copy template roles to all partners
 * 
 * Requires re-run after server code depoloy
 * Touch stop_role_copy to stop execution
 */

$dryRun = true; //TODO: change for real run
if($argc > 1 && $argv[1] == 'realrun')
	$dryRun = false;
	
$stopFile = dirname(__FILE__).'/stop_role_copy'; // creating this file will stop the script
$partnerLimitEachLoop = 20;

//------------------------------------------------------

require_once(dirname(__FILE__).'/../../../bootstrap.php');


// stores the last handled admin kuser id, helps to restore in case of crash
$lastPartnerFile = '04.role_copy_last_partner';
$lastPartner = 1;
if(file_exists($lastPartnerFile)) {
	$lastPartner = file_get_contents($lastPartnerFile);
	KalturaLog::log('last partner file already exists with value - '.$lastPartner);
}
if(!$lastPartner)
	$lastPartner = 1;

$templatePartnerId = kConf::get('template_partner_id');

$c = new Criteria();
$c->addAnd(UserRolePeer::PARTNER_ID, $templatePartnerId, Criteria::EQUAL);
$templatePartnerRoles = UserRolePeer::doSelect($c);

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
	
	UserRolePeer::clearInstancePool();
	
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

