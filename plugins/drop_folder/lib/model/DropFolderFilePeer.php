<?php


/**
 * Skeleton subclass for performing query and update operations on the 'drop_folder_file' table.
 *
 * 
 *
 * You should add additional methods to this class to meet the
 * application requirements.  This class will only be generated as
 * long as it does not already exist in the output directory.
 *
 * @package plugins.dropFolder
 * @subpackage model
 */
class DropFolderFilePeer extends BaseDropFolderFilePeer
{

	public static function setDefaultCriteriaFilter ()
	{
		parent::setDefaultCriteriaFilter();
		if ( self::$s_criteria_filter == null )
		{
			self::$s_criteria_filter = new criteriaFilter ();
		}
		
		$c = new myCriteria(); 
		$c->addAnd ( self::STATUS, DropFolderFileStatus::PURGED , Criteria::NOT_EQUAL);
		self::$s_criteria_filter->setFilter ( $c );
	}
	
	
	public static function retrieveByDropFolderIdAndFileName($dropFolderId, $fileName)
	{
		$c = new Criteria();
		$c->addAnd(DropFolderFilePeer::DROP_FOLDER_ID, $dropFolderId, Criteria::EQUAL);
		$c->addAnd(DropFolderFilePeer::FILE_NAME, $fileName, Criteria::EQUAL);
		$dropFolderFile = DropFolderFilePeer::doSelectOne($c);
		return $dropFolderFile;		
	}
	
	public static function retrieveByDropFolderIdAndStatus($dropFolderId, $status)
	{
		$c = new Criteria();
		$c->addAnd(DropFolderFilePeer::DROP_FOLDER_ID, $dropFolderId, Criteria::EQUAL);
		$c->addAnd(DropFolderFilePeer::STATUS, $status, Criteria::EQUAL);
		$dropFolderFiles = DropFolderFilePeer::doSelect($c);
		return $dropFolderFiles;		
	}
	
	public static function retrieveByDropFolderIdStatusesAndSlug($dropFolderId, $statuses, $parsedSlug)
	{
		$c = new Criteria();
		$c->addAnd(DropFolderFilePeer::DROP_FOLDER_ID, $dropFolderId, Criteria::EQUAL);
		$c->addAnd(DropFolderFilePeer::STATUS, $statuses, Criteria::IN);
		$c->addAnd(DropFolderFilePeer::PARSED_SLUG, $parsedSlug, Criteria::EQUAL);
		$dropFolderFiles = DropFolderFilePeer::doSelect($c);
		return $dropFolderFiles;		
	}
	
	public static function retrieveByLeadIdAndStatuses($leadId, $statuses)
	{
		$c = new Criteria();
		$c->addAnd(DropFolderFilePeer::LEAD_DROP_FOLDER_FILE_ID, $leadId, Criteria::EQUAL);
		$c->addAnd(DropFolderFilePeer::STATUS, $statuses, Criteria::IN);
		$dropFolderFiles = DropFolderFilePeer::doSelect($c);
		return $dropFolderFiles;		
	}
	
	
	public static function getCacheInvalidationKeys()
	{
		return array(array("dropFolderFile:id=%s", self::ID), array("dropFolderFile:fileName=%s", self::FILE_NAME), array("dropFolderFile:dropFolderId=%s", self::DROP_FOLDER_ID));		
	}
	
	public static function getAtomicColumns()
	{
		return array(DropFolderFilePeer::STATUS);
	}
} // DropFolderFilePeer
