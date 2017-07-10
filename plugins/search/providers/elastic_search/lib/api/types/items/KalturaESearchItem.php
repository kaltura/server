<?php
/**
 * @package plugins.elasticSearch
 * @subpackage api.objects
 */
abstract class KalturaESearchItem extends KalturaESearchBaseItem {

	/**
	 * @var string
	 */
	public $searchTerm;

	/**
	 * @var KalturaESearchItemType
	 */
	public $itemType;

	/**
	 * @var KalturaESearchRange
	 */
	public $range;

	private static $map_between_objects = array(
		'searchTerm',
		'itemType',
		'range',
	);

	protected function getMapBetweenObjects()
	{
		return array_merge(parent::getMapBetweenObjects(), self::$map_between_objects);
	}

}
