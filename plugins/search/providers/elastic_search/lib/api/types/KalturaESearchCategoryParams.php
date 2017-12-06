<?php
/**
 * @package plugins.elasticSearch
 * @subpackage api.objects
 */
class KalturaESearchCategoryParams extends KalturaESearchParams
{
	/**
	 * @var KalturaESearchCategoryOperator
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
