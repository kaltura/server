<?php
/**
 * @package plugins.contentDistribution
 * @subpackage api.objects
 * @abstract
 */
abstract class KalturaConfigurableDistributionJobProviderData extends KalturaDistributionJobProviderData
{

	/**
	 * @var string serialized array of field values
	 */
	public $fieldValues;
	
	
	private static $map_between_objects = array
	(
	    "fieldValues",
	);
    
    
	public function __construct(KalturaDistributionJobData $distributionJobData = null)
	{
		parent::__construct($distributionJobData);
		
	    if(!$distributionJobData)
			return;
			
		if(!($distributionJobData->distributionProfile instanceof KalturaConfigurableDistributionProfile))
			return;
			
		$entryDistributionDb = EntryDistributionPeer::retrieveByPK($distributionJobData->entryDistributionId);
		$dbDistributionProfile = DistributionProfilePeer::retrieveByPK($distributionJobData->distributionProfile->id);
		if (!$entryDistributionDb) {
		    KalturaLog::err('Cannot get entry distribution id ['.$distributionJobData->entryDistributionId.']');
		    return;
		}
		if (!$dbDistributionProfile) {
		    KalturaLog::err('Cannot get distribution profile id ['.$distributionJobData->distributionProfile->id.']');
		    return;
		}
		
		$tempFieldValues = $dbDistributionProfile->getAllFieldValues($entryDistributionDb);
		if (!$tempFieldValues || !is_array($tempFieldValues)) {
		    KalturaLog::err('Error getting field values from entry distribution id ['.$entryDistributionDb->getId().'] profile id ['.$dbDistributionProfile->getId().']');
		    $tempFieldValues = array();
		}
		$this->fieldValues = serialize($tempFieldValues);
	}
	
	
}
