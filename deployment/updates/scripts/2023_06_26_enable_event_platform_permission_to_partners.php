<?php
/**
 * Enable FEATURE_EVENT_PLATFORM_PERMISSION to partners that have VIRTUALEVENT_PLUGIN_PERMISSION enabled
 *
 *
 * Examples:
 * php 2023_01_03_enable_studio_V3_permission_to_all_partners.php
 * php 2023_01_03_enable_studio_V3_permission_to_all_partners.php realrun
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
const FEATURE_EVENT_PLATFORM_PERMISSION = 'FEATURE_EVENT_PLATFORM_PERMISSION';

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
		$virtualEventPermission = PermissionPeer::getByNameAndPartner(VIRTUALEVENT_PLUGIN_PERMISSION, $partner->getId());
		if (!$virtualEventPermission || $virtualEventPermission->getStatus() != PermissionStatus::ACTIVE)
		{
			continue;
		}
		
		KalturaLog::debug("Set permission [" . FEATURE_EVENT_PLATFORM_PERMISSION . "] for partner id [". $partner->getId() ."]");
		$eventPlatformPermission = PermissionPeer::getByNameAndPartner(FEATURE_EVENT_PLATFORM_PERMISSION, $partner->getId());
		if (!$eventPlatformPermission)
		{
			$eventPlatformPermission = new Permission();
			$eventPlatformPermission->setType(PermissionType::SPECIAL_FEATURE);
			$eventPlatformPermission->setPartnerId($partner->getId());
			$eventPlatformPermission->setName(FEATURE_EVENT_PLATFORM_PERMISSION);
		}
		
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
