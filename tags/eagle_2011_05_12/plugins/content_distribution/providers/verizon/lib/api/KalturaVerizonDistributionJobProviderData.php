<?php
/**
 * @package plugins.verizonDistribution
 * @subpackage api.objects
 */
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

	/**
	 * @var string
	 */
	public $vrzFlavorAssetId;
	
	/**
	 * @var string
	 */
	public $deleteOp = '';

	/**
	 * @var string
	 */
	public $providerName;

	/**
	 * @var string
	 */
	public $providerId;
	
	public function __construct(KalturaDistributionJobData $distributionJobData = null)
	{
		if(!$distributionJobData)
			return;
			
		if(!($distributionJobData->distributionProfile instanceof KalturaVerizonDistributionProfile))
			return;
	
		$this->distributionProfileId = $distributionJobData->distributionProfile->id;
	
		$this->metadataProfileId = $distributionJobData->distributionProfile->metadataProfileId;
		$this->providerName = $distributionJobData->distributionProfile->providerName;
		$this->providerId = $distributionJobData->distributionProfile->providerId;
	
		$vrzFlavorAsset = flavorAssetPeer::retrieveByEntryIdAndFlavorParams($distributionJobData->entryDistribution->entryId, $distributionJobData->distributionProfile->vrzFlavorParamsId);
		if($vrzFlavorAsset)
			$this->vrzFlavorAssetId = $vrzFlavorAsset->getId();
	
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
		"vrzFlavorAssetId" ,		
		"providerName" ,		
		"providerId" ,		
		"metadataProfileId" ,
		"distributionProfileId" ,
	);

	public function getMapBetweenObjects ( )
	{
		return array_merge ( parent::getMapBetweenObjects() , self::$map_between_objects );
	}
}
