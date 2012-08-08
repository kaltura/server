<?php
require_once(dirname(__FILE__).'/../../bootstrap.php');

// command: php migrateEntryCategories.php [realRun|dryRun] [partner id] [start entry int id] [max entries]

class MigrationCategoryKuser extends categoryKuser
{
	/* (non-PHPdoc)
	 * @see categoryKuser::preUpdate()
	 */
	public function preUpdate(PropelPDO $con = null)
	{
		if ($this->alreadyInSave)
		{
			return true;
		}	
		
		
		if($this->isModified())
			$this->setUpdatedAt(time());
		
		$this->tempModifiedColumns = $this->modifiedColumns;
		return true;
	}
}

$partnerId = null;
$page = 500;

$dryRun = false;
if($argc == 1 || strtolower($argv[1]) != 'realrun')
{
	$dryRun = true;
	KalturaLog::alert('Using dry run mode');
}

if($argc > 2)
	$partnerId = $argv[2];


	
$criteria = new Criteria();
$criteria->addAscendingOrderByColumn(categoryKuserPeer::ID);

if($partnerId)
	$criteria->add(categoryKuserPeer::PARTNER_ID, $partnerId);
	
$criteria->setLimit($page);

$categoryKusers = categoryKuserPeer::doSelect($criteria);
while(count($categoryKusers))
{
	KalturaLog::info("Migrating [" . count($categoryKusers) . "] categories.");
	foreach($categoryKusers as $categoryKuser)
	{
		$categoryKuser = cast($categoryKuser, 'MigrationCategoryKuser');
		
		/* @var $categoryKuser categoryKuser */
		$categoryKuser->reSetScreenName();
				
		KalturaStatement::setDryRun($dryRun);
		$categoryKuser->save();
		KalturaStatement::setDryRun(false);
		
		$startCategoryKuserId = $categoryKuser->getId();
		KalturaLog::info("Migrated categoryKuser [$startCategoryKuserId].");
	}
	
	kEventsManager::flushEvents();
	kMemoryManager::clearMemory();
	
	$nextCriteria = clone $criteria;
	$nextCriteria->add(categoryKuserPeer::ID, $startCategoryKuserId, Criteria::GREATER_THAN);
	$categoryKusers = categoryKuserPeer::doSelect($nextCriteria);
	usleep(100);
}

KalturaLog::info("Done");

function cast($object, $toClass)
{
	if(class_exists($toClass))
	{
		$objectIn = serialize($object);
		$obj_name_len = strlen(get_class($object));
		$objectOut = 'O:' . strlen($toClass) . ':"' . $toClass . '":' . substr($objectIn, $obj_name_len + strlen($obj_name_len) + 6);

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