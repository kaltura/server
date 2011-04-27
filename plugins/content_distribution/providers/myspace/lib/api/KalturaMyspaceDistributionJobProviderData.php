<?php
/**
 * @package plugins.myspaceDistribution
 * @subpackage api.objects
 */
class KalturaMyspaceDistributionJobProviderData extends KalturaDistributionJobProviderData
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
	public $myspFlavorAssetId;

	/**
	 * @var string
	 */
	public $feedTitle;

	/**
	 * @var string
	 */
	public $feedDescription;

	/**
	 * @var string
	 */
	public $feedContact;
	
	/**
	 * @var string
	 */
	public $deleteOp = '';
	
	public function __construct(KalturaDistributionJobData $distributionJobData = null)
	{
		if(!$distributionJobData)
			return;
			
		if(!($distributionJobData->distributionProfile instanceof KalturaMyspaceDistributionProfile))
			return;
	
		$this->distributionProfileId = $distributionJobData->distributionProfile->id;
		$this->metadataProfileId = $distributionJobData->distributionProfile->metadataProfileId;
		$this->feedTitle = $distributionJobData->distributionProfile->feedTitle;
		$this->feedDescription = $distributionJobData->distributionProfile->feedDescription;
		$this->feedContact = $distributionJobData->distributionProfile->feedContact;

		$myspFlavorAsset = flavorAssetPeer::retrieveByEntryIdAndFlavorParams($distributionJobData->entryDistribution->entryId, $distributionJobData->distributionProfile->myspFlavorParamsId);
		if($myspFlavorAsset)
			$this->myspFlavorAssetId = $myspFlavorAsset->getId();
		
		if($distributionJobData instanceof KalturaDistributionSubmitJobData)
			$this->xml = MyspaceDistributionProvider::generateSubmitXML($distributionJobData->entryDistribution->entryId, $this);
			
		if($distributionJobData instanceof KalturaDistributionDeleteJobData)
			$this->xml = MyspaceDistributionProvider::generateDeleteXML($distributionJobData->entryDistribution->entryId, $this);
			
		if($distributionJobData instanceof KalturaDistributionUpdateJobData)
			$this->xml = MyspaceDistributionProvider::generateUpdateXML($distributionJobData->entryDistribution->entryId, $this);
	}
		
	private static $map_between_objects = array
	(
		"xml" ,
		"myspFlavorAssetId" ,		
		"feedTitle",
		"feedDescription",
		"feedContact",
		"metadataProfileId" ,
		"distributionProfileId" ,
	);

	public function getMapBetweenObjects ( )
	{
		return array_merge ( parent::getMapBetweenObjects() , self::$map_between_objects );
	}
}
