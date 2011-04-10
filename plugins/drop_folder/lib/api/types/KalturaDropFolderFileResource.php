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
			$object_to_fill = new kFileSyncResource();
					
//		TODO
//		$object_to_fill->setFileSyncObjectType(DropFolderPlugin::getDropFolderFileSyncObjectTypeCoreValue(DropFolderFileSyncObjectType::DROP_FOLDER_FILE));
//		$object_to_fill->setObjectSubType(DropFolderFile::FILE_SYNC_DROP_FOLDER_FILE_SUB_TYPE_FILE);
		$object_to_fill->setObjectId($this->dropFolderFileId);
		
		return $object_to_fill;
	}
}