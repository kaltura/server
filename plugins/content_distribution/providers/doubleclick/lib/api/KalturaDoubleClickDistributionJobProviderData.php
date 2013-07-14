<?php
/**
* @package plugins.doubleClickDistribution
 * @subpackage api.objects
 */
class KalturaDoubleClickDistributionJobProviderData extends KalturaDistributionJobProviderData
{
	public function __construct(KalturaDistributionJobData $distributionJobData = null)
	{
	}

	private static $map_between_objects = array();

	public function getMapBetweenObjects()
	{
		return array_merge ( parent::getMapBetweenObjects() , self::$map_between_objects );
	}	
}
