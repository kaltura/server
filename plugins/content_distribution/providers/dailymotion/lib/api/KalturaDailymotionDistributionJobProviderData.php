<?php
/**
 * @package plugins.dailymotionDistribution
 * @subpackage api.objects
 */
class KalturaDailymotionDistributionJobProviderData extends KalturaConfigurableDistributionJobProviderData
{
	/**
	 * @var string
	 */
	public $videoAssetFilePath;
	
	
	public function __construct(KalturaDistributionJobData $distributionJobData = null)
	{
	    parent::__construct($distributionJobData);
	    
		if(!$distributionJobData)
			return;
			
		if(!($distributionJobData->distributionProfile instanceof KalturaDailymotionDistributionProfile))
			return;
			
		$flavorAssets = assetPeer::retrieveByIds(explode(',', $distributionJobData->entryDistribution->flavorAssetIds));
		if(count($flavorAssets)) // if we have specific flavor assets for this distribution, grab the first one
			$flavorAsset = reset($flavorAssets);
		else // take the source asset
			$flavorAsset = assetPeer::retrieveOriginalReadyByEntryId($distributionJobData->entryDistribution->entryId);
		
		if($flavorAsset) 
		{
			$syncKey = $flavorAsset->getSyncKey(flavorAsset::FILE_SYNC_FLAVOR_ASSET_SUB_TYPE_ASSET);
			$this->videoAssetFilePath = kFileSyncUtils::getLocalFilePathForKey($syncKey, true);
		}
		
	}


	
	private static $map_between_objects = array
	(
		"videoAssetFilePath",
	);

	public function getMapBetweenObjects ( )
	{
		return array_merge ( parent::getMapBetweenObjects() , self::$map_between_objects );
	}
	
	/**
	 * @return string $videoAssetFilePath
	 */
	public function getVideoAssetFilePath()
	{
		return $this->videoAssetFilePath;
	}

	/**
	 * @param string $videoAssetFilePath
	 */
	public function setVideoAssetFilePath($videoAssetFilePath)
	{
		$this->videoAssetFilePath = $videoAssetFilePath;
	}
}
