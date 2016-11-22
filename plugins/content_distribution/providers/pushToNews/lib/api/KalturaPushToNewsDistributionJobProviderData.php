<?php
/**
 * @package plugins.pushToNewsDistribution
 * @subpackage api.objects
 */
class KalturaPushToNewsDistributionJobProviderData extends KalturaConfigurableDistributionJobProviderData
{
	/**
	 * @var KalturaPushToNewsDistributionObjectsArray
	 */
	public $objectsForDistribution;

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
			
		if(!($distributionJobData->distributionProfile instanceof KalturaPushToNewsDistributionProfile))
			return;
			
		$entryDistributionDb = EntryDistributionPeer::retrieveByPK($distributionJobData->entryDistributionId);
		$distributionProfileDb = DistributionProfilePeer::retrieveByPK($distributionJobData->distributionProfileId);
		
		if (is_null($entryDistributionDb))
			return KalturaLog::err('Entry distribution #'.$distributionJobData->entryDistributionId.' not found');
		
		if (is_null($distributionProfileDb))
			return KalturaLog::err('Distribution profile #'.$distributionJobData->distributionProfileId.' not found');

		if (!$distributionProfileDb instanceof PushToNewsDistributionProfile)
			return KalturaLog::err('Distribution profile #'.$distributionJobData->distributionProfileId.' is not instance of Push-To-News DistributionProfile');

		$this->objectsForDistribution = $this->getDistributionObjects($distributionProfileDb, $entryDistributionDb);
		KalturaLog::log("Objects for distribution: ".print_r($this->objectsForDistribution, true));
	}
		
	/**
	 * Maps the object attributes to getters and setters for Core-to-API translation and back
	 *  
	 * @var array
	 */
	private static $map_between_objects = array
	(
		'objectsForDistribution',
	);

	/* (non-PHPdoc)
	 * @see KalturaObject::getMapBetweenObjects()
	 */
	public function getMapBetweenObjects ( )
	{
		return array_merge ( parent::getMapBetweenObjects() , self::$map_between_objects );
	}
	
	protected function getDistributionObjects(PushToNewsDistributionProfile $distributionProfileDb, EntryDistribution $entryDistributionDb)
	{
		$pushToNewsObjects = new KalturaPushToNewsDistributionObjectsArray();
		
		$metadataObject = new KalturaPushToNewsDistributionObject();
		$metadata = $distributionProfileDb->getMetadata($entryDistributionDb);
		$metadataObject->type = 'metadata';
		$metadataObject->contents = $metadata;
		$pushToNewsObjects[] = $metadataObject;
				
		return $pushToNewsObjects;
	}
}
