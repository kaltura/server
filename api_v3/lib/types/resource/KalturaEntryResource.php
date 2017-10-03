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
	
	/* (non-PHPdoc)
	 * @see KalturaObject::validateForUsage($sourceObject, $propertiesToSkip)
	 */
	public function validateForUsage($sourceObject, $propertiesToSkip = array())
	{
		parent::validateForUsage($sourceObject, $propertiesToSkip);
		
		$this->validatePropertyNotNull('entryId');
	
    	$srcEntry = entryPeer::retrieveByPK($this->entryId);
		if (!$srcEntry)
			throw new KalturaAPIException(KalturaErrors::ENTRY_ID_NOT_FOUND, $this->entryId);
			
		if($srcEntry->getMediaType() == KalturaMediaType::IMAGE || $srcEntry->getMediaType() == KalturaMediaType::LIVE_STREAM_FLASH)
			return;
		
		$srcFlavorAsset = null;
		if(is_null($this->flavorParamsId))
		{
			$srcFlavorAsset = assetPeer::retrieveOriginalByEntryId($this->entryId);
			if (!$srcFlavorAsset)
				throw new KalturaAPIException(KalturaErrors::ORIGINAL_FLAVOR_ASSET_IS_MISSING);
		}
		else
		{
			$srcFlavorAsset = assetPeer::retrieveByEntryIdAndParams($this->entryId, $this->flavorParamsId);
			if (!$srcFlavorAsset)
				throw new KalturaAPIException(KalturaErrors::FLAVOR_ASSET_ID_NOT_FOUND, $this->assetId);
		}
		
		$key = $srcFlavorAsset->getSyncKey(asset::FILE_SYNC_ASSET_SUB_TYPE_ASSET);
		$c = FileSyncPeer::getCriteriaForFileSyncKey($key);
		$fileSyncs = FileSyncPeer::doSelect($c);

		foreach($fileSyncs as $fileSync)
		{
			$fileSync = kFileSyncUtils::resolve($fileSync);
			if($fileSync->getFileType() != FileSync::FILE_SYNC_FILE_TYPE_LINK)
				return;
		}
		throw new KalturaAPIException(KalturaErrors::FILE_DOESNT_EXIST);
	}
	
	/* (non-PHPdoc)
	 * @see KalturaResource::validateEntry()
	 */
	public function validateEntry(entry $dbEntry)
	{
		parent::validateEntry($dbEntry);
		
		$srcEntry = entryPeer::retrieveByPK($this->entryId);
		if(!$srcEntry)
			throw new KalturaAPIException(KalturaErrors::ENTRY_ID_NOT_FOUND, $this->entryId);
		if($srcEntry->getMediaType() == KalturaMediaType::IMAGE)
			return parent::validateEntry($dbEntry);
	}

	/* (non-PHPdoc)
	 * @see KalturaObject::toObject($object_to_fill, $props_to_skip)
	 */
	public function toObject ( $object_to_fill = null , $props_to_skip = array() )
	{
		$this->validateForUsage($object_to_fill, $props_to_skip);
		
    	$srcEntry = entryPeer::retrieveByPK($this->entryId);
    	if(!$srcEntry)
    		throw new KalturaAPIException(KalturaErrors::ENTRY_ID_NOT_FOUND, $this->entryId);

    	if($srcEntry->getType() == entryType::LIVE_STREAM)
    	{
    		/* @var $srcEntry LiveEntry */
    		
    		if(!in_array($srcEntry->getSource(), array(EntrySourceType::LIVE_STREAM, EntrySourceType::LIVE_STREAM_ONTEXTDATA_CAPTIONS)))
    			throw new KalturaAPIException(KalturaErrors::RESOURCE_TYPE_NOT_SUPPORTED, get_class($this));
    			
    		$mediaServer = $srcEntry->getMediaServer();
    		if($mediaServer && !is_null($mediaServer->getDc()) && $mediaServer->getDc() != kDataCenterMgr::getCurrentDcId())
    		{
				$remoteDCHost = kDataCenterMgr::getRemoteDcExternalUrlByDcId($mediaServer->getDc());
				if($remoteDCHost)
				{
					kFileUtils::dumpApiRequest($remoteDCHost);
				}
				else
				{
					throw new KalturaAPIException(KalturaErrors::UPLOADED_FILE_NOT_FOUND_BY_TOKEN);
				}
    		}
    		
			if($object_to_fill && !($object_to_fill instanceof kLiveEntryResource))
				throw new KalturaAPIException(KalturaErrors::RESOURCE_TYPE_NOT_SUPPORTED, get_class($object_to_fill));
				
			$object_to_fill = new kLiveEntryResource();
    		$object_to_fill->setEntry($srcEntry);
    		    		
    		return $object_to_fill;
    	}
    	
		if(!$object_to_fill)
			$object_to_fill = new kFileSyncResource();
			
    	if($srcEntry->getMediaType() == KalturaMediaType::IMAGE)
    	{
			$object_to_fill->setFileSyncObjectType(FileSyncObjectType::ENTRY);
			$object_to_fill->setObjectSubType(entry::FILE_SYNC_ENTRY_SUB_TYPE_DATA);
			$object_to_fill->setObjectId($srcEntry->getId());
			
			return $object_to_fill;
    	}
    	
    	$srcFlavorAsset = null;
    	if(is_null($this->flavorParamsId))
    	{
			$srcFlavorAsset = assetPeer::retrieveOriginalByEntryId($this->entryId);
	    	if(!$srcFlavorAsset)
	    		throw new KalturaAPIException(KalturaErrors::ORIGINAL_FLAVOR_ASSET_IS_MISSING);
    	}
		else
		{
			$srcFlavorAsset = assetPeer::retrieveByEntryIdAndParams($this->entryId, $this->flavorParamsId);
	    	if(!$srcFlavorAsset)
	    		throw new KalturaAPIException(KalturaErrors::FLAVOR_PARAMS_ID_NOT_FOUND, $this->flavorParamsId);
		}
		
    		
		$object_to_fill->setFileSyncObjectType(FileSyncObjectType::FLAVOR_ASSET);
		$object_to_fill->setObjectSubType(asset::FILE_SYNC_FLAVOR_ASSET_SUB_TYPE_ASSET);
		$object_to_fill->setObjectId($srcFlavorAsset->getId());
		$object_to_fill->setOriginEntryId($this->entryId);

		return $object_to_fill;
	}
	
	/* (non-PHPdoc)
	 * @see KalturaResource::entryHandled()
	 */
	public function entryHandled(entry $dbEntry)
	{
    	$srcEntry = entryPeer::retrieveByPK($this->entryId);
    	if(
    		$srcEntry->getType() == KalturaEntryType::MEDIA_CLIP 
    		&& 
    		$dbEntry->getType() == KalturaEntryType::MEDIA_CLIP 
    		&& 
    		$dbEntry->getMediaType() == KalturaMediaType::IMAGE
    	)
    	{
    		if($dbEntry->getMediaType() == KalturaMediaType::IMAGE)
    		{
    			$dbEntry->setDimensions($srcEntry->getWidth(), $srcEntry->getHeight());
    			$dbEntry->setMediaDate($srcEntry->getMediaDate(null));
    			$dbEntry->save();
    		}
    		else 
    		{
		    	$srcFlavorAsset = null;
		    	if(is_null($this->flavorParamsId))
					$srcFlavorAsset = assetPeer::retrieveOriginalByEntryId($this->entryId);
				else
					$srcFlavorAsset = assetPeer::retrieveByEntryIdAndParams($this->entryId, $this->flavorParamsId);
				
				if($srcFlavorAsset)
				{
	    			$dbEntry->setDimensions($srcFlavorAsset->getWidth(), $srcFlavorAsset->getHeight());
	    			$dbEntry->save();
				}
    		}
    	}
    	
    	return parent::entryHandled($dbEntry);
	}
}