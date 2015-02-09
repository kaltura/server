<?php
/**
 * @package plugins.freewheelGenericDistribution
 * @subpackage api.objects
 */
class KalturaFreewheelGenericDistributionJobProviderData extends KalturaConfigurableDistributionJobProviderData
{
	/**
	 * Demonstrate passing array of paths to the job
	 * 
	 * @var KalturaStringArray
	 */
	public $videoAssetFilePaths;
	
	/**
	 * Demonstrate passing single path to the job
	 * 
	 * @var string
	 */
	public $thumbAssetFilePath;
	
	/**
	 * @var KalturaCuePointArray
	 */
	public $cuePoints;
	

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
			
		if(!($distributionJobData->distributionProfile instanceof KalturaFreewheelGenericDistributionProfile))
			return;
			
		$this->videoAssetFilePaths = new KalturaStringArray();
		
		// loads all the flavor assets that should be submitted to the remote destination site
		$flavorAssets = assetPeer::retrieveByIds(explode(',', $distributionJobData->entryDistribution->flavorAssetIds));
		foreach($flavorAssets as $flavorAsset)
		{
			$videoAssetFilePath = new KalturaString();
			$syncKey = $flavorAsset->getSyncKey(flavorAsset::FILE_SYNC_FLAVOR_ASSET_SUB_TYPE_ASSET);
			$videoAssetFilePath->value = kFileSyncUtils::getLocalFilePathForKey($syncKey, false);
			$this->videoAssetFilePaths[] = $videoAssetFilePath;
		}
		
		$thumbAssets = assetPeer::retrieveByIds(explode(',', $distributionJobData->entryDistribution->thumbAssetIds));
		if(count($thumbAssets))
		{
			$thumbAsset = reset($thumbAssets);
			$syncKey = $thumbAssets->getSyncKey(thumbAsset::FILE_SYNC_FLAVOR_ASSET_SUB_TYPE_ASSET);
			$this->thumbAssetFilePath = kFileSyncUtils::getLocalFilePathForKey($syncKey, false);
		}
		
		// entry cue points
		$c = KalturaCriteria::create(CuePointPeer::OM_CLASS);
		$c->add(CuePointPeer::PARTNER_ID, $distributionJobData->entryDistribution->partnerId);
		$c->add(CuePointPeer::ENTRY_ID, $distributionJobData->entryDistribution->entryId);
		$c->add(CuePointPeer::TYPE, AdCuePointPlugin::getCuePointTypeCoreValue(AdCuePointType::AD));
		$c->addAscendingOrderByColumn(CuePointPeer::START_TIME);
		$cuePointsDb = CuePointPeer::doSelect($c);
		$this->cuePoints = KalturaCuePointArray::fromDbArray($cuePointsDb);
	}
		
	/**
	 * Maps the object attributes to getters and setters for Core-to-API translation and back
	 *  
	 * @var array
	 */
	private static $map_between_objects = array
	(
		"thumbAssetFilePath",
	);

	/* (non-PHPdoc)
	 * @see KalturaObject::getMapBetweenObjects()
	 */
	public function getMapBetweenObjects ( )
	{
		return array_merge ( parent::getMapBetweenObjects() , self::$map_between_objects );
	}
	
	/* (non-PHPdoc)
	 * @see KalturaObject::toObject()
	 */
	public function toObject($object = null, $skip = array())
	{
		$object = parent::toObject($object, $skip);
		
		if($this->videoAssetFilePaths)
		{
			$videoAssetFilePaths = array();
			foreach($this->videoAssetFilePaths as $videoAssetFilePath)
			{
				/* @var $videoAssetFilePath KalturaString */
				$videoAssetFilePaths[] = $videoAssetFilePath->value;
			}
				
			$object->setVideoAssetFilePaths($videoAssetFilePaths);
		}
		
		return $object;
	}
	
	/* (non-PHPdoc)
	 * @see KalturaObject::fromObject()
	 */
	public function fromObject($object, IResponseProfile $responseProfile = null)
	{
		parent::fromObject($object, $responseProfile);
		$videoAssetFilePaths = $object->getVideoAssetFilePaths();
		if($videoAssetFilePaths && is_array($videoAssetFilePaths))
		{
			$this->videoAssetFilePaths = new KalturaStringArray();
			foreach($videoAssetFilePaths as $assetFilePath)
			{
				$videoAssetFilePath = new KalturaString();
				$videoAssetFilePath->value = $assetFilePath;
				$this->videoAssetFilePaths[] = $videoAssetFilePath;
			}
		}
	}
}
