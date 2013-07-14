<?php
/**
 * @package plugins.huluDistribution
 * @subpackage api.objects
 */
class KalturaHuluDistributionJobProviderData extends KalturaConfigurableDistributionJobProviderData
{
	/**
	 * @var string
	 */
	public $videoAssetFilePath;
	
	/**
	 * @var string
	 */
	public $thumbAssetFilePath;
	
	/**
	 * @var KalturaCuePointArray
	 */
	public $cuePoints;
	
	/**
	 * @var string
	 */
	public $fileBaseName;
	
	/**
	 * @var KalturaStringArray
	 */
	public $captionLocalPaths;
	
 
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
			
		if(!($distributionJobData->distributionProfile instanceof KalturaHuluDistributionProfile))
			return;
			
		
		// loads all the flavor assets that should be submitted to the remote destination site
		$flavorAssets = assetPeer::retrieveByIds(explode(',', $distributionJobData->entryDistribution->flavorAssetIds));
		if(count($flavorAssets))
		{
			$flavorAsset = reset($flavorAssets);
			$syncKey = $flavorAsset->getSyncKey(flavorAsset::FILE_SYNC_FLAVOR_ASSET_SUB_TYPE_ASSET);
			$this->videoAssetFilePath = kFileSyncUtils::getLocalFilePathForKey($syncKey, false);
		}
		
		$thumbAssets = assetPeer::retrieveByIds(explode(',', $distributionJobData->entryDistribution->thumbAssetIds));
		if(count($thumbAssets))
		{
			$thumbAsset = reset($thumbAssets);
			$syncKey = $thumbAsset->getSyncKey(thumbAsset::FILE_SYNC_FLAVOR_ASSET_SUB_TYPE_ASSET);
			$this->thumbAssetFilePath = kFileSyncUtils::getLocalFilePathForKey($syncKey, false);
		}
		
		$additionalAssets = assetPeer::retrieveByIds(explode(',', $distributionJobData->entryDistribution->assetIds));
		$this->captionLocalPaths = new KalturaStringArray();
		if(count($additionalAssets))
		{
			$captionAssetFilePathArray = array();
			foreach ($additionalAssets as $additionalAsset)
			{	
				$assetType = $additionalAsset->getType();
				$syncKey = $additionalAsset->getSyncKey(CaptionAsset::FILE_SYNC_ASSET_SUB_TYPE_ASSET);
				if(kFileSyncUtils::fileSync_exists($syncKey)){
					if (($assetType == CaptionPlugin::getAssetTypeCoreValue(CaptionAssetType::CAPTION))||
						($assetType == AttachmentPlugin::getAssetTypeCoreValue(AttachmentAssetType::ATTACHMENT))){
						$string = new KalturaString();
						$string->value = kFileSyncUtils::getLocalFilePathForKey($syncKey, false); 
						$this->captionLocalPaths[] =  $string;
					}
				}
			}
		}
		
		$tempFieldValues = unserialize($this->fieldValues);
		$pattern = '/[^A-Za-z0-9_\-]/';
		$seriesTitle = preg_replace($pattern, '', $tempFieldValues[HuluDistributionField::SERIES_TITLE]);
		$seasonNumber = preg_replace($pattern, '', $tempFieldValues[HuluDistributionField::SEASON_NUMBER]);
		$videoEpisodeNumber =  preg_replace($pattern, '', $tempFieldValues[HuluDistributionField::VIDEO_EPISODE_NUMBER]);
		$videoTitle = preg_replace($pattern, '', $tempFieldValues[HuluDistributionField::VIDEO_TITLE]);
		$this->fileBaseName = $seriesTitle.'-'.$seasonNumber.'-'.$videoEpisodeNumber.'-'.$videoTitle;
		
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
		'videoAssetFilePath',
		'thumbAssetFilePath',
		'fileBaseName',
	);

	/* (non-PHPdoc)
	 * @see KalturaObject::getMapBetweenObjects()
	 */
	public function getMapBetweenObjects ( )
	{
		return array_merge ( parent::getMapBetweenObjects() , self::$map_between_objects );
	}
}
