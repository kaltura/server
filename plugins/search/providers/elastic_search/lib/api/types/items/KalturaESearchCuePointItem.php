<?php
/**
 * @package plugins.elasticSearch
 * @subpackage api.objects
 */
class KalturaESearchCuePointItem extends KalturaESearchItem
{

	/**
	 * @var KalturaESearchCuePointFieldName
	 */
	public $fieldName;

	/**
	 * @var KalturaCuePointType
	 */
	public $cuePointType;

	private static $map_between_objects = array(
		'fieldName',
		'cuePointType',
	);

	private static $map_dynamic_enum = array();

	protected function getMapBetweenObjects()
	{
		return array_merge(parent::getMapBetweenObjects(), self::$map_between_objects);
	}

	public function toObject($object_to_fill = null, $props_to_skip = array())
	{
		if (!$object_to_fill)
			$object_to_fill = new ESearchCuePointItem();

		return parent::toObject($object_to_fill, $props_to_skip);
	}

	protected function getItemFieldName()
	{
		return $this->fieldName;
	}

	protected function getDynamicEnumMap()
	{
		return self::$map_dynamic_enum;
	}

}
