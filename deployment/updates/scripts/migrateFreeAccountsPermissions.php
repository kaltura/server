<?php
require_once(dirname(__FILE__).'/../../bootstrap.php');

$permissionName = 'FEATURE_END_USER_REPORTS';
$startUpdatedAt = null;
$startPartnerId = 0;
$limit = null;
$page = 200;

$dryRun = false;
if($argc == 1 || strtolower($argv[1]) != 'realrun')
{
	$dryRun = true;
	KalturaLog::alert('Using dry run mode');
}

if($argc > 2)
	$startUpdatedAt = $argv[2];
	
if($argc > 3)
	$limit = $argv[3];
	
$criteria = new Criteria();
$criteria->add(PartnerPeer::ID, $startPartnerId, Criteria::GREATER_THAN);
$criteria->add(PartnerPeer::PARTNER_PACKAGE, 1);
$criteria->addAscendingOrderByColumn(PartnerPeer::ID);

if($startUpdatedAt)
	$criteria->add(PartnerPeer::UPDATED_AT, $startUpdatedAt, Criteria::GREATER_THAN);
	
if($limit)
	$criteria->setLimit(min($page, $limit));
else
	$criteria->setLimit($page);

$partners = PartnerPeer::doSelect($criteria);
$migrated = 0;
while(count($partners) && (!$limit || $migrated < $limit))
{
	KalturaLog::info("Migrating [" . count($partners) . "] partners.");
	$migrated += count($partners);
	foreach($partners as $partner)
	{
		/* @var $partner Partner */

		$permission = PermissionPeer::getByNameAndPartner($permissionName, array($partner->getId(), 0));
		if(!$permission)
		{
			$permission = new Permission();
			$permission->setType(PermissionType::SPECIAL_FEATURE);
			$permission->setPartnerId($partner->getId());
		}
		$permission->setStatus(PermissionStatus::ACTIVE);
		KalturaStatement::setDryRun($dryRun);
		$permission->save();
		KalturaStatement::setDryRun(false);
		
		$startUpdatedAt = $partner->getUpdatedAt(null);
		$startPartnerId = $partner->getId();
		KalturaLog::info("Migrated partner [" . $partner->getId() . "] with updated at [$startUpdatedAt: " . $partner->getUpdatedAt() . "].");
	}
	kMemoryManager::clearMemory();

	$nextCriteria = clone $criteria;
	$nextCriteria->add(PartnerPeer::ID, $startPartnerId, Criteria::GREATER_THAN);
	$partners = PartnerPeer::doSelect($nextCriteria);
}

KalturaLog::info("Done");
