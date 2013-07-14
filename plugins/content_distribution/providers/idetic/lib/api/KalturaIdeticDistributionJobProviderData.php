<?php
/**
 * @package plugins.ideticDistribution
 * @subpackage api.objects
 */
class KalturaIdeticDistributionJobProviderData extends KalturaConfigurableDistributionJobProviderData
{	
	
	/**
	 * @var string
	 */
	public $thumbnailUrl;
	
	/**
	 * @var string
	 */
	public $flavorAssetUrl;
	
	
	private static $map_between_objects = array
	(
		"thumbnailUrl",
		"flavorAssetUrl",
	);

	public function getMapBetweenObjects ( )
	{
		return array_merge ( parent::getMapBetweenObjects() , self::$map_between_objects );
	}
	
	public function toObject($dbObject = null, $skip = array())
	{
		if (is_null($dbObject))
			$dbObject = new kIdeticDistributionJobProviderData();
			
		return parent::toObject($dbObject, $skip);
	}
	
	
	public function __construct(KalturaDistributionJobData $distributionJobData = null)
	{
	    parent::__construct($distributionJobData);
	    
		if(!$distributionJobData)
			return;
			
		if(!($distributionJobData->distributionProfile instanceof KalturaIdeticDistributionProfile))
			return;
			
		$flavorAsset=null;
		$flavorAssets = assetPeer::retrieveByIds(explode(',', $distributionJobData->entryDistribution->flavorAssetIds));
		if(count($flavorAssets)) // if we have specific flavor assets for this distribution, grab the first one
			$flavorAsset = reset($flavorAssets);
		if($flavorAsset) 
		{
			$this->flavorAssetUrl = $flavorAsset->getDownloadUrl();
		}
		
		$thumbAssets = assetPeer::retrieveByIds(explode(',', $distributionJobData->entryDistribution->thumbAssetIds));
		if(count($thumbAssets))
		{
			$thumbAsset = reset($thumbAssets)->getSyncKey(thumbAsset::FILE_SYNC_FLAVOR_ASSET_SUB_TYPE_ASSET);
			$this->thumbnailUrl = $thumbAsset->getDownloadUrl();
		}
	}
	
}
