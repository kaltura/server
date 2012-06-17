<?php
require_once(dirname(__FILE__).'/../../bootstrap.php');

$partnerId = null;
$startCategoryId = null;
$page = 500;

$dryRun = false;
if($argc == 1 || strtolower($argv[1]) != 'realrun')
{
	$dryRun = true;
	KalturaLog::alert('Using dry run mode');
}

if($argc > 2)
	$partnerId = $argv[2];

if($argc > 3)
	$startCategoryId = $argv[3];
	
$criteria = new Criteria();
$criteria->addAscendingOrderByColumn(categoryPeer::DEPTH);
$criteria->addAscendingOrderByColumn(categoryPeer::ID);

if($partnerId)
	$criteria->add(categoryPeer::PARTNER_ID, $partnerId);
	
if($startCategoryId)
	$criteria->add(categoryPeer::ID, $startCategoryId, Criteria::GREATER_THAN);
	
$criteria->setLimit($page);

$categories = categoryPeer::doSelect($criteria);
while(count($categories))
{
	KalturaLog::info("Migrating [" . count($categories) . "] categories.");
	foreach($categories as $category)
	{
		/* @var $category category */
		$category->reSetFullIds();
		$category->reSetDirectEntriesCount();
		$category->reSetDirectSubCategoriesCount();
				
		KalturaStatement::setDryRun($dryRun);
		$category->save();
		KalturaStatement::setDryRun(false);
		
		$startCategoryId = $category->getId();
		KalturaLog::info("Migrated category [$startCategoryId].");
	}
	
	kEventsManager::flushEvents();
	kMemoryManager::clearMemory();
	
	$nextCriteria = clone $criteria;
	$nextCriteria->add(categoryPeer::ID, $startCategoryId, Criteria::GREATER_THAN);
	$categories = categoryPeer::doSelect($nextCriteria);
	usleep(100);
}

KalturaLog::info("Done");
