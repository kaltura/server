<?php
/**
 * Find all file syncs that used to be links and still pointing to their source
 * Select the first link and make is source to all other links
 * Change all the rest of the links back to be links
 *
 * @package Deployment
 * @subpackage updates
 */ 


$dryRun = true;
if(in_array('realrun', $argv))
	$dryRun = false;
	
$stopFile = dirname(__FILE__).'/stop_file_sync'; // creating this file will stop the script
$countLimitEachLoop = 200;
$lastId = null;
if (isset($argv[1]) && is_numeric($argv[1]))
    $lastId = $argv[1];
        
//------------------------------------------------------

require_once(dirname(__FILE__).'/../../bootstrap.php');

function cast($object, $toClass)
{
	if(class_exists($toClass))
	{
		$objectIn = serialize($object);
		$objectOut = 'O:' . strlen($toClass) . ':"' . $toClass . '":' . substr($objectIn, $objectIn[2] + 7);
		$ret = unserialize($objectOut);
		if($ret instanceof $toClass)
			return $ret;
	}
	
	return false;
}

/**
 * @package Deployment
 * @subpackage updates
 */
class MigrationFileSync extends FileSync
{
	/* (non-PHPdoc)
	 * @see BaseFileSync::setUpdatedAt()
	 * 
	 * Do nothing
	 */
	public function setUpdatedAt($v)
	{
	}
}

$con = myDbHelper::getConnection(myDbHelper::DB_HELPER_CONN_PROPEL2);
	
$criteriaTemplate = new Criteria();

if (isset($argv[2]) && is_numeric($argv[2]))
    $criteriaTemplate->add(FileSyncPeer::PARTNER_ID, $argv[2]);

$criteriaTemplate->add(FileSyncPeer::LINK_COUNT, 0, Criteria::GREATER_THAN);
$criteriaTemplate->add(FileSyncPeer::FILE_TYPE, FileSync::FILE_SYNC_FILE_TYPE_FILE);
$criteriaTemplate->add(FileSyncPeer::STATUS, array(FileSync::FILE_SYNC_STATUS_ERROR, FileSync::FILE_SYNC_STATUS_PENDING), Criteria::IN);
$criteriaTemplate->addAscendingOrderByColumn(FileSyncPeer::ID);
$criteriaTemplate->setLimit($countLimitEachLoop);

$criteria = clone $criteriaTemplate;
if($lastId)
	$criteria->add(FileSyncPeer::ID, $lastId, Criteria::GREATER_THAN);
	
$fileSyncs = FileSyncPeer::doSelect($criteria, $con);
while(count($fileSyncs))
{
	foreach($fileSyncs as $fileSync)
	{
		/* @var $fileSync FileSync */
		$lastId = $fileSync->getId();
			
		$linksCriteria = new Criteria();
		$linksCriteria->add(FileSyncPeer::DC, $fileSync->getDc());
		$linksCriteria->add(FileSyncPeer::FILE_TYPE, array(FileSync::FILE_SYNC_FILE_TYPE_FILE, FileSync::FILE_SYNC_FILE_TYPE_LINK), Criteria::IN);
		$linksCriteria->add(FileSyncPeer::LINKED_ID, $fileSync->getId());
		$linksCriteria->add(FileSyncPeer::STATUS, $fileSync->getStatus(), Criteria::NOT_IN);
			
		$links = FileSyncPeer::doSelect($linksCriteria, $con);
		KalturaStatement::setDryRun($dryRun);
		
		// change the status to current source file sync status 
		foreach($links as $link)
		{
			$link = cast($link, 'MigrationFileSync');
			
			/* @var $link FileSync */
			$link->setStatus($fileSync->getStatus());
			$link->save();
		}
		KalturaStatement::setDryRun(false);
	}
	
	$criteria = clone $criteriaTemplate;
	$criteria->add(FileSyncPeer::ID, $lastId, Criteria::GREATER_THAN);
	$fileSyncs = FileSyncPeer::doSelect($criteria, $con);
	usleep(100);
}
