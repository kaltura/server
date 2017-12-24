<?php
/**
 * @package plugins.elasticSearch
 * @subpackage api.objects
 */
class KalturaESearchCategoryEntryItem extends KalturaESearchAbstractEntryItem
{
	/**
	 * @var KalturaESearchCategoryEntryFieldName
	 */
	public $fieldName;

	/**
	 * @var KalturaCategoryEntryStatus
	 */
	public $categoryEntryStatus;

	private static $map_between_objects = array(
		'fieldName',
		'categoryEntryStatus',
	);

	private static $map_dynamic_enum = array();

	private static $map_field_enum = array(
		KalturaESearchCategoryEntryFieldName::ID => ESearchCategoryEntryFieldName::ID,
		KalturaESearchCategoryEntryFieldName::FULL_IDS => ESearchCategoryEntryFieldName::FULL_IDS,
		KalturaESearchCategoryEntryFieldName::NAME => ESearchCategoryEntryFieldName::NAME,
		KalturaESearchCategoryEntryFieldName::ANCESTOR_ID => ESearchCategoryEntryFieldName::ANCESTOR_ID,
		KalturaESearchCategoryEntryFieldName::ANCESTOR_NAME => ESearchCategoryEntryFieldName::ANCESTOR_NAME,
	);

	protected function getMapBetweenObjects()
	{
		return array_merge(parent::getMapBetweenObjects(), self::$map_between_objects);
	}

	public function toObject($object_to_fill = null, $props_to_skip = array())
	{
		if (!$object_to_fill)
			$object_to_fill = ESearchCategoryEntryItemFactory::getCoreItemByFieldName($this->fieldName);

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
