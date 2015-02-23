<?php
/**
 * @package plugins.unicornDistribution
 * @subpackage api.objects
 */
class KalturaUnicornDistributionJobProviderData extends KalturaConfigurableDistributionJobProviderData
{
	/**
	 * The Catalog GUID the video is in or will be ingested into.
	 * 
	 * @var string
	 */
	public $catalogGuid;
	
	/**
	 * The Title assigned to the video. The Foreign Key will be used if no title is provided.
	 * 
	 * @var string
	 */
	public $title;
	
	/**
	 * Indicates that the media content changed and therefore the job should wait for HTTP callback notification to be closed.
	 * 
	 * @var bool
	 */
	public $mediaChanged;
	
	/**
	 * Flavor asset version.
	 * 
	 * @var string
	 */
	public $flavorAssetVersion;
	
	/**
	 * The schema and host name to the Kaltura server, e.g. http://www.kaltura.com
	 * 
	 * @var string
	 */
	public $notificationBaseUrl;
	
	public function __construct(KalturaDistributionJobData $distributionJobData = null)
	{
		parent::__construct($distributionJobData);
		
		$this->notificationBaseUrl = 'http://' . kConf::get('cdn_api_host');
		
		if(!$distributionJobData)
			return;
		
		if(!($distributionJobData->distributionProfile instanceof KalturaUnicornDistributionProfile))
			return;
		
		$entryDistributionDb = EntryDistributionPeer::retrieveByPK($distributionJobData->entryDistributionId);
		$distributionProfileDb = DistributionProfilePeer::retrieveByPK($distributionJobData->distributionProfileId);
		/* @var $distributionProfileDb UnicornDistributionProfile */
		
		$flavorAssetIds = explode(',', $entryDistributionDb->getFlavorAssetIds());
		$flavorAssetId = reset($flavorAssetIds);
		$flavorAsset = assetPeer::retrieveById($flavorAssetId);
		$flavorAssetOldVersion = $entryDistributionDb->getFromCustomData(kUnicornDistributionJobProviderData::CUSTOM_DATA_FLAVOR_ASSET_OLD_VERSION);
		$flavorAssetNewVersion = null;
		if($flavorAsset)
		{
			$flavorAssetNewVersion = $flavorAsset->getVersion();
		}
		
		$values = $distributionProfileDb->getAllFieldValues($entryDistributionDb);
		$this->catalogGuid = $values[UnicornDistributionField::CATALOG_GUID];
		$this->title = $values[UnicornDistributionField::TITLE];
		$this->flavorAssetVersion = $flavorAssetNewVersion;
		$this->mediaChanged = ($flavorAssetOldVersion != $flavorAssetNewVersion);
	}
	
	private static $map_between_objects = array(
		'catalogGuid',
		'title',
		'mediaChanged',
		'flavorAssetVersion',
	);
	
	/* (non-PHPdoc)
	 * @see KalturaObject::getMapBetweenObjects()
	 */
	public function getMapBetweenObjects()
	{
		return array_merge(parent::getMapBetweenObjects(), self::$map_between_objects);
	}

}
