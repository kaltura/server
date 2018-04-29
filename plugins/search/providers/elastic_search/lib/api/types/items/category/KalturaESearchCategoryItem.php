<?php
/**
 * @package plugins.elasticSearch
 * @subpackage api.objects
 */
class KalturaESearchCategoryItem extends KalturaESearchAbstractCategoryItem
{

	/**
	 * @var KalturaESearchCategoryFieldName
	 */
	public $fieldName;

	private static $map_between_objects = array(
		'fieldName'
	);

	private static $map_dynamic_enum = array();

	private static $map_field_enum = array(
		KalturaESearchCategoryFieldName::ID => ESearchCategoryFieldName::ID,
		KalturaESearchCategoryFieldName::PRIVACY => ESearchCategoryFieldName::PRIVACY,
		KalturaESearchCategoryFieldName::PRIVACY_CONTEXT => ESearchCategoryFieldName::PRIVACY_CONTEXT,
		KalturaESearchCategoryFieldName::PRIVACY_CONTEXTS => ESearchCategoryFieldName::PRIVACY_CONTEXTS,
		KalturaESearchCategoryFieldName::PARENT_ID => ESearchCategoryFieldName::PARENT_ID,
		KalturaESearchCategoryFieldName::DEPTH => ESearchCategoryFieldName::DEPTH,
		KalturaESearchCategoryFieldName::NAME => ESearchCategoryFieldName::NAME,
		KalturaESearchCategoryFieldName::FULL_NAME => ESearchCategoryFieldName::FULL_NAME,
		KalturaESearchCategoryFieldName::FULL_IDS => ESearchCategoryFieldName::FULL_IDS,
		KalturaESearchCategoryFieldName::DESCRIPTION => ESearchCategoryFieldName::DESCRIPTION,
		KalturaESearchCategoryFieldName::TAGS => ESearchCategoryFieldName::TAGS,
		KalturaESearchCategoryFieldName::DISPLAY_IN_SEARCH => ESearchCategoryFieldName::DISPLAY_IN_SEARCH,
		KalturaESearchCategoryFieldName::INHERITANCE_TYPE => ESearchCategoryFieldName::INHERITANCE_TYPE,
		KalturaESearchCategoryFieldName::USER_ID => ESearchCategoryFieldName::KUSER_ID,
		KalturaESearchCategoryFieldName::REFERENCE_ID => ESearchCategoryFieldName::REFERENCE_ID,
		KalturaESearchCategoryFieldName::INHERITED_PARENT_ID => ESearchCategoryFieldName::INHERITED_PARENT_ID,
		KalturaESearchCategoryFieldName::MODERATION => ESearchCategoryFieldName::MODERATION,
		KalturaESearchCategoryFieldName::CONTRIBUTION_POLICY => ESearchCategoryFieldName::CONTRIBUTION_POLICY,
		KalturaESearchCategoryFieldName::ENTRIES_COUNT => ESearchCategoryFieldName::ENTRIES_COUNT,
		KalturaESearchCategoryFieldName::DIRECT_ENTRIES_COUNT => ESearchCategoryFieldName::DIRECT_ENTRIES_COUNT,
		KalturaESearchCategoryFieldName::DIRECT_SUB_CATEGORIES_COUNT => ESearchCategoryFieldName::DIRECT_SUB_CATEGORIES_COUNT,
		KalturaESearchCategoryFieldName::MEMBERS_COUNT => ESearchCategoryFieldName::MEMBERS_COUNT,
		KalturaESearchCategoryFieldName::PENDING_MEMBERS_COUNT => ESearchCategoryFieldName::PENDING_MEMBERS_COUNT,
		KalturaESearchCategoryFieldName::PENDING_ENTRIES_COUNT => ESearchCategoryFieldName::PENDING_ENTRIES_COUNT,
		KalturaESearchCategoryFieldName::CREATED_AT => ESearchCategoryFieldName::CREATED_AT,
		KalturaESearchCategoryFieldName::UPDATED_AT => ESearchCategoryFieldName::UPDATED_AT,
	);

	protected function getMapBetweenObjects()
	{
		return array_merge(parent::getMapBetweenObjects(), self::$map_between_objects);
	}

	public function toObject($object_to_fill = null, $props_to_skip = array())
	{
		if (!$object_to_fill)
			$object_to_fill = new ESearchCategoryItem();
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
