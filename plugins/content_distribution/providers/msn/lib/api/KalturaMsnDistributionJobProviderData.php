<?php
/**
 * @package plugins.msnDistribution
 * @subpackage api.objects
 */
class KalturaMsnDistributionJobProviderData extends KalturaDistributionJobProviderData
{
	/**
	 * @var string
	 */
	public $xml;
	
	/**
	 * @var string
	 */
	public $csId;
	
	/**
	 * @var string
	 */
	public $source;
	
	/**
	 * @var int
	 */
	public $metadataProfileId;
	
	/**
	 * @var string
	 */
	public $movFlavorAssetId;
	
	/**
	 * @var string
	 */
	public $flvFlavorAssetId;
	
	/**
	 * @var string
	 */
	public $wmvFlavorAssetId;
	
	/**
	 * @var string
	 */
	public $thumbAssetId;
	
	/**
	 * @var int
	 */
	public $emailed;
	
	/**
	 * @var int
	 */
	public $rated;
	
	/**
	 * @var int
	 */
	public $blogged;
	
	/**
	 * @var int
	 */
	public $reviewed;
	
	/**
	 * @var int
	 */
	public $bookmarked;
	
	/**
	 * @var int
	 */
	public $playbackFailed;
	
	/**
	 * @var int
	 */
	public $timeSpent;
	
	/**
	 * @var int
	 */
	public $recommended;
	
	public function __construct(KalturaDistributionJobData $distributionJobData = null)
	{
		if(!$distributionJobData)
			return;
			
		if(!($distributionJobData->distributionProfile instanceof KalturaMsnDistributionProfile))
			return;
			
		$this->csId = $distributionJobData->distributionProfile->csId;
		$this->source = $distributionJobData->distributionProfile->source;
		$this->metadataProfileId = $distributionJobData->distributionProfile->metadataProfileId;
		
		$movFlavorAsset = flavorAssetPeer::retrieveByEntryIdAndFlavorParams($distributionJobData->entryDistribution->entryId, $distributionJobData->distributionProfile->movFlavorParamsId);
		if($movFlavorAsset)
			$this->movFlavorAssetId = $movFlavorAsset->getId();
		
		$flvFlavorAsset = flavorAssetPeer::retrieveByEntryIdAndFlavorParams($distributionJobData->entryDistribution->entryId, $distributionJobData->distributionProfile->flvFlavorParamsId);
		if($flvFlavorAsset)
			$this->flvFlavorAssetId = $flvFlavorAsset->getId();
		
		$wmvFlavorAsset = flavorAssetPeer::retrieveByEntryIdAndFlavorParams($distributionJobData->entryDistribution->entryId, $distributionJobData->distributionProfile->wmvFlavorParamsId);
		if($wmvFlavorAsset)
			$this->wmvFlavorAssetId = $wmvFlavorAsset->getId();
		
		$thumbAssets = thumbAssetPeer::retrieveByIds(explode(',', $distributionJobData->entryDistribution->thumbAssetIds));
		if(count($thumbAssets))
			$this->thumbAssetId = reset($thumbAssets)->getId();
			
		if($distributionJobData instanceof KalturaDistributionSubmitJobData)
			$this->xml = MsnDistributionProvider::generateSubmitXML($distributionJobData->entryDistribution->entryId, $this);
			
		if($distributionJobData instanceof KalturaDistributionDeleteJobData)
			$this->xml = MsnDistributionProvider::generateDeleteXML($distributionJobData->entryDistribution->entryId, $this);
			
		if($distributionJobData instanceof KalturaDistributionUpdateJobData)
			$this->xml = MsnDistributionProvider::generateUpdateXML($distributionJobData->entryDistribution->entryId, $this);
	}
		
	private static $map_between_objects = array
	(
		"xml" ,
		"csId" ,
		"source" ,
		"metadataProfileId" ,
		"movFlavorAssetId" ,
		"flvFlavorAssetId" ,
		"wmvFlavorAssetId" ,
		"thumbAssetId" ,
		"emailed" ,
		"rated" ,
		"blogged" ,
		"reviewed" ,
		"bookmarked" ,
		"playbackFailed" ,
		"timeSpent" ,
		"recommended" ,
	);

	public function getMapBetweenObjects ( )
	{
		return array_merge ( parent::getMapBetweenObjects() , self::$map_between_objects );
	}
}
