<?php
/**
 * Enable VIRTUALEVENT_PLUGIN_PERMISSION to all partners
 *
 *
 * Examples:
 * php enableVirtualEventPermissionToAllPartners.php
 * php enableVirtualEventPermissionToAllPartners.php realrun
 *
 * @package Deployment
 * @subpackage updates
 */

$dryRun = true;
if (in_array('realrun', $argv))
	$dryRun = false;

$countLimitEachLoop = 500;
$offset = $countLimitEachLoop;

const VIRTUALEVENT_PLUGIN_PERMISSION = 'VIRTUALEVENT_PLUGIN_PERMISSION';

//------------------------------------------------------


require_once(__DIR__ . '/../../../deployment/bootstrap.php');

$con = myDbHelper::getConnection(myDbHelper::DB_HELPER_CONN_PROPEL2);
KalturaStatement::setDryRun($dryRun);

$c = new Criteria();
$c->addAscendingOrderByColumn(PartnerPeer::ID);
$c->addAnd(PartnerPeer::ID, 99, Criteria::GREATER_EQUAL);
$c->addAnd(PartnerPeer::STATUS,1, Criteria::EQUAL);
$c->setLimit($countLimitEachLoop);

$partners = PartnerPeer::doSelect($c, $con);

while (count($partners))
{
	foreach ($partners as $partner)
	{
		/* @var $partner Partner */
		$virtualEventPermission = PermissionPeer::getByNameAndPartner(VIRTUALEVENT_PLUGIN_PERMISSION, $partner->getId());
		if (!$virtualEventPermission)
		{
			$virtualEventPermission = new Permission();
			$virtualEventPermission->setType(PermissionType::SPECIAL_FEATURE);
			$virtualEventPermission->setPartnerId($partner->getId());
			$virtualEventPermission->setName(VIRTUALEVENT_PLUGIN_PERMISSION);
		}
		elseif ($virtualEventPermission->getStatus() == PermissionStatus::ACTIVE)
		{
			continue;
		}
		print("Set permission [" . VIRTUALEVENT_PLUGIN_PERMISSION . "] for partner id [". $partner->getId() ."]");
		$virtualEventPermission->setStatus(PermissionStatus::ACTIVE);
		$virtualEventPermission->save();
	}
	
	kMemoryManager::clearMemory();
	$c = new Criteria();
	$c->addAscendingOrderByColumn(PartnerPeer::ID);
	$c->addAnd(PartnerPeer::ID, 99, Criteria::GREATER_EQUAL);
	$c->addAnd(PartnerPeer::STATUS,1, Criteria::EQUAL);
	$c->setLimit($countLimitEachLoop);
	$c->setOffset($offset);
	
	$partners = PartnerPeer::doSelect($c, $con);
	$offset +=  $countLimitEachLoop;
}

print("Done");
