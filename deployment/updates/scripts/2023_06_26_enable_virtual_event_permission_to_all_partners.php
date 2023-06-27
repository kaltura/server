<?php
/**
 * Enable VIRTUALEVENT_PLUGIN_PERMISSION to all partners
 *
 *
 * Examples:
 * php 2023_06_26_enable_virtual_event_permission_to_all_partners.php
 * php 2023_06_26_enable_virtual_event_permission_to_all_partners.php realrun
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


require_once (__DIR__ . '/../../bootstrap.php');

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
		$eventPlatformPermission = PermissionPeer::getByNameAndPartner(VIRTUALEVENT_PLUGIN_PERMISSION, $partner->getId());
		if (!$eventPlatformPermission)
		{
			$eventPlatformPermission = new Permission();
			$eventPlatformPermission->setType(PermissionType::SPECIAL_FEATURE);
			$eventPlatformPermission->setPartnerId($partner->getId());
			$eventPlatformPermission->setName(VIRTUALEVENT_PLUGIN_PERMISSION);
		}
		elseif ($eventPlatformPermission->getStatus() == PermissionStatus::ACTIVE)
		{
			continue;
		}
		KalturaLog::debug("Set permission [" . VIRTUALEVENT_PLUGIN_PERMISSION . "] for partner id [". $partner->getId() ."]");
		$eventPlatformPermission->setStatus(PermissionStatus::ACTIVE);
		$eventPlatformPermission->save();
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

KalturaLog::debug("Done");
