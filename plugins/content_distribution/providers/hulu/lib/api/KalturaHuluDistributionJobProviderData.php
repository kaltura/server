<?php
/**
 * @package plugins.huluDistribution
 * @subpackage api.objects
 */
class KalturaHuluDistributionJobProviderData extends KalturaDistributionJobProviderData
{
	/**
	 * @var string
	 */
	public $xml;
	
	/**
	 * @var string
	 */
	public $xmlFileName;
	
	/**
	 * @var int
	 */
	public $metadataProfileId;
	
	/**
	 * @var int
	 */
	public $distributionProfileId;
	
	/**
	 * @var string
	 */
	public $aspectRatio;
	
	/**
	 * @var int
	 */
	public $frameRate;
	
	public function __construct(KalturaDistributionJobData $distributionJobData = null)
	{
		if(!$distributionJobData)
			return;
			
		if(!($distributionJobData->distributionProfile instanceof KalturaHuluDistributionProfile))
			return;
			
		$this->xmlFileName = $distributionJobData->entryDistribution->entryId . '.xml';
		$this->metadataProfileId = $distributionJobData->distributionProfile->metadataProfileId;
		$this->distributionProfileId = $distributionJobData->distributionProfile->id;
		
		$mediaInfo = mediaInfoPeer::retrieveOriginalByEntryId($distributionJobData->entryDistribution->entryId);
		$this->frameRate = $mediaInfo->getVideoFrameRate();
		
//		TODO
//		$this->aspectRatio = KDLWrap::getAspectRation($mediaInfo->getVideoWidth(), $mediaInfo->getVideoHeight());
		
		if($distributionJobData instanceof KalturaDistributionSubmitJobData)
			$this->xml = HuluDistributionProvider::generateSubmitXML($distributionJobData->entryDistribution->entryId, $this);
			
		if($distributionJobData instanceof KalturaDistributionDeleteJobData)
			$this->xml = HuluDistributionProvider::generateDeleteXML($distributionJobData->entryDistribution->entryId, $this);
			
		if($distributionJobData instanceof KalturaDistributionUpdateJobData)
			$this->xml = HuluDistributionProvider::generateUpdateXML($distributionJobData->entryDistribution->entryId, $this);
	}
		
	private static $map_between_objects = array
	(
		"xml" ,
		"metadataProfileId" ,
		"distributionProfileId" ,
		"aspectRatio" ,
		"frameRate" ,
	);

	public function getMapBetweenObjects ( )
	{
		return array_merge ( parent::getMapBetweenObjects() , self::$map_between_objects );
	}
}
