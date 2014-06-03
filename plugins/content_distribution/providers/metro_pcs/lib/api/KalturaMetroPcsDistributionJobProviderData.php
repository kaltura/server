<?php
/**
 * @package plugins.metroPcsDistribution
 * @subpackage api.objects
 */
class KalturaMetroPcsDistributionJobProviderData extends KalturaConfigurableDistributionJobProviderData
{
		
	/**
	 * @var string
	 */
	public $assetLocalPaths;
	
	
	/**
	 * @var string
	 */
	public $thumbUrls;
	
	
	public function __construct(KalturaDistributionJobData $distributionJobData = null)
	{			   
		parent::__construct($distributionJobData);
	    
		if(!$distributionJobData)
			return;
			
		if(!($distributionJobData->distributionProfile instanceof KalturaMetroPcsDistributionProfile))
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
						$videoAssetFilePathArray[$id] = kFileSyncUtils::getLocalFilePathForKey($syncKey, true);
					}
				}
			}						
			$this->assetLocalPaths = serialize($videoAssetFilePathArray);	
		}
		
		//thumbnails
		$thumbnails = assetPeer::retrieveByIds(explode(',', $distributionJobData->entryDistribution->thumbAssetIds));
		if (count($thumbnails))
		{
			$thumbUrlsArray = array();
			foreach ($thumbnails as $thumb)
			{
				$thumbUrlsArray[$thumb->getId()] = self::getAssetUrl($thumb);
			}
			$this->thumbUrls = serialize($thumbUrlsArray);
		}
		
	}
		
	private static $map_between_objects = array
	(
		"assetLocalPaths",
		"thumbUrls",
	);

	public function getMapBetweenObjects()
	{
		return array_merge(parent::getMapBetweenObjects(), self::$map_between_objects);
	}
	
	
	protected static function getAssetUrl(asset $asset)
	{
		$urlManager = DeliveryProfilePeer::getDeliveryProfile($asset->getEntryId());
		$urlManager->getFullAssetUrl($asset);
		$url = preg_replace('/^https?:\/\//', '', $url);
		$url = 'http://' . $url . '/ext/' . $asset->getId() . '.' . $asset->getFileExt(); 
		return $url;
	}
	
}
