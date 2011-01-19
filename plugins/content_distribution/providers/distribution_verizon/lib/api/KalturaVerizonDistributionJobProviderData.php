<?php
class KalturaVerizonDistributionJobProviderData extends KalturaDistributionJobProviderData
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
	 * @var int
	 */
	public $distributionProfileId;

	
	public function __construct(KalturaDistributionJobData $distributionJobData = null)
	{
		if(!$distributionJobData)
			return;
			
		if(!($distributionJobData->distributionProfile instanceof KalturaVerizonDistributionProfile))
			return;
	
		$this->distributionProfileId = $distributionJobData->distributionProfile->id;
	
		$this->metadataProfileId = $distributionJobData->distributionProfile->metadataProfileId;
			
		if($distributionJobData instanceof KalturaDistributionSubmitJobData)
			$this->xml = VerizonDistributionProvider::generateSubmitXML($distributionJobData->entryDistribution->entryId, $this);
			
		if($distributionJobData instanceof KalturaDistributionDeleteJobData)
			$this->xml = VerizonDistributionProvider::generateDeleteXML($distributionJobData->entryDistribution->entryId, $this);
			
		if($distributionJobData instanceof KalturaDistributionUpdateJobData)
			$this->xml = VerizonDistributionProvider::generateUpdateXML($distributionJobData->entryDistribution->entryId, $this);
	}
		
	private static $map_between_objects = array
	(
		"xml" ,
		"metadataProfileId" ,
		"distributionProfileId" ,
	);

	public function getMapBetweenObjects ( )
	{
		return array_merge ( parent::getMapBetweenObjects() , self::$map_between_objects );
	}
}
