<?php
/**
 * Used to ingest media that dropped through drop folder
 * 
 * @package plugins.dropFolder
 * @subpackage api.objects
 */
class KalturaDropFolderFileResource extends KalturaContentResource 
{
	/**
	 * Token that returned from media server such as FMS or red5. 
	 * @var string
	 */
	public $dropFolderFileId;

	public function validateEntry(entry $dbEntry)
	{
		parent::validateEntry($dbEntry);
    	$this->validatePropertyNotNull('dropFolderFileId');
	}
	
	public function toObject ( $object_to_fill = null , $props_to_skip = array() )
	{
		if(!$object_to_fill)
			$object_to_fill = new kLocalFileResource();

		$dropFolderFile = DropFolderFilePeer::retrieveByPK($this->dropFolderFileId);
		if(!$dropFolderFile)
			throw new KalturaAPIException(KalturaErrors::INVALID_OBJECT_ID, $this->dropFolderFileId);
		
		$dropFolder = DropFolderPeer::retrieveByPK($dropFolderFile->getDropFolderId());
		if(!$dropFolder)
			throw new KalturaAPIException(KalturaErrors::INVALID_OBJECT_ID, $dropFolderFile->getDropFolderId());
			
		$localFilePath = $dropFolder->getPath() . '/' . $dropFolderFile->getFileName();
		
		$object_to_fill->setLocalFilePath($localFilePath);
		return $object_to_fill;
	}
}