<?php
class KalturaYouTubeDistributionJobProviderData extends KalturaDistributionJobProviderData
{
	/**
	 * @var string
	 */
	public $videoAssetFilePath;
	
	/**
	 * @var string
	 */
	public $sftpDirectory;
	
	/**
	 * @var string
	 */
	public $sftpMetadataFilename;
	
	public function __construct(KalturaDistributionJobData $distributionJobData = null)
	{
		if(!$distributionJobData)
			return;
			
		if(!($distributionJobData->distributionProfile instanceof KalturaYouTubeDistributionProfile))
			return;
			
		$this->csId = $distributionJobData->distributionProfile->csId;
		$this->source = $distributionJobData->distributionProfile->source;
		$this->metadataProfileId = $distributionJobData->distributionProfile->metadataProfileId;
		
		$sourceAsset = flavorAssetPeer::retrieveOriginalReadyByEntryId($distributionJobData->entryDistribution->entryId);
		if($sourceAsset) 
		{
			$syncKey = $sourceAsset->getSyncKey(flavorAsset::FILE_SYNC_FLAVOR_ASSET_SUB_TYPE_ASSET);
			$this->videoAssetFilePath = kFileSyncUtils::getLocalFilePathForKey($syncKey, true);
		}
	}
		
	private static $map_between_objects = array
	(
		"videoAssetFilePath",
		"sftpDirectory",
		"sftpMetadataFilename",
	);

	public function getMapBetweenObjects ( )
	{
		return array_merge ( parent::getMapBetweenObjects() , self::$map_between_objects );
	}
}
