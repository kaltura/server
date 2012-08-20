<?php
/**
 * @package plugins.ftpDistribution
 * @subpackage api.objects
 */
class KalturaFtpDistributionJobProviderData extends KalturaConfigurableDistributionJobProviderData
{
	/**
	 * @var KalturaFtpDistributionFileArray
	 */
	public $filesForDistribution;
	
	/**
	 * Called on the server side and enables you to populate the object with any data from the DB
	 * 
	 * @param KalturaDistributionJobData $distributionJobData
	 */
	public function __construct(KalturaDistributionJobData $distributionJobData = null)
	{
		parent::__construct($distributionJobData);
		
		if(!$distributionJobData)
			return;
			
		if(!($distributionJobData->distributionProfile instanceof KalturaFtpDistributionProfile))
			return;
			
		$entryDistributionDb = EntryDistributionPeer::retrieveByPK($distributionJobData->entryDistributionId);
		$distributionProfileDb = DistributionProfilePeer::retrieveByPK($distributionJobData->distributionProfileId);
		
		if (is_null($entryDistributionDb))
			return KalturaLog::err('Entry distribution #'.$distributionJobData->entryDistributionId.' not found');
		
		if (is_null($distributionProfileDb))
			return KalturaLog::err('Distribution profile #'.$distributionJobData->distributionProfileId.' not found');

		if (!$distributionProfileDb instanceof FtpDistributionProfile)
			return KalturaLog::err('Distribution profile #'.$distributionJobData->distributionProfileId.' is not instance of FtpDistributionProfile');

		$this->filesForDistribution = $this->getDistributionFiles($distributionProfileDb, $entryDistributionDb);
	}
		
	/**
	 * Maps the object attributes to getters and setters for Core-to-API translation and back
	 *  
	 * @var array
	 */
	private static $map_between_objects = array
	(
		'filesForDistribution',
	);

	/* (non-PHPdoc)
	 * @see KalturaObject::getMapBetweenObjects()
	 */
	public function getMapBetweenObjects ( )
	{
		return array_merge ( parent::getMapBetweenObjects() , self::$map_between_objects );
	}
	
	protected function getDistributionFiles(FtpDistributionProfile $distributionProfileDb, EntryDistribution $entryDistributionDb)
	{
		$files = new KalturaFtpDistributionFileArray();

		if (!$distributionProfileDb->getDisableMetadata()) 
		{
			$file = new KalturaFtpDistributionFile();
			$metadataXml = $distributionProfileDb->getMetadataXml($entryDistributionDb);
			$file->filename = $distributionProfileDb->getMetadataFilename($entryDistributionDb);
			$file->contents = $metadataXml;
			$file->assetId = 'metadata';
			$file->hash = md5($metadataXml);
			$files[] = $file;
		}
		
		$flavorAssetsIds = explode(',', $entryDistributionDb->getFlavorAssetIds());
		$thumbnailAssetIds = explode(',', $entryDistributionDb->getThumbAssetIds());
		$assets = assetPeer::retrieveByIds(array_merge($flavorAssetsIds, $thumbnailAssetIds));
		foreach($assets as $asset) 
		{
			/* @var $assets asset */
			$syncKey = $asset->getSyncKey(flavorAsset::FILE_SYNC_FLAVOR_ASSET_SUB_TYPE_ASSET);
			
			$file = new KalturaFtpDistributionFile();
			$file->assetId = $asset->getId();
			$file->localFilePath = kFileSyncUtils::getLocalFilePathForKey($syncKey, false);
			$file->version = $syncKey->getVersion();
			$defaultFilename = pathinfo($file->localFilePath, PATHINFO_BASENAME);
			if ($asset instanceof thumbAsset)
				$file->filename = $distributionProfileDb->getThumbnailAssetFilename($entryDistributionDb, $defaultFilename, $asset->getId());
			else
				$file->filename = $distributionProfileDb->getFlavorAssetFilename($entryDistributionDb, $defaultFilename, $asset->getId());
				
			$files[] = $file;
		}
		
		return $files;
	}
}
