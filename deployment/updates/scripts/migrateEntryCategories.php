<?php
require_once(dirname(__FILE__).'/../../bootstrap.php');

// command: php migrateEntryCategories.php [realRun|dryRun] [partner id] [start entry int id] [max entries]

class migrationCategoryEntry extends categoryEntry
{
	/* (non-PHPdoc)
	 * @see categoryEntry::postInsert()
	 */
	public function postInsert(PropelPDO $con = null)
	{	
		// DO nothing - don't increase category entries count
	}
}

$partnerId = null;
$startEntryIntId = null;
$limit = null;
$page = 200;

$dryRun = false;
if($argc == 1 || strtolower($argv[1]) != 'realrun')
{
	$dryRun = true;
	KalturaLog::alert('Using dry run mode');
}

if($argc > 2)
	$partnerId = $argv[2];

if($argc > 3)
	$startEntryIntId = $argv[3];
	
if($argc > 4)
	$limit = $argv[4];
	
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
$migrated = 0;
while(count($entries) && (!$limit || $migrated < $limit))
{
	KalturaLog::info("Migrating [" . count($entries) . "] entries.");
	$migrated += count($entries);
	$lastIntId = null;
	foreach($entries as $entry)
	{
		/* @var $entry entry */
		
		$categoriesCriteria = new Criteria();
		$categoriesCriteria->add(categoryPeer::ID, $entry->getCategoriesIds(), Criteria::IN);
		$categories = categoryPeer::doSelect($categoriesCriteria);
		
		$categoryIds = array();
		foreach($categories as $category)
		{
			/* @var $category category */
			$categoryIds[] = $category->getId();
		} 
		
		$categoryEntriesCriteria = new Criteria();
		$categoryEntriesCriteria->addSelectColumn(categoryEntryPeer::CATEGORY_ID);
		$categoryEntriesCriteria->add(categoryEntryPeer::ENTRY_ID, $entry->getId());
		$categoryEntriesCriteria->add(categoryEntryPeer::CATEGORY_ID, $categoryIds, Criteria::IN);

		$stmt = categoryEntryPeer::doSelectStmt($categoryEntriesCriteria);
		$categoryEntriesIds = $stmt->fetchAll(PDO::FETCH_COLUMN);
		
		KalturaStatement::setDryRun($dryRun);		
		foreach($categories as $category)
		{
			/* @var $category category */
			$entryId = $entry->getId();
			$categoryId = $category->getId();
			
			if(in_array($categoryId, $categoryEntriesIds))
			{
				KalturaLog::debug("CategoryEntry already exists for entry [$entryId] and category [$categoryId]");
				continue;
			}
			
			$categoryEntry = new migrationCategoryEntry();
			$categoryEntry->setEntryId($entryId);
			$categoryEntry->setCategoryId($categoryId);
			$categoryEntry->setPartnerId($entry->getPartnerId());
			$categoryEntry->setCategoryFullIds($category->getFullIds());
			$categoryEntry->setStatus(CategoryEntryStatus::ACTIVE);
			$categoryEntry->save();
		}
		KalturaStatement::setDryRun(false);
		
		$lastIntId = $entry->getIntId();
		KalturaLog::info("Migrated entry [" . $entry->getId() . "] with int id [$lastIntId].");
	}
	kMemoryManager::clearMemory();

	$nextCriteria = clone $criteria;
	$nextCriteria->add(entryPeer::INT_ID, $lastIntId, Criteria::GREATER_THAN);
	$entries = entryPeer::doSelect($nextCriteria);
}

KalturaLog::info("Done");
