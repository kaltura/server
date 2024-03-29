<?php
/**
 * @package plugins.cortexApiDistribution
 * @subpackage api.objects
 */
class KalturaCortexApiDistributionJobProviderData extends KalturaConfigurableDistributionJobProviderData
{
	/**
	 * @var string
	 */
	public $videoAssetFilePath;
	
	/**
	 * @var string
	 */
	public $thumbAssetFilePath;

	/**
	 * @var KalturaCortexApiCaptionDistributionInfoArray
	 */
	public $captionsInfo;

	public function __construct(KalturaDistributionJobData $distributionJobData = null)
	{
		parent::__construct($distributionJobData);
	    
		if(!$distributionJobData)
		{
			return;
		}
		
		if(!($distributionJobData->distributionProfile instanceof KalturaCortexApiDistributionProfile))
		{
			return;
		}
			
		$flavorAssets = assetPeer::retrieveByIds(explode(',', $distributionJobData->entryDistribution->flavorAssetIds));
		if(count($flavorAssets))  // if we have specific flavor assets for this distribution, grab the first one
		{
			$flavorAsset = reset($flavorAssets);
		}
		else // take the source asset
		{
			$flavorAsset = assetPeer::retrieveOriginalReadyByEntryId($distributionJobData->entryDistribution->entryId);
		}

		
		if($flavorAsset) 
		{
			$syncKey = $flavorAsset->getSyncKey(flavorAsset::FILE_SYNC_FLAVOR_ASSET_SUB_TYPE_ASSET);
			if(kFileSyncUtils::fileSync_exists($syncKey))
			{
				$this->videoAssetFilePath = kFileSyncUtils::getLocalFilePathForKey($syncKey, false);
			}
		}
		
		$thumbAssets = assetPeer::retrieveByIds(explode(',', $distributionJobData->entryDistribution->thumbAssetIds));
		if(count($thumbAssets))
		{
			$syncKey = reset($thumbAssets)->getSyncKey(thumbAsset::FILE_SYNC_FLAVOR_ASSET_SUB_TYPE_ASSET);
			if(kFileSyncUtils::fileSync_exists($syncKey))
			{
				$this->thumbAssetFilePath = kFileSyncUtils::getLocalFilePathForKey($syncKey, false);
			}
		}
		
		$this->addCaptionsData($distributionJobData);
	}
	
	private static $map_between_objects = array
	(
		"videoAssetFilePath",
		"thumbAssetFilePath",
		"captionsInfo",
	);

	public function getMapBetweenObjects ( )
	{
		return array_merge ( parent::getMapBetweenObjects() , self::$map_between_objects );
	}
	
	protected function addCaptionsData(KalturaDistributionJobData $distributionJobData)
	{
		/* @var $mediaFile KalturaDistributionRemoteMediaFile */
		$assetIdsArray = explode ( ',', $distributionJobData->entryDistribution->assetIds );
		if (empty($assetIdsArray))
		{
			return;
		}
		$assets = array ();
		$this->captionsInfo = new KalturaCortexApiCaptionDistributionInfoArray();
		
		foreach ( $assetIdsArray as $assetId )
		{
			$asset = assetPeer::retrieveByIdNoFilter( $assetId );
			if (!$asset)
			{
				KalturaLog::err("Asset [$assetId] not found");
				continue;
			}
			if ($asset->getStatus() == asset::ASSET_STATUS_READY)
			{
				$assets [] = $asset;
			}
			else{
				KalturaLog::err("Asset [$assetId] has status [".$asset->getStatus()."]. not added to provider data");
			}
		}

		foreach ( $assets as $asset )
		{
			$assetType = $asset->getType ();
			if($assetType == CaptionPlugin::getAssetTypeCoreValue ( CaptionAssetType::CAPTION ))
			{
				$syncKey = $asset->getSyncKey ( asset::FILE_SYNC_FLAVOR_ASSET_SUB_TYPE_ASSET );
				if (kFileSyncUtils::fileSync_exists ( $syncKey ) && $asset->getStatus() == KalturaCaptionAssetStatus::READY)
				{
					$captionInfo = $this->getCaptionInfo($asset, $syncKey, $distributionJobData);
					if ($captionInfo)
					{
						$captionInfo->label = $asset->getLabel();
						if(!$captionInfo->label)
							$captionInfo->label = $asset->getLanguage();
						$captionInfo->language = $this->getLanguageCode($asset->getLanguage());
						if ($captionInfo->language)
							$this->captionsInfo [] = $captionInfo;
						else
							KalturaLog::err('The caption ['.$asset->getId().'] has unrecognized language ['.$asset->getLanguage().']');
					}
				}
			}
		}
	}
	
	protected function getLanguageCode($language = null)
	{
		$languageReflector = KalturaTypeReflectorCacher::get('KalturaLanguage');
		$languageCodeReflector = KalturaTypeReflectorCacher::get('KalturaLanguageCode');
		if($languageReflector && $languageCodeReflector)
		{
			$languageCode = $languageReflector->getConstantName($language);
			if($languageCode)
			{
				return $languageCodeReflector->getConstantValue($languageCode);
			}
		}
		return null;
	}

	/**
	 * @param CaptionAsset $asset
	 * @param                            $syncKey
	 * @param KalturaDistributionJobData $distributionJobData
	 * @return KalturaCortexApiCaptionDistributionInfo|void
	 */
	protected function getCaptionInfo($asset, $syncKey, KalturaDistributionJobData $distributionJobData)
	{
		$captionInfo = new KalturaCortexApiCaptionDistributionInfo ();
		$fileSync = kFileSyncUtils::getResolveLocalFileSyncForKey($syncKey);
		$captionInfo->filePath = $fileSync->getFullPath();
		$captionInfo->assetId = $asset->getId();
		$captionInfo->version = $asset->getVersion();
		$captionInfo->fileExt = $asset->getFileExt();
		/* @var $mediaFile KalturaDistributionRemoteMediaFile */
		$distributed = false;
		foreach ( $distributionJobData->mediaFiles as $mediaFile )
		{
			if ($mediaFile->assetId == $asset->getId ())
			{
				$distributed = true;
				if ($asset->getVersion () > $mediaFile->version)
				{
					$captionInfo->action = KalturaCortexApiDistributionCaptionAction::UPDATE_ACTION;
				}
				break;
			}
		}
		if (! $distributed)
		{
			$captionInfo->action = KalturaCortexApiDistributionCaptionAction::SUBMIT_ACTION;
		}
		elseif ($captionInfo->action != KalturaCortexApiDistributionCaptionAction::UPDATE_ACTION)
		{
			return;
		}
		return $captionInfo;
	}
}
