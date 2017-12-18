<?php
/**
 * @package plugins.elasticSearch
 * @subpackage api.objects
 */
class KalturaESearchEntryParams extends KalturaESearchParams
{
	/**
	 * @var KalturaESearchEntryOperator
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
