<?php
class KalturaComcastDistributionJobProviderData extends KalturaDistributionJobProviderData
{
	/**
	 * @var string
	 */
	public $xml;
	
	/**
	 * @var int
	 */
	public $metadataProfileId;
	
	/**
	 * @var string
	 */
	public $thumbAssetId;
	
	/**
	 * @var string
	 */
	public $flavorAssetId;
	
	/**
	 * @var string
	 */
	public $keywords;
	
	/**
	 * @var string
	 */
	public $author;
	
	/**
	 * @var string
	 */
	public $album;
	
	public function __construct(KalturaDistributionJobData $distributionJobData = null)
	{
		if(!$distributionJobData)
			return;
			
		if(!($distributionJobData->distributionProfile instanceof KalturaComcastDistributionProfile))
			return;
		
		$this->metadataProfileId = $distributionJobData->distributionProfile->metadataProfileId;	
		$this->keywords = $distributionJobData->distributionProfile->keywords;
		$this->author = $distributionJobData->distributionProfile->author;
		$this->album = $distributionJobData->distributionProfile->album;
		
		assetPeer::resetInstanceCriteriaFilter();
		
		$flavorAssets = assetPeer::retrieveByPKs(explode(',', $distributionJobData->entryDistribution->flavorAssetIds));
		$this->flavorAssetId = reset($flavorAssets)->getId();
		
		$thumbAssets = assetPeer::retrieveByPKs(explode(',', $distributionJobData->entryDistribution->thumbAssetIds));
		$this->thumbAssetId = reset($thumbAssets)->getId();
			
		if($distributionJobData instanceof KalturaDistributionSubmitJobData)
			$this->xml = ComcastDistributionProvider::generateSubmitXML($distributionJobData->entryDistribution->entryId, $this);
			
//		TODO - currently not supported
//				
//		if($distributionJobData instanceof KalturaDistributionDeleteJobData)
//			$this->xml = ComcastDistributionProvider::generateDeleteXML($distributionJobData->entryDistribution->entryId, $this);
//			
//		if($distributionJobData instanceof KalturaDistributionUpdateJobData)
//			$this->xml = ComcastDistributionProvider::generateUpdateXML($distributionJobData->entryDistribution->entryId, $this);
	}
		
	private static $map_between_objects = array
	(
		"xml" ,
		"metadataProfileId" ,
		"thumbAssetId" ,
		"flavorAssetId" ,
		"keywords" ,
		"author" ,
		"album" ,
	);

	public function getMapBetweenObjects ( )
	{
		return array_merge ( parent::getMapBetweenObjects() , self::$map_between_objects );
	}
}
