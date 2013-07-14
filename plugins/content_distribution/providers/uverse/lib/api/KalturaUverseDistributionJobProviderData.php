<?php
/**
 * @package plugins.uverseDistribution
 * @subpackage api.objects
 */
class KalturaUverseDistributionJobProviderData extends KalturaConfigurableDistributionJobProviderData
{
	/**
	 * The local file path of the video asset that needs to be distributed
	 * 
	 * @var string
	 */
	public $localAssetFilePath;
	
	/**
	 * The remote URL of the video asset that was distributed
	 * 
	 * @var string
	 */
	public $remoteAssetUrl;
	
	/**
	 * The file name of the remote video asset that was distributed
	 * 
	 * @var string
	 */
	public $remoteAssetFileName;
	
	public function __construct(KalturaDistributionJobData $distributionJobData = null)
	{
		parent::__construct($distributionJobData);

		if(!$distributionJobData)
			return;
			
		if(!($distributionJobData->distributionProfile instanceof KalturaUverseDistributionProfile))
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
				$this->localAssetFilePath = kFileSyncUtils::getLocalFilePathForKey($syncKey, false);
		}
		
		$entryDistributionDb = EntryDistributionPeer::retrieveByPK($distributionJobData->entryDistributionId);
		if ($entryDistributionDb)
		{
			$this->remoteAssetUrl = $entryDistributionDb->getFromCustomData(UverseEntryDistributionCustomDataField::REMOTE_ASSET_URL);
			$this->remoteAssetFileName = $entryDistributionDb->getFromCustomData(UverseEntryDistributionCustomDataField::REMOTE_ASSET_FILE_NAME);
		}
		else
			KalturaLog::err('Entry distribution ['.$distributionJobData->entryDistributionId.'] not found');
	}
		
	private static $map_between_objects = array
	(
		'localAssetFilePath',
		'remoteAssetUrl',
		'remoteAssetFileName'
	);

	public function getMapBetweenObjects()
	{
		return array_merge(parent::getMapBetweenObjects(), self::$map_between_objects);
	}
	
}
