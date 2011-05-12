<?php
/**
 * Used to ingest media that is already ingested to Kaltura system as a different entry in the past, the new created flavor asset will be ready immediately using a file sync of link type that will point to the existing file sync of the existing entry.
 * 
 * @package api
 * @subpackage objects
 */
class KalturaEntryResource extends KalturaContentResource 
{
	/**
	 * ID of the source entry 
	 * @var string
	 */
	public $entryId;
	
	/**
	 * ID of the source flavor params, set to null to use the source flavor
	 * @var int
	 */
	public $flavorParamsId;

	public function validateEntry(entry $dbEntry)
	{
		parent::validateEntry($dbEntry);
    	$this->validatePropertyNotNull('entryId');
	
    	$srcEntry = entryPeer::retrieveByPK($this->entryId);
		if (!$srcEntry || $srcEntry->getType() != entryType::MEDIA_CLIP || $srcEntry->getMediaType() != $dbEntry->getMediaType())
			throw new KalturaAPIException(KalturaErrors::ENTRY_ID_NOT_FOUND, $this->entryId);
	}
	
	public function toObject ( $object_to_fill = null , $props_to_skip = array() )
	{
		if(!$object_to_fill)
			$object_to_fill = new kFileSyncResource();
			
    	$srcEntry = entryPeer::retrieveByPK($this->entryId);
    	
    	if($srcEntry->getMediaType() == KalturaMediaType::IMAGE)
    	{
			$object_to_fill->setFileSyncObjectType(FileSync::FILE_SYNC_OBJECT_TYPE_ENTRY);
			$object_to_fill->setObjectSubType(entry::FILE_SYNC_ENTRY_SUB_TYPE_DATA);
			$object_to_fill->setObjectId($srcEntry->getId());
			
			return $object_to_fill;
    	}
    	
    	$srcFlavorAsset = null;
    	assetPeer::resetInstanceCriteriaFilter();
    	if(is_null($this->flavorParamsId))
			$srcFlavorAsset = assetPeer::retrieveOriginalByEntryId($this->entryId);
		else
			$srcFlavorAsset = assetPeer::retrieveByEntryIdAndParams($this->entryId, $this->flavorParamsId);

		if (!$srcFlavorAsset)
			throw new KalturaAPIException(KalturaErrors::FLAVOR_ASSET_ID_NOT_FOUND, $this->assetId);
			
		$object_to_fill->setFileSyncObjectType(FileSync::FILE_SYNC_OBJECT_TYPE_FLAVOR_ASSET);
		$object_to_fill->setObjectSubType(asset::FILE_SYNC_FLAVOR_ASSET_SUB_TYPE_ASSET);
		$object_to_fill->setObjectId($srcFlavorAsset->getId());
		
		return $object_to_fill;
	}
}