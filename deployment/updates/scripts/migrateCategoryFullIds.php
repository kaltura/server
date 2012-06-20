<?php
require_once(dirname(__FILE__).'/../../bootstrap.php');


/**
 * @package Deployment
 * @subpackage updates
 */
class MigrationCategory extends category
{
	/* (non-PHPdoc)
	 * @see Category::setUpdatedAt()
	 * 
	 * Do nothing
	 */
	public function setUpdatedAt($v)
	{
	}
	
	/* (non-PHPdoc)
	 * @see Category::addCopyCategoryKuserJob()
	 * 
	 * Do nothing
	 */
	protected function addCopyCategoryKuserJob($categoryId)
	{	
	}
	
/* (non-PHPdoc)
	 * @see Category::addDeleteCategoryEntryJob()
	 * 
	 * Do nothing
	 */
	protected function addDeleteCategoryEntryJob($categoryId)
	{	
	}
	
	/* (non-PHPdoc)
	 * @see Category::addDeleteCategoryKuserJob()
	 * 
	 * Do nothing
	 */
	protected function addDeleteCategoryKuserJob($categoryId)
	{	
	}
	
	/* (non-PHPdoc)
	 * @see Category::addIndexCategoryEntryJob()
	 * 
	 * Do nothing
	 */
	protected function addIndexCategoryEntryJob($categoryId = null, $shouldUpdate = true)
	{	
	}
	
	/* (non-PHPdoc)
	 * @see Category::addIndexCategoryJob()
	 * 
	 * Do nothing
	 */
	protected function addIndexCategoryJob($fullIdsStartsWithCategoryId, $categoriesIdsIn, $inheritedParentId = null, $lock = false)
	{	
	}
	
	/* (non-PHPdoc)
	 * @see Category::addIndexCategoryKuserJob()
	 * 
	 * Do nothing
	 */
	protected function addIndexCategoryKuserJob($categoryId = null, $shouldUpdate = true)
	{	
	}
		
	/* (non-PHPdoc)
	 * @see Category::addIndexEntryJob()
	 * 
	 * Do nothing
	 */
	protected function addIndexEntryJob($categoryId, $shouldUpdate = false)
	{	
	}
	
	/* (non-PHPdoc)
	 * @see Category::addMoveEntriesToCategoryJob()
	 * 
	 * Do nothing
	 */
	protected function addMoveEntriesToCategoryJob($destCategoryId)
	{	
	}
	
	/* (non-PHPdoc)
	 * @see Category::addRecalcCategoriesCount()
	 * 
	 * Do nothing
	 */
	protected function addRecalcCategoriesCount($categoryId)
	{	
	}
}

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
		$category = cast($category, 'MigrationCategory');
		KalturaLog::debug('MigrationCategory ' . print_r($category,true));
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

function cast($object, $toClass)
{
	if(class_exists($toClass))
	{
		KalturaLog::debug('Class exists ' . print_r($toClass,true));
		$objectIn = serialize($object);
		$objectOut = 'O:' . strlen($toClass) . ':"' . $toClass . '":' . substr($objectIn, $objectIn[2] + 7);
		$ret = unserialize($objectOut);
		if($ret instanceof $toClass)
			return $ret;
	}
	else
	{
		KalturaLog::debug('Class doesnt exists' . print_r($toClass,true));
	}
	
	return false;
}
