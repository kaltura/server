<?php
/**
 * @package plugins.attUverseDistribution
 * @subpackage api.objects
 */
class KalturaAttUverseDistributionJobProviderData extends KalturaConfigurableDistributionJobProviderData
{
		
	/**
	 * @var string
	 */
	public $assetLocalPaths;
	
	/**
	 * @var string
	 */
	public $thumbLocalPaths;
	
	/**
	 * @var string
	 */
	public $captionLocalPaths;
	
	/**
	 * The remote URL of the video asset that was distributed
	 * 
	 * @var string
	 */
	public $remoteAssetFileUrls;
	
	/**
	 * The remote URL of the thumbnail asset that was distributed
	 * 
	 * @var string
	 */
	public $remoteThumbnailFileUrls;
	
	/**
	 * The remote URL of the caption asset that was distributed
	 * 
	 * @var string
	 */
	public $remoteCaptionFileUrls;
	
	
	public function __construct(KalturaDistributionJobData $distributionJobData = null)
	{			   
		parent::__construct($distributionJobData);
	    
		if(!$distributionJobData)
			return;
			
		if(!($distributionJobData->distributionProfile instanceof KalturaAttUverseDistributionProfile))
			return;
			
		$distributedFlavorIds = null;
		$distributedThumbIds = null;
			
		//Flavor Assets
		$flavorAssets = assetPeer::retrieveByIds(explode(',', $distributionJobData->entryDistribution->flavorAssetIds));
		if(count($flavorAssets)) {
			$videoAssetFilePathArray = array();
			foreach ($flavorAssets as $flavorAsset)
			{
				if($flavorAsset) 
				{
					/* @var $flavorAsset flavorAsset */
					$syncKey = $flavorAsset->getSyncKey(flavorAsset::FILE_SYNC_ASSET_SUB_TYPE_ASSET);
					if(kFileSyncUtils::fileSync_exists($syncKey)){
						$id = $flavorAsset->getId();
						$videoAssetFilePathArray[$id] = kFileSyncUtils::getLocalFilePathForKey($syncKey, false);
					}
				}
			}
			$assetLocalIds = array_keys($videoAssetFilePathArray);
			$distributedFlavorIds = implode(',', $assetLocalIds);			
			$this->assetLocalPaths = serialize($videoAssetFilePathArray);	
		}
		
		//Thumbnail		
		$thumbAssets = assetPeer::retrieveByIds(explode(',', $distributionJobData->entryDistribution->thumbAssetIds));
				
		if(count($thumbAssets))
		{	
			$thumbAssetFilePathArray = array();
			foreach ($thumbAssets as $thumbAsset)
			{														
				$syncKey = $thumbAsset->getSyncKey(thumbAsset::FILE_SYNC_ASSET_SUB_TYPE_ASSET);
				if(kFileSyncUtils::fileSync_exists($syncKey)){
					$id = $thumbAsset->getId();
					$thumbAssetFilePathArray[$id] = kFileSyncUtils::getLocalFilePathForKey($syncKey, false);				    
				}
			}
			$thumbLocalIds = array_keys($thumbAssetFilePathArray);
			$distributedThumbIds = implode(',', $thumbLocalIds);
			$this->thumbLocalPaths = serialize($thumbAssetFilePathArray);
		}	
		
		//additional assets
		$additionalAssets = assetPeer::retrieveByIds(explode(',', $distributionJobData->entryDistribution->assetIds));
		if(count($additionalAssets))
		{	
			$captionAssetFilePathArray = array();
			foreach ($additionalAssets as $additionalAsset)
			{	
				$assetType = $additionalAsset->getType();
				$syncKey = $additionalAsset->getSyncKey(CaptionAsset::FILE_SYNC_ASSET_SUB_TYPE_ASSET);
				$id = $additionalAsset->getId();
				if(kFileSyncUtils::fileSync_exists($syncKey)){
					if (($assetType == CaptionPlugin::getAssetTypeCoreValue(CaptionAssetType::CAPTION))||
						($assetType == AttachmentPlugin::getAssetTypeCoreValue(AttachmentAssetType::ATTACHMENT))){									
						$captionAssetFilePathArray[$id] = kFileSyncUtils::getLocalFilePathForKey($syncKey, false);				    
					}
				}
			}
			$captionLocalIds = array_keys($captionAssetFilePathArray);
			$distributedCaptionIds = implode(',', $captionLocalIds);
			$this->captionLocalPaths = serialize($captionAssetFilePathArray);
		}	
		
		//putting distributed flavors ids and distributed thumbnail ids in entry distribution custom data		
		$entryDistributionDb = EntryDistributionPeer::retrieveByPK($distributionJobData->entryDistributionId);
		if ($entryDistributionDb)
		{
			$entryDistributionDb->putInCustomData(AttUverseEntryDistributionCustomDataField::DISTRIBUTED_FLAVOR_IDS, $distributedFlavorIds);
			$entryDistributionDb->putInCustomData(AttUverseEntryDistributionCustomDataField::DISTRIBUTED_THUMBNAIL_IDS, $distributedThumbIds);
			$entryDistributionDb->putInCustomData(AttUverseEntryDistributionCustomDataField::DISTRIBUTED_CAPTION_IDS, $distributedCaptionIds);
			$entryDistributionDb->save();
		}
		else
			KalturaLog::err('Entry distribution ['.$distributionJobData->entryDistributionId.'] not found');
	}
		
	private static $map_between_objects = array
	(
		"assetLocalPaths",
		"thumbLocalPaths",
		"remoteAssetFileUrls",
		"remoteThumbnailFileUrls",
	);

	public function getMapBetweenObjects()
	{
		return array_merge(parent::getMapBetweenObjects(), self::$map_between_objects);
	}
	
}
