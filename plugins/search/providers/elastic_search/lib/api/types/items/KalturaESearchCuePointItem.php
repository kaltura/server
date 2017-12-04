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

	private static $map_field_enum = array(
		KalturaESearchCuePointFieldName::ANSWERS => ESearchCuePointFieldName::ANSWERS,
		KalturaESearchCuePointFieldName::END_TIME => ESearchCuePointFieldName::END_TIME,
		KalturaESearchCuePointFieldName::EXPLANATION => ESearchCuePointFieldName::EXPLANATION,
		KalturaESearchCuePointFieldName::HINT => ESearchCuePointFieldName::HINT,
		KalturaESearchCuePointFieldName::ID => ESearchCuePointFieldName::ID,
		KalturaESearchCuePointFieldName::NAME => ESearchCuePointFieldName::NAME,
		KalturaESearchCuePointFieldName::QUESTION => ESearchCuePointFieldName::QUESTION,
		KalturaESearchCuePointFieldName::START_TIME => ESearchCuePointFieldName::START_TIME,
		KalturaESearchCuePointFieldName::TAGS => ESearchCuePointFieldName::TAGS,
		KalturaESearchCuePointFieldName::TEXT => ESearchCuePointFieldName::TEXT,
		KalturaESearchCuePointFieldName::SUB_TYPE => ESearchCuePointFieldName::SUB_TYPE,
	);

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

	protected function getFieldEnumMap()
	{
		return self::$map_field_enum;
	}

}
