<?php
/**
 * @package plugins.youtubeApiDistribution
 * @subpackage api.objects
 */
class KalturaYoutubeApiDistributionJobProviderData extends KalturaConfigurableDistributionJobProviderData
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
	 * @var KalturaYouTubeApiCaptionDistributionInfoArray
	 */
	public $captionsInfo;	

	public function __construct(KalturaDistributionJobData $distributionJobData = null)
	{
		parent::__construct($distributionJobData);
	    
		if(!$distributionJobData)
			return;
		
		if(!($distributionJobData->distributionProfile instanceof KalturaYoutubeApiDistributionProfile))
			return;
			
		$flavorAssets = assetPeer::retrieveByIds(explode(',', $distributionJobData->entryDistribution->flavorAssetIds));
		if(count($flavorAssets)) // if we have specific flavor assets for this distribution, grab the first one
			$flavorAsset = reset($flavorAssets);
		else // take the source asset
			$flavorAsset = assetPeer::retrieveOriginalReadyByEntryId($distributionJobData->entryDistribution->entryId);
		
		if($flavorAsset) 
		{
			$syncKey = $flavorAsset->getSyncKey(flavorAsset::FILE_SYNC_FLAVOR_ASSET_SUB_TYPE_ASSET);
			if(kFileSyncUtils::fileSync_exists($syncKey))
				$this->videoAssetFilePath = kFileSyncUtils::getLocalFilePathForKey($syncKey, false);
		}
		
		$thumbAssets = assetPeer::retrieveByIds(explode(',', $distributionJobData->entryDistribution->thumbAssetIds));
		if(count($thumbAssets))
		{
			$syncKey = reset($thumbAssets)->getSyncKey(thumbAsset::FILE_SYNC_FLAVOR_ASSET_SUB_TYPE_ASSET);
			if(kFileSyncUtils::fileSync_exists($syncKey))
				$this->thumbAssetFilePath = kFileSyncUtils::getLocalFilePathForKey($syncKey, false);
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
	
	private function addCaptionsData(KalturaDistributionJobData $distributionJobData) {
		/* @var $mediaFile KalturaDistributionRemoteMediaFile */
		$assetIdsArray = explode ( ',', $distributionJobData->entryDistribution->assetIds );
		$assets = array ();
		$this->captionsInfo = array ();
		
		foreach ( $assetIdsArray as $assetId ) {
			$asset = assetPeer::retrieveById ( $assetId );
			if ($asset) {
				$assets [] = $asset;
			} //if the asset was not retrieved it means the asset was deleted
			else {
				$captionInfo = new KalturaYouTubeApiCaptionDistributionInfo ();
				$captionInfo->action = KalturaYouTubeApiDistributionCaptionAction::DELETE_ACTION;
				$captionInfo->assetId = $assetId;
				//getting the asset's remote id
				foreach ( $distributionJobData->mediaFiles as $mediaFile ) {
					if ($mediaFile->assetId == $assetId) {
						$captionInfo->remoteId = $mediaFile->remoteId;
						$this->captionsInfo [] = $captionInfo;
						break;
					}
				}
			}
		}
		
		foreach ( $assets as $asset ) {
			$assetType = $asset->getType ();
			switch ($assetType) {
				case CaptionPlugin::getAssetTypeCoreValue ( CaptionAssetType::CAPTION ):
					/* @var $asset CaptionAsset */
					$syncKey = $asset->getSyncKey ( asset::FILE_SYNC_FLAVOR_ASSET_SUB_TYPE_ASSET );
					if (kFileSyncUtils::fileSync_exists ( $syncKey )) {
						$captionInfo = $this->getCaptionInfo($asset, $syncKey);
						if ($captionInfo){
							$captionInfo->language = $asset->getLanguage();
							$this->captionsInfo [] = $captionInfo;
						}
					}
					break;
				case AttachmentPlugin::getAssetTypeCoreValue ( AttachmentAssetType::ATTACHMENT ) :
					$syncKey = $asset->getSyncKey ( asset::FILE_SYNC_FLAVOR_ASSET_SUB_TYPE_ASSET );
					if (kFileSyncUtils::fileSync_exists ( $syncKey )) {
						$captionInfo = $this->getCaptionInfo($asset, $syncKey);
						if ($captionInfo){
							//TODO how to get the caption language
							$captionInfo->language = 'en'; 
							$this->captionsInfo [] = $captionInfo;
						}
					}
					break;
			}
		}
	}
	
	private function getCaptionInfo($asset, $syncKey) {
		$captionInfo = new KalturaYouTubeApiCaptionDistributionInfo ();
		$captionInfo->filePath = kFileSyncUtils::getLocalFilePathForKey ( $syncKey, false );
		$captionInfo->assetId = $asset->getId();
		$captionInfo->version = $asset->getVersion();
		/* @var $mediaFile KalturaDistributionRemoteMediaFile */
		$distributed = false;
		foreach ( $distributionJobData->mediaFiles as $mediaFile ) {
			if ($mediaFile->assetId == $asset->getId ()) {
				$distributed = true;
				if ($asset->getVersion () > $mediaFile->version) {
					$captionInfo->action = KalturaYouTubeApiDistributionCaptionAction::UPDATE_ACTION;
				}
				break;
			}
		}
		if (! $distributed)
			$captionInfo->action = KalturaYouTubeApiDistributionCaptionAction::SUBMIT_ACTION;
		elseif ($captionInfo->action != KalturaYouTubeApiDistributionCaptionAction::UPDATE_ACTION) {
			return;
		}
		return $captionInfo;
	}
}
