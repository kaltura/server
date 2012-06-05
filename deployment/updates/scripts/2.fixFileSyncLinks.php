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

// find all links that already changed to files
$criteriaTemplate = new Criteria();
$criteriaTemplate->add(FileSyncPeer::LINKED_ID, 0, Criteria::GREATER_THAN);
$criteriaTemplate->add(FileSyncPeer::FILE_TYPE, FileSync::FILE_SYNC_FILE_TYPE_FILE);
$criteriaTemplate->add(FileSyncPeer::STATUS, array(FileSync::FILE_SYNC_STATUS_PURGED, FileSync::FILE_SYNC_STATUS_DELETED), Criteria::NOT_IN);
$criteriaTemplate->setLimit($countLimitEachLoop);

$fileSyncs = FileSyncPeer::doSelect($criteriaTemplate, $con);
while(count($fileSyncs))
{
	$handledIds = array();
	foreach($fileSyncs as $fileSync)
	{
		/* @var $fileSync FileSync */
		
		if(in_array($fileSync->getId(), $handledIds))
			continue;
			
		FileSyncPeer::setUseCriteriaFilter(false);
		$srcFileSync = FileSyncPeer::retrieveByPK($fileSync->getLinkedId());
		FileSyncPeer::setUseCriteriaFilter(true);
		
		// find all the links of the same source
		$linksCriteria = new Criteria();
		$linksCriteria->add(FileSyncPeer::DC, $fileSync->getDc());
		$linksCriteria->add(FileSyncPeer::FILE_TYPE, FileSync::FILE_SYNC_FILE_TYPE_FILE);
		$linksCriteria->add(FileSyncPeer::LINKED_ID, $fileSync->getLinkedId());
		$linksCriteria->add(FileSyncPeer::STATUS, array(FileSync::FILE_SYNC_STATUS_PURGED, FileSync::FILE_SYNC_STATUS_DELETED), Criteria::NOT_IN);
		$linksCriteria->addAscendingOrderByColumn(FileSyncPeer::PARTNER_ID);
			
		$links = FileSyncPeer::doSelect($linksCriteria, $con);
		KalturaStatement::setDryRun($dryRun);
		
		// choose the first link and convert it to file
		$firstLink = array_shift($links);
		/* @var $firstLink FileSync */
		if($firstLink)
		{
			$firstLink = cast($firstLink, 'MigrationFileSync');
			
			$firstLink->setLinkedId(0); // keep it zero instead of null, that's the only way to know it used to be a link.
			$firstLink->setFileSize($srcFileSync->getFileSize());
			$firstLink->setLinkCount(count($links));
			$firstLink->save();
			
			$handledIds[] = $firstLink->getId();
		}
		
		// change all the rest of the links to point on the new file sync
		foreach($links as $link)
		{
			$link = cast($link, 'MigrationFileSync');
			
			/* @var $link FileSync */
			$link->setFileType(FileSync::FILE_SYNC_FILE_TYPE_LINK);
			$link->setLinkedId($firstLink->getId());
			$link->save();
			
			$handledIds[] = $link->getId();
		}
		KalturaStatement::setDryRun(false);
		
		// make sure that current file handled, otherwise we will have enless loops
		if(!in_array($fileSync->getId(), $handledIds))
			throw new Exception("File Sync id [" . $fileSync->getId() . "] not handled");
	}
	
	$fileSyncs = FileSyncPeer::doSelect($criteriaTemplate, $con);
	usleep(100);
}
