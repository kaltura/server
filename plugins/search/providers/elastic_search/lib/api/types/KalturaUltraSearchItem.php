<?php
/**
 * @package plugins.elasticSearch
 * @subpackage api.objects
 */
abstract class KalturaUltraSearchItem extends KalturaUltraSearchBaseItem {
	/**
	 * @var KalturaUltraSearchItemType
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
