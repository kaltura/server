<?php
/**
 * @package plugins.elasticSearch
 * @subpackage api.objects
 */
class KalturaESearchUserParams extends KalturaESearchParams
{
	/**
	 * @var KalturaESearchUserOperator
	 */
	public $searchOperator;
	
	private static $mapBetweenObjects = array
	(
		"searchOperator",
	);
	
	public function getMapBetweenObjects()
	{
		return array_merge(parent::getMapBetweenObjects(), self::$mapBetweenObjects);
	}

}
