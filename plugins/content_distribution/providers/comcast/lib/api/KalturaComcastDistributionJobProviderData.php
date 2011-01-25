<?php
/**
 * @package plugins.comcastDistribution
 * @subpackage api.objects
 */
class KalturaComcastDistributionJobProviderData extends KalturaDistributionJobProviderData
{
	private static $map_between_objects = array
	(
	);

	public function getMapBetweenObjects ( )
	{
		return array_merge ( parent::getMapBetweenObjects() , self::$map_between_objects );
	}
}
