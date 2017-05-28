<?php
/**
 * @package plugins.elasticSearch
 * @subpackage api.objects
 */
abstract class KalturaESearchItem extends KalturaESearchBaseItem {
	/**
	 * @var KalturaESearchItemType
	 */
	public $itemType;


	private static $map_between_objects = array(
		'itemType',
	);

	protected function getMapBetweenObjects()
	{
		return array_merge(parent::getMapBetweenObjects(), self::$map_between_objects);
	}


}
