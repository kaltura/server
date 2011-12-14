<?php
/**
 * @package plugins.quickPlayDistribution
 * @subpackage api.objects
 */
class KalturaQuickPlayDistributionJobProviderData extends KalturaConfigurableDistributionJobProviderData
{
	/**
	 * @var string
	 */
	public $xml;

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
			
		if(!($distributionJobData->distributionProfile instanceof KalturaQuickPlayDistributionProfile))
			return;

		// loads all the flavor assets that should be submitted to the remote destination site
		$flavorAssets = assetPeer::retrieveByIds(explode(',', $distributionJobData->entryDistribution->flavorAssetIds));
		$thumbAssets = assetPeer::retrieveByIds(explode(',', $distributionJobData->entryDistribution->thumbAssetIds));
		$entry = entryPeer::retrieveByPK($distributionJobData->entryDistribution->entryId);
		$verizonFeed = new QuickPlayFeedHelper('quickplay_template.xml', $distributionJobData, $this, $flavorAssets, $thumbAssets, $entry);
		$this->xml = $verizonFeed->getXml();
		
		// save the flavors & their versions that we are sending
		$distributionJobData->mediaFiles = new KalturaDistributionRemoteMediaFileArray();
		foreach($flavorAssets as $flavorAsset)
		{
			$mediaFile = new KalturaDistributionRemoteMediaFile();
			$mediaFile->assetId = $flavorAsset->getId();
			$mediaFile->version = $flavorAsset->getVersion();
			$distributionJobData->mediaFiles[] = $mediaFile;
		}
	}
		
	/**
	 * Maps the object attributes to getters and setters for Core-to-API translation and back
	 *  
	 * @var array
	 */
	private static $map_between_objects = array
	(
		'xml',
	);

	/* (non-PHPdoc)
	 * @see KalturaObject::getMapBetweenObjects()
	 */
	public function getMapBetweenObjects ( )
	{
		return array_merge ( parent::getMapBetweenObjects() , self::$map_between_objects );
	}
}
