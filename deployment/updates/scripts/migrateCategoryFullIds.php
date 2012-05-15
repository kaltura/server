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

if($argc > 1)
	$partnerId = $argv[1];

if($argc > 2)
	$startCategoryId = $argv[2];
	
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
		
		KalturaStatement::setDryRun($dryRun);
		$category->save();
		KalturaStatement::setDryRun(false);
		
		$startCategoryId = $category->getId();
		KalturaLog::info("Migrated category [$startCategoryId].");
	}
	kMemoryManager::clearMemory();

	$nextCriteria = clone $criteria;
	$nextCriteria->add(categoryPeer::ID, $startCategoryId, Criteria::GREATER_THAN);
	$entries = categoryPeer::doSelect($nextCriteria);
	usleep(100);
}

KalturaLog::info("Done");
