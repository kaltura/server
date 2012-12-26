<?php
/**
 * This script set the partner default delivery type to auto for all partners that use flavors v2 set
 * 
 * Usage:
 * php migrateFlavorsV2autoDeliveryType.php [realrun/dryrun] [startUpdatedAt] [limit]
 * 
 * Defaults: 
 * 		dryrun, 
 * 		startUpdatedAt is zero, 
 * 		no limit
 * 
 * @package deploy
 * @subpackage update
 */
require_once(dirname(__FILE__).'/../../bootstrap.php');

$dryRun = true;
if(isset($argv[1]) && strtolower($argv[1]) == 'realrun')
{
	$dryRun = false;
}
else 
{
	KalturaLog::info('Using dry run mode');
}
KalturaStatement::setDryRun($dryRun);

$startUpdatedAt = null;
if (isset($argv[2]))
{
	$startUpdatedAt = $argv[2];
}

$limit = null;
if(isset($argv[3]))
{
	$limit = $argv[3];
}

$page = 500;

$criteria = new Criteria();
$criteria->add(PermissionPeer::PARTNER_ID, 99, Criteria::GREATER_THAN);
$criteria->add(PermissionPeer::TYPE, PermissionType::SPECIAL_FEATURE);
$criteria->add(PermissionPeer::NAME, PermissionName::FEATURE_V2_FLAVORS);
$criteria->addAscendingOrderByColumn(PermissionPeer::UPDATED_AT);
if ($startUpdatedAt)
	$criteria->add(PermissionPeer::UPDATED_AT, $startUpdatedAt, Criteria::GREATER_EQUAL);

if($limit)
	$criteria->setLimit(min($page, $limit));
else
	$criteria->setLimit($page);
	
$permissions = PermissionPeer::doSelect($criteria);
$migrated = 0;
$lastPermissionId = null;
while (count($permissions) && (!$limit || $migrated < $limit))
{
	KalturaLog::debug("Migrating [" . count($permissions) . "] permissions");
	$migrated += count($permissions);
	foreach ($permissions as $permission)
	{
		/* @var $permission Permission */
		$lastPermissionId = $permission->getId();
		$startUpdatedAt = $permission->getUpdatedAt(null);
		KalturaLog::debug("Migrating permission [$lastPermissionId] Updated at [$startUpdatedAt]");
		$partner = PartnerPeer::retrieveByPK($permission->getPartnerId());
		if(!$partner)
		{
			KalturaLog::err("Partner [" . $permission->getPartnerId() . "] not found");
			continue;
		}
			
		KalturaLog::debug("Migrating partner [" . $permission->getPartnerId() . "]");
		
		$partner->putInCustomData('defaultDeliveryType', 'auto');
		$partner->save();
	}
	
	kMemoryManager::clearMemory();

	$nextCriteria = clone $criteria;
	$criteria->add(PermissionPeer::ID, $lastPermissionId, Criteria::NOT_EQUAL);
	$criteria->add(PermissionPeer::UPDATED_AT, $startUpdatedAt, Criteria::GREATER_EQUAL);
	$permissions = PermissionPeer::doSelect($nextCriteria);
}

KalturaLog::info("Done - migrated $migrated items");
