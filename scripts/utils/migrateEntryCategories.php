<?php
require_once(dirname(__FILE__).'/../bootstrap.php');

$partnerId = null;
$startEntryIntId = null;
$limit = null;
$page = 200;

if($argc > 1)
	$partnerId = $argv[1];

if($argc > 2)
	$startEntryIntId = $argv[2];
	
if($argc > 3)
	$limit = $argv[3];
	
$criteria = new Criteria();
$criteria->addAscendingOrderByColumn(entryPeer::INT_ID);

if($partnerId)
	$criteria->add(entryPeer::PARTNER_ID, $partnerId);
	
if($startEntryIntId)
	$criteria->add(entryPeer::INT_ID, $startEntryIntId, Criteria::GREATER_THAN);
	
if($limit)
	$criteria->setLimit(min($page, $limit));
else
	$criteria->setLimit($page);

$entries = entryPeer::doSelect($criteria);
while(count($entries))
{
	KalturaLog::info("Migrating [" . count($entries) . "] entries.");
	$lastIntId = null;
	foreach($entries as $entry)
	{
		/* @var $entry entry */
		$categoryIds = explode(',', $entry->getCategoriesIds());
		foreach($categoryIds as $categoryId)
		{
			$categoryEntry = new categoryEntry();
			$categoryEntry->setEntryId($entry->getId());
			$categoryEntry->setCategoryId($categoryId);
			$categoryEntry->setPartnerId($entry->getPartnerId());
			$categoryEntry->save();
		}
		
		$lastIntId = $entry->getId();
		KalturaLog::info("Migrated entry [" . $entry->getId() . "] with int id [$lastIntId].");
	}
	kMemoryManager::clearMemory();

	$nextCriteria = clone $criteria;
	$nextCriteria->add(entryPeer::INT_ID, $lastIntId, Criteria::GREATER_THAN);
	$entries = entryPeer::doSelect($nextCriteria);
}

KalturaLog::info("Done");
