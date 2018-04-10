<?php
/**
 * @package plugins.facebookDistribution
 * @subpackage api.objects
 */
class KalturaFacebookDistributionJobProviderData extends KalturaConfigurableDistributionJobProviderData
{
	/**
	 * @var string
	 */
	public $videoAssetFilePath;
	
	/**
	 * @var string
	 */
	public $thumbAssetId;

	/**
	 * @var KalturaFacebookCaptionDistributionInfoArray
	 */
	public $captionsInfo;

	public function __construct(KalturaDistributionJobData $distributionJobData = null)
	{
		parent::__construct($distributionJobData);
	    
		if( (!$distributionJobData) ||
			!($distributionJobData->distributionProfile instanceof KalturaFacebookDistributionProfile) ){
			KalturaLog::info("Distribution data given did not exist or was not facebook related, given: ".print_r($distributionJobData, true));
			return;
		}

		$this->videoAssetFilePath = $this->getValidVideoPath($distributionJobData);

		if(!$this->videoAssetFilePath){
			KalturaLog::err("Could not find a valid video asset");
			return;
		}


		$thumbAssetIds = explode(',', $distributionJobData->entryDistribution->thumbAssetIds);
		if(count($thumbAssetIds))
			$this->thumbAssetId = reset($thumbAssetIds);

		$this->addCaptionsData($distributionJobData);
	}
	
	private static $map_between_objects = array
	(
		"videoAssetFilePath",
		"thumbAssetId",
		"captionsInfo"
	);

	public function getMapBetweenObjects ( )
	{
		return array_merge ( parent::getMapBetweenObjects() , self::$map_between_objects );
	}
	
	private function addCaptionsData(KalturaDistributionJobData $distributionJobData) 
	{
		$assetIdsArray = explode ( ',', $distributionJobData->entryDistribution->assetIds );
		if (empty($distributionJobData->entryDistribution->assetIds) || empty($assetIdsArray)) return;
		$this->captionsInfo = new KalturaFacebookCaptionDistributionInfoArray();
		
		foreach ( $assetIdsArray as $assetId ) 
		{
			$asset = assetPeer::retrieveByIdNoFilter( $assetId );
			if (!$asset)
			{
				KalturaLog::err("Asset [$assetId] not found");
				continue;
			}
			if($asset->getType() != CaptionPlugin::getAssetTypeCoreValue ( CaptionAssetType::CAPTION ))
			{
				KalturaLog::debug("Asset [$assetId] is not a caption");
				continue;				
			}
			if ($asset->getStatus() == asset::ASSET_STATUS_READY) 
			{
				$syncKey = $asset->getSyncKey ( asset::FILE_SYNC_ASSET_SUB_TYPE_ASSET );
				if (kFileSyncUtils::fileSync_exists ( $syncKey )) 
				{
					$captionInfo = $this->getCaptionInfo($asset);
					if($captionInfo)
					{
						$captionInfo->filePath = kFileSyncUtils::getLocalFilePathForKey ( $syncKey, false );
						$this->captionsInfo [] = $captionInfo;
					}					 
				}						
			}
			else
			{
				KalturaLog::debug("Asset [$assetId] has status [".$asset->getStatus()."]. not added to provider data");
			}
		}
	}
	
	private function getCaptionInfo($asset)
	{
		$captionInfo = new KalturaFacebookCaptionDistributionInfo();
		$captionInfo->assetId = $asset->getId();
		$captionInfo->version = $asset->getVersion();
		$captionInfo->label = $asset->getLabel();
		$captionInfo->language = $asset->getLanguage();
		
		if(!$captionInfo->label && !$captionInfo->language)
		{
			KalturaLog::err('The caption ['.$asset->getId().'] has unrecognized language ['.$asset->getLanguage().'] and label ['.$asset->getLabel().']');
			return null;
		}

		return $captionInfo;
	}
	
	private function getValidVideoPath(KalturaDistributionJobData $distributionJobData)
	{
		$flavorAssets = array();
		$videoAssetFilePath = null;
		$isValidVideo = false;
		
		if(count($distributionJobData->entryDistribution->flavorAssetIds))
		{
			$flavorAssets = assetPeer::retrieveByIds(explode(',', $distributionJobData->entryDistribution->flavorAssetIds));
		}
		else 
		{
			$flavorAssets = assetPeer::retrieveReadyFlavorsByEntryId($distributionJobData->entryDistribution->entryId);
		}
		
		foreach ($flavorAssets as $flavorAsset) 
		{
			$syncKey = $flavorAsset->getSyncKey(flavorAsset::FILE_SYNC_ASSET_SUB_TYPE_ASSET);
			if(kFileSyncUtils::fileSync_exists($syncKey))
			{
				$videoAssetFilePath = kFileSyncUtils::getLocalFilePathForKey($syncKey, false);
				$mediaInfo = mediaInfoPeer::retrieveByFlavorAssetId($flavorAsset->getId());
				if($mediaInfo)
				{
					try
					{
						FacebookGraphSdkUtils::validateVideoAttributes($videoAssetFilePath, $mediaInfo->getFileSize(), $mediaInfo->getVideoDuration());
						$isValidVideo = true;
					}
					catch(Exception $e)
					{
						KalturaLog::debug('Asset ['.$flavorAsset->getId().'] not valid for distribution: '.$e->getMessage());
					}	
				}
				if($isValidVideo)
					break;		
			}				
		}		
		return $videoAssetFilePath;
	}

}
