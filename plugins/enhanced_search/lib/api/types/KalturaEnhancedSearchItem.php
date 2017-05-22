<?php
/**
 * @package plugins.enhancedSearch
 * @subpackage api.objects
 */
abstract class KalturaEnhancedSearchItem extends KalturaEnhancedSearchBaseItem {
	/**
	 * @var KalturaEnhancedSearchItemType
	 */
	public $searchType;


	private static $map_between_objects = array(
		'searchType',
	);

	protected function getMapBetweenObjects()
	{
		return array_merge(parent::getMapBetweenObjects(), self::$map_between_objects);
	}


}
