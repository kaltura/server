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
	public $eSerachQuery;

	private static $mapBetweenObjects = array
	(
		"eSerachQuery",
	);

	public function getMapBetweenObjects()
	{
		return array_merge(parent::getMapBetweenObjects(), self::$mapBetweenObjects);
	}

}
