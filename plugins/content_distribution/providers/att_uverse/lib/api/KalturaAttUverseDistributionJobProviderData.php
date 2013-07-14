<?php
/**
 * @package plugins.attUverseDistribution
 * @subpackage api.objects
 */
class KalturaAttUverseDistributionJobProviderData extends KalturaConfigurableDistributionJobProviderData
{
		
	/**
	 * @var KalturaAttUverseDistributionFileArray
	 */
	public $filesForDistribution;
	
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
		
		/* @var $distributionProfileDb AttUverseDistributionProfile */
		
		$distributionProfileDb = DistributionProfilePeer::retrieveByPK($distributionJobData->distributionProfileId);
		$distributedFlavorIds = null;
		$distributedThumbIds = null;
		$this->filesForDistribution = new KalturaAttUverseDistributionFileArray();
		$entryDistributionDb = EntryDistributionPeer::retrieveByPK($distributionJobData->entryDistributionId);
		//Flavor Assets
		$flavorAssets = assetPeer::retrieveByIds(explode(',', $distributionJobData->entryDistribution->flavorAssetIds));
		if(count($flavorAssets)) {
			$assetLocalIds = array();
			foreach ($flavorAssets as $flavorAsset)
			{
				$file = new KalturaAttUverseDistributionFile();
				$file->assetType = KalturaAssetType::FLAVOR;
				/* @var $flavorAsset flavorAsset */
				$syncKey = $flavorAsset->getSyncKey(flavorAsset::FILE_SYNC_ASSET_SUB_TYPE_ASSET);
				if(kFileSyncUtils::fileSync_exists($syncKey)){
					$assetLocalIds[] = $flavorAsset->getId();
					$file->assetId = $flavorAsset->getId();
					$file->localFilePath = kFileSyncUtils::getLocalFilePathForKey($syncKey, false);
					$defaultFilename = pathinfo($file->localFilePath, PATHINFO_BASENAME);
					$file->remoteFilename = $distributionProfileDb->getFlavorAssetFilename($entryDistributionDb,$defaultFilename,$flavorAsset->getId() );
					$this->filesForDistribution[] = $file;
				}
			}
			$distributedFlavorIds = implode(',', $assetLocalIds);			
		}
		
		//Thumbnail		
		$thumbAssets = assetPeer::retrieveByIds(explode(',', $distributionJobData->entryDistribution->thumbAssetIds));
				
		if(count($thumbAssets))
		{	
			$thumbLocalIds = array();
			foreach ($thumbAssets as $thumbAsset)
			{							
				$file = new KalturaAttUverseDistributionFile();
				$file->assetType = KalturaAssetType::THUMBNAIL;	
				$syncKey = $thumbAsset->getSyncKey(thumbAsset::FILE_SYNC_ASSET_SUB_TYPE_ASSET);
				if(kFileSyncUtils::fileSync_exists($syncKey)){
					$thumbLocalIds[] = $thumbAsset->getId();
					$file->assetId = $thumbAsset->getId();
					$file->localFilePath = kFileSyncUtils::getLocalFilePathForKey($syncKey, false);
					$defaultFilename = pathinfo($file->localFilePath, PATHINFO_BASENAME);
					$file->remoteFilename = $distributionProfileDb->getThumbnailAssetFilename($entryDistributionDb, $defaultFilename, $thumbAsset->getId());
					$this->filesForDistribution[] = $file;
				}
			}
			$distributedThumbIds = implode(',', $thumbLocalIds);
		}	
		
		//additional assets
		$additionalAssets = assetPeer::retrieveByIds(explode(',', $distributionJobData->entryDistribution->assetIds));
		if(count($additionalAssets))
		{	
			$captionLocalIds = array();
			foreach ($additionalAssets as $additionalAsset)
			{	
				$file = new KalturaAttUverseDistributionFile();
				$file->assetType = kPluginableEnumsManager::coreToApi(KalturaAssetType::getEnumClass(),$additionalAsset->getType());
				$syncKey = $additionalAsset->getSyncKey(CaptionAsset::FILE_SYNC_ASSET_SUB_TYPE_ASSET);
				$id = $additionalAsset->getId();
				if(kFileSyncUtils::fileSync_exists($syncKey)){
					if (($file->assetType == CaptionPlugin::getApiValue(CaptionAssetType::CAPTION))||
						($file->assetType == AttachmentPlugin::getApiValue(AttachmentAssetType::ATTACHMENT))){									
						$captionLocalIds[] = $additionalAsset->getId();
						$file->assetId = $additionalAsset->getId();
						$file->localFilePath = kFileSyncUtils::getLocalFilePathForKey($syncKey, false);
						$defaultFilename = pathinfo($file->localFilePath, PATHINFO_BASENAME);
						$file->remoteFilename = $distributionProfileDb->getAssetFilename($entryDistributionDb, $defaultFilename, $additionalAsset->getId());
						$this->filesForDistribution[] = $file;
					}
				}
			}
			$distributedCaptionIds = implode(',', $captionLocalIds);
		}	
		
		//putting distributed flavors ids and distributed thumbnail ids in entry distribution custom data		
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
		"remoteAssetFileUrls",
		"remoteThumbnailFileUrls",
	);

	public function getMapBetweenObjects()
	{
		return array_merge(parent::getMapBetweenObjects(), self::$map_between_objects);
	}
	
}
