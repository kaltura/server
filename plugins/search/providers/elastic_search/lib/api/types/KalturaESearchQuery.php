<?php
/**
 * @package plugins.elasticSearch
 * @subpackage api.objects
 */
class KalturaESearchQuery extends KalturaESearchObject
{
	/**
	 * @var string
	 */
	public $eSearchQuery;

	private static $mapBetweenObjects = array
	(
		"eSearchQuery",
	);

	public function getMapBetweenObjects()
	{
		return array_merge(parent::getMapBetweenObjects(), self::$mapBetweenObjects);
	}

}
