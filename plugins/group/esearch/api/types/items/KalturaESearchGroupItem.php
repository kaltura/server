<?php
/**
 * @package plugins.group
 * @subpackage api.objects
 */
class KalturaESearchGroupItem extends KalturaESearchAbstractGroupItem
{
	/**
	 * @var KalturaESearchGroupFieldName
	 */
	public $fieldName;

	private static $map_between_objects = array(
		'fieldName'
	);

	private static $map_dynamic_enum = array();

	private static $map_field_enum = array(
		KalturaESearchGroupFieldName::SCREEN_NAME => ESearchUserFieldName::SCREEN_NAME,
		KalturaESearchGroupFieldName::EMAIL => ESearchUserFieldName::EMAIL,
		KalturaESearchGroupFieldName::TAGS => ESearchUserFieldName::TAGS,
		KalturaESearchGroupFieldName::UPDATED_AT => ESearchUserFieldName::UPDATED_AT,
		KalturaESearchGroupFieldName::CREATED_AT => ESearchUserFieldName::CREATED_AT,
		KalturaESearchGroupFieldName::LAST_NAME => ESearchUserFieldName::LAST_NAME,
		KalturaESearchGroupFieldName::FIRST_NAME => ESearchUserFieldName::FIRST_NAME,
		KalturaESearchGroupFieldName::PERMISSION_NAMES => ESearchUserFieldName::PERMISSION_NAMES,
		KalturaESearchGroupFieldName::GROUP_IDS => ESearchUserFieldName::GROUP_IDS,
		KalturaESearchGroupFieldName::ROLE_IDS => ESearchUserFieldName::ROLE_IDS,
		KalturaESearchGroupFieldName::USER_ID => ESearchUserFieldName::PUSER_ID,
		KalturaESearchGroupFieldName::CAPABILITIES => ESearchUserFieldName::CAPABILITIES,
	);

	protected function getMapBetweenObjects()
	{
		return array_merge(parent::getMapBetweenObjects(), self::$map_between_objects);
	}

	public function toObject($object_to_fill = null, $props_to_skip = array())
	{
		if (!$object_to_fill)
			$object_to_fill = new ESearchUserItem();
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