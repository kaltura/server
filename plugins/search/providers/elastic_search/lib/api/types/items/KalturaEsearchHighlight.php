<?php
/**
 * @package plugins.elasticSearch
 * @subpackage api.objects
 */
class KalturaEsearchHighlight extends KalturaObject
{
	/**
	 * @var string
	 */
	public $fieldName;

	/**
	 * @var KalturaStringArray
	 */
	public $hits;

	private static $map_between_objects = array(
		'fieldName',
		'hits',
	);

	protected function getMapBetweenObjects()
	{
		return array_merge(parent::getMapBetweenObjects(), self::$map_between_objects);
	}

	public function toObject($object_to_fill = null, $props_to_skip = array())
	{
		if (!$object_to_fill)
			$object_to_fill = new EsearchHighlight();

		return parent::toObject($object_to_fill, $props_to_skip);
	}
}