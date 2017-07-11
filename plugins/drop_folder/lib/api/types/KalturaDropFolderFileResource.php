<?php
/**
 * Used to ingest media that dropped through drop folder
 * 
 * @package plugins.dropFolder
 * @subpackage api.objects
 */
class KalturaDropFolderFileResource extends KalturaGenericDataCenterContentResource
{
	/**
	 * Id of the drop folder file object
	 * @var int
	 */
	public $dropFolderFileId;
	
	/*
	 * mapping between the field on this object (on the left) and the setter/getter on the entry object (on the right)  
	 */
	private static $map_between_objects = array(
		'dropFolderFileId',
	 );
		 
	public function getMapBetweenObjects()
	{
		return array_merge(parent::getMapBetweenObjects(), self::$map_between_objects);
	}	
	

	public function getDc()
	{
		$dropFolderFile = DropFolderFilePeer::retrieveByPK($this->dropFolderFileId);
		if(!$dropFolderFile)
			throw new KalturaAPIException(KalturaErrors::INVALID_OBJECT_ID, $this->dropFolderFileId);
		
		$dropFolder = DropFolderPeer::retrieveByPK($dropFolderFile->getDropFolderId());
		if(!$dropFolder)
			throw new KalturaAPIException(KalturaErrors::INVALID_OBJECT_ID, $dropFolderFile->getDropFolderId());
			
		return $dropFolder->getDc();
	}
	
	public function validateEntry(entry $dbEntry)
	{
		parent::validateEntry($dbEntry);
    	$this->validatePropertyNotNull('dropFolderFileId');
    	
    	$dropFolderFile = DropFolderFilePeer::retrieveByPK($this->dropFolderFileId);
    	if(!$dropFolderFile)
    		throw new KalturaAPIException(KalturaErrors::INVALID_OBJECT_ID, $this->dropFolderFileId);
    		
    	$dropFolder = DropFolderPeer::retrieveByPK($dropFolderFile->getDropFolderId());
    	if(!$dropFolder)
    		throw new KalturaAPIException(KalturaErrors::INVALID_OBJECT_ID, $dropFolderFile->getDropFolderId());
    	
    	if ($dropFolder->getType() == DropFolderType::LOCAL)
		{
        	$filePath = rtrim($dropFolder->getPath(), '\\\/') . '/' . ltrim($dropFolderFile->getFileName(), '\\\/');
        	if(!file_exists($filePath))
        	{
        		$dropFolderFile->setStatus(DropFolderFileStatus::ERROR_HANDLING);
        		$dropFolderFile->setErrorCode(DropFolderFileErrorCode::ERROR_READING_FILE);
        		$dropFolderFile->setErrorDescription(DropFolderPlugin::ERROR_READING_FILE_MESSAGE.$filePath);
        		$dropFolderFile->save();
        		
        		throw new KalturaAPIException(KalturaErrors::FILE_DOESNT_EXIST, $filePath);
        	}
		}
	}
	
	public function toObject ( $object_to_fill = null , $props_to_skip = array() )
	{
		if(!$object_to_fill)
			$object_to_fill = new kDropFolderFileResource();
			
		$object_to_fill = parent::toObject($object_to_fill, $props_to_skip);

		$dropFolderFile = DropFolderFilePeer::retrieveByPK($this->dropFolderFileId);
		if(!$dropFolderFile) {
			throw new KalturaAPIException(KalturaErrors::INVALID_OBJECT_ID, $this->dropFolderFileId);
		}
			
		$dropFolder = DropFolderPeer::retrieveByPK($dropFolderFile->getDropFolderId());
		if(!$dropFolder) {
			throw new KalturaAPIException(KalturaErrors::INVALID_OBJECT_ID, $dropFolderFile->getDropFolderId());
		}
		
		$object_to_fill->setDropFolderFileId($dropFolderFile->getId());

		$fullPath = $dropFolder->getPath().'/'.$dropFolderFile->getFileName();
		$object_to_fill->setLocalFilePath($fullPath);
		
		if ($dropFolder->getType() == DropFolderType::LOCAL)
		{
			// if the drop folder is set to automatic with immidate deletion of files (days = 0) move the files instead of copying them.
			// this will result in fast handling of drop folder files
			$deleteOriginalFile = ($dropFolder->getFileDeletePolicy() == DropFolderFileDeletePolicy::AUTO_DELETE && $dropFolder->getAutoFileDeleteDays() == 0);
			
		    $object_to_fill->setKeepOriginalFile(!$deleteOriginalFile);
		    $object_to_fill->setIsReady(true);
		}
		else /* ($dropFolder instanceof RemoteDropFolder) */
		{
		    $object_to_fill->setKeepOriginalFile(false);
			$object_to_fill->setIsReady(false);
		}
		
		return $object_to_fill;
	}
	
	public function entryHandled(entry $dbEntry)
	{
		parent::entryHandled($dbEntry);
		
		$dropFolderFile = DropFolderFilePeer::retrieveByPK($this->dropFolderFileId);
	
		if (is_null($dropFolderFile))
			throw new KalturaAPIException(KalturaErrors::INVALID_OBJECT_ID, $this->dropFolderFileId);
		
		if ($dropFolderFile->getStatus() != DropFolderFileStatus::DOWNLOADING)
		{
			$dropFolder = DropFolderPeer::retrieveByPK($dropFolderFile->getDropFolderId());
			if(!$dropFolder) {
				throw new KalturaAPIException(KalturaErrors::INVALID_OBJECT_ID, $dropFolderFile->getDropFolderId());
			}
			if($dropFolder->getFileDeletePolicy() == DropFolderFileDeletePolicy::AUTO_DELETE && $dropFolder->getAutoFileDeleteDays() == 0)
    			$dropFolderFile->setStatus(DropFolderFileStatus::PURGED);
    		else
    			$dropFolderFile->setStatus(DropFolderFileStatus::HANDLED);
		}
		$dropFolderFile->setEntryId($dbEntry->getId());
		$dropFolderFile->save();		
	}
}