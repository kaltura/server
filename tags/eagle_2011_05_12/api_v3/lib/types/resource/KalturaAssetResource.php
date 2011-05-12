<?php
/**
 * Used to ingest media that is already ingested to Kaltura system as a different flavor asset in the past, the new created flavor asset will be ready immediately using a file sync of link type that will point to the existing file sync of the existing flavor asset.
 * 
 * @package api
 * @subpackage objects
 */
class KalturaAssetResource extends KalturaContentResource 
{
	/**
	 * ID of the source asset 
	 * @var string
	 */
	public $assetId;

	public function validateEntry(entry $dbEntry)
	{
		parent::validateEntry($dbEntry);
    	$this->validatePropertyNotNull('assetId');
	}
	
	public function toObject ( $object_to_fill = null , $props_to_skip = array() )
	{
		if(!$object_to_fill)
			$object_to_fill = new kFileSyncResource();
			
		$srcFlavorAsset = flavorAssetPeer::retrieveById($this->assetId);
		if (!$srcFlavorAsset)
			throw new KalturaAPIException(KalturaErrors::FLAVOR_ASSET_ID_NOT_FOUND, $resource->assetId);
			
		$object_to_fill->setFileSyncObjectType(FileSync::FILE_SYNC_OBJECT_TYPE_FLAVOR_ASSET);
		$object_to_fill->setObjectSubType(asset::FILE_SYNC_FLAVOR_ASSET_SUB_TYPE_ASSET);
		$object_to_fill->setObjectId($srcFlavorAsset->getId());
		
		return $object_to_fill;
	}
}