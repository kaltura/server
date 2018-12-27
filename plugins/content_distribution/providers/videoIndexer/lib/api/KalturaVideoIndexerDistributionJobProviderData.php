<?php
/**
 * @package plugins.videoIndexerDistribution
 * @subpackage api.objects
 */
class KalturaVideoIndexerDistributionJobProviderData extends KalturaDistributionJobProviderData
{

	/**
	 * @var string
	 */
	public $filePath;


	/**
	 * Called on the server side and enables you to populate the object with any data from the DB
	 * 
	 * @param KalturaDistributionJobData $distributionJobData
	 */
	public function __construct(KalturaDistributionJobData $distributionJobData = null)
	{
		if(!$distributionJobData)
			return;
			
		if(!($distributionJobData->distributionProfile instanceof KalturaVideoIndexerDistributionProfile))
			return;


		$flavorAsset = assetPeer::retrieveByEntryIdAndParams($distributionJobData->entryDistribution->entryId, 0);
		$syncKey = $flavorAsset->getSyncKey(flavorAsset::FILE_SYNC_FLAVOR_ASSET_SUB_TYPE_ASSET);
		$fileSync = FileSyncPeer::retrieveByFileSyncKey($syncKey, true);

		$this->filePath = $fileSync->getFilePath();
	}
		
	/**
	 * Maps the object attributes to getters and setters for Core-to-API translation and back
	 *  
	 * @var array
	 */
	private static $map_between_objects = array
	(
		'filePath'
	);

	/* (non-PHPdoc)
	 * @see KalturaObject::getMapBetweenObjects()
	 */
	public function getMapBetweenObjects ( )
	{
		return array_merge ( parent::getMapBetweenObjects() , self::$map_between_objects );
	}
}
