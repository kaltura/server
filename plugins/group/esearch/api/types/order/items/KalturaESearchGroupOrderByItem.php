<?php
/**
 * @package plugins.elasticSearch
 * @subpackage api.objects
 */
class KalturaESearchGroupOrderByItem extends KalturaESearchOrderByItem
{
	/**
	 *  @var KalturaESearchGroupOrderByFieldName
	 */
	public $sortField;

	private static $map_between_objects = array(
		'sortField',
	);

	private static $map_field_enum = array(
		KalturaESearchGroupOrderByFieldName::CREATED_AT => ESearchGroupOrderByFieldName::CREATED_AT,
		KalturaESearchGroupOrderByFieldName::UPDATED_AT => ESearchGroupOrderByFieldName::UPDATED_AT,
		KalturaESearchGroupOrderByFieldName::SCREEN_NAME => ESearchGroupOrderByFieldName::SCREEN_NAME,
		KalturaESearchGroupOrderByFieldName::USER_ID => ESearchGroupOrderByFieldName::USER_ID,
        KalturaESearchGroupOrderByFieldName::FULL_NAME => ESearchGroupOrderByFieldName::FULL_NAME,
		KalturaESearchGroupOrderByFieldName::MEMBERS_COUNT => ESearchGroupOrderByFieldName::MEMBERS_COUNT,
	);

	public function getMapBetweenObjects()
	{
		return array_merge(parent::getMapBetweenObjects(), self::$map_between_objects);
	}

	public function toObject($object_to_fill = null, $props_to_skip = array())
	{
		if (!$object_to_fill)
			$object_to_fill = new ESearchGroupOrderByItem();
		return parent::toObject($object_to_fill, $props_to_skip);
	}

	public function getFieldEnumMap()
	{
		return self::$map_field_enum;
	}

	public function getItemFieldName()
	{
		return $this->sortField;
	}

}
