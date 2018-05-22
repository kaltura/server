<?php

/**
 * Auto-generated index class for ScheduleEvent
 */
class ScheduleEventIndex extends BaseIndexObject
{
	const PLUGINS_DATA = "plugins_data";

	public static function getObjectName()
	{
		return 'schedule_event';
	}

	public static function getObjectIndexName()
	{
		return 'schedule_event';
	}

	public static function getSphinxIdField()
	{
		return 'int_id';
	}

	public static function getPropelIdField()
	{
		return ScheduleEventPeer::ID;
	}

	public static function getIdField()
	{
		return ScheduleEventPeer::ID;
	}

	public static function getDefaultCriteriaFilter()
	{
		return ScheduleEventPeer::getCriteriaFilter();
	}

	protected static $fieldsMap;

	public static function getIndexFieldsMap()
	{
		if (!self::$fieldsMap)
		{
			self::$fieldsMap = array(
				'int_id' => 'id',
				'parent_id' => 'parentId',
				'partner_id' => 'partnerId',
				'summary' => 'summary',
				'description' => 'description',
				'schedule_event_type' => 'type',
				'schedule_event_status' => 'status',
				'original_start_date' => 'originalStartDate',
				'start_date' => 'startDate',
				'end_date' => 'endDate',
				'reference_id' => 'referenceId',
				'classification_type' => 'classificationType',
				'location' => 'location',
				'organizer' => 'organizer',
				'owner_kuser_id' => 'ownerKuserId',
				'priority' => 'priority',
				'sequence' => 'sequence',
				'recurrence_type' => 'recurrenceType',
				'duration' => 'duration',
				'contact' => 'contact',
				'comment' => 'comment',
				'tags' => 'tags',
				'created_at' => 'createdAt',
				'updated_at' => 'updatedAt',
				'entry_ids' => 'entryIds',
				'category_ids' => 'categoryIdsForIndex',
				'resource_ids' => 'resourceIdsForIndex',
				'template_entry_categories_ids' => 'templateEntryCategoriesIdsForIndex',
				'resource_system_names' => 'resourceSystemNamesForIndex',
				'template_entry_id' => 'templateEntryId',
			);
		}
		return self::$fieldsMap;
	}

	protected static $typesMap;

	public static function getIndexFieldTypesMap()
	{
		if (!self::$typesMap)
		{
			self::$typesMap = array(
				'int_id' => IIndexable::FIELD_TYPE_UINT,
				'parent_id' => IIndexable::FIELD_TYPE_STRING,
				'partner_id' => IIndexable::FIELD_TYPE_INTEGER,
				'summary' => IIndexable::FIELD_TYPE_STRING,
				'description' => IIndexable::FIELD_TYPE_STRING,
				'schedule_event_type' => IIndexable::FIELD_TYPE_UINT,
				'schedule_event_status' => IIndexable::FIELD_TYPE_UINT,
				'original_start_date' => IIndexable::FIELD_TYPE_DATETIME,
				'start_date' => IIndexable::FIELD_TYPE_DATETIME,
				'end_date' => IIndexable::FIELD_TYPE_DATETIME,
				'reference_id' => IIndexable::FIELD_TYPE_STRING,
				'classification_type' => IIndexable::FIELD_TYPE_UINT,
				'location' => IIndexable::FIELD_TYPE_STRING,
				'organizer' => IIndexable::FIELD_TYPE_STRING,
				'owner_kuser_id' => IIndexable::FIELD_TYPE_UINT,
				'priority' => IIndexable::FIELD_TYPE_UINT,
				'sequence' => IIndexable::FIELD_TYPE_UINT,
				'recurrence_type' => IIndexable::FIELD_TYPE_UINT,
				'duration' => IIndexable::FIELD_TYPE_UINT,
				'contact' => IIndexable::FIELD_TYPE_STRING,
				'comment' => IIndexable::FIELD_TYPE_STRING,
				'tags' => IIndexable::FIELD_TYPE_STRING,
				'created_at' => IIndexable::FIELD_TYPE_DATETIME,
				'updated_at' => IIndexable::FIELD_TYPE_DATETIME,
				'entry_ids' => IIndexable::FIELD_TYPE_STRING,
				'category_ids' => IIndexable::FIELD_TYPE_STRING,
				'resource_ids' => IIndexable::FIELD_TYPE_STRING,
				'plugins_data' => IIndexable::FIELD_TYPE_STRING,
				'template_entry_categories_ids' => IIndexable::FIELD_TYPE_STRING,
				'resource_system_names' => IIndexable::FIELD_TYPE_STRING,
				'template_entry_id' => IIndexable::FIELD_TYPE_STRING,
			);
		}
		return self::$typesMap;
	}

	protected static $nullableFields;

	public static function getIndexNullableList()
	{
		if (!self::$nullableFields)
		{
			self::$nullableFields = array(
			);
		}
		return self::$nullableFields;
	}

	protected static $enrichableFields;

	public static function getIndexEnrichableList()
	{
		if (!self::$enrichableFields)
		{
			self::$enrichableFields = array(
			);
		}
		return self::$enrichableFields;
	}

	protected static $searchableFieldsMap;

	public static function getIndexSearchableFieldsMap()
	{
		if (!self::$searchableFieldsMap)
		{
			self::$searchableFieldsMap = array(
				'schedule_event.ID' => 'int_id',
				'schedule_event.PARENT_ID' => 'parent_id',
				'schedule_event.PARTNER_ID' => 'partner_id',
				'schedule_event.SUMMARY' => 'summary',
				'schedule_event.DESCRIPTION' => 'description',
				'schedule_event.TYPE' => 'schedule_event_type',
				'schedule_event.STATUS' => 'schedule_event_status',
				'schedule_event.ORIGINAL_START_DATE' => 'original_start_date',
				'schedule_event.START_DATE' => 'start_date',
				'schedule_event.END_DATE' => 'end_date',
				'schedule_event.REFERENCE_ID' => 'reference_id',
				'schedule_event.CLASSIFICATION_TYPE' => 'classification_type',
				'schedule_event.LOCATION' => 'location',
				'schedule_event.ORGANIZER' => 'organizer',
				'schedule_event.OWNER_KUSER_ID' => 'owner_kuser_id',
				'schedule_event.PRIORITY' => 'priority',
				'schedule_event.SEQUENCE' => 'sequence',
				'schedule_event.RECURRENCE_TYPE' => 'recurrence_type',
				'schedule_event.DURATION' => 'duration',
				'schedule_event.CONTACT' => 'contact',
				'schedule_event.COMMENT' => 'comment',
				'schedule_event.TAGS' => 'tags',
				'schedule_event.CREATED_AT' => 'created_at',
				'schedule_event.UPDATED_AT' => 'updated_at',
				'schedule_event.ENTRY_IDS' => 'entry_ids',
				'schedule_event.CATEGORY_IDS' => 'category_ids',
				'schedule_event.RESOURCE_IDS' => 'resource_ids',
				'schedule_event.PLUGINS_DATA' => 'plugins_data',
				'schedule_event.TEMPLATE_ENTRY_CATEGORIES_IDS' => 'template_entry_categories_ids',
				'schedule_event.RESOURCE_SYSTEM_NAMES' => 'resource_system_names',
				'schedule_event.TEMPLATE_ENTRY_ID' => 'template_entry_id',
			);
		}
		return self::$searchableFieldsMap;
	}

	protected static $searchEscapeTypes;

	public static function getSearchFieldsEscapeTypeList()
	{
		if (!self::$searchEscapeTypes)
		{
			self::$searchEscapeTypes = array(
			);
		}
		return self::$searchEscapeTypes;
	}

	protected static $indexEscapeTypes;

	public static function getIndexFieldsEscapeTypeList()
	{
		if (!self::$indexEscapeTypes)
		{
			self::$indexEscapeTypes = array(
			);
		}
		return self::$indexEscapeTypes;
	}

	protected static $matchableFields;

	public static function getIndexMatchableList()
	{
		if (!self::$matchableFields)
		{
			self::$matchableFields = array(
				"parent_id",
				"summary",
				"description",
				"reference_id",
				"location",
				"contact",
				"comment",
				"tags",
				"entry_ids",
				"category_ids",
				"resource_ids",
				"template_entry_categories_ids",
				"resource_system_names",
				"template_entry_id",
			);
		}
		return self::$matchableFields;
	}

	protected static $orderFields;

	public static function getIndexOrderList()
	{
		if (!self::$orderFields)
		{
			self::$orderFields = array(
				'schedule_event.SUMMARY' => 'summary',
				'schedule_event.ORIGINAL_START_DATE' => 'original_start_date',
				'schedule_event.START_DATE' => 'start_date',
				'schedule_event.END_DATE' => 'end_date',
				'schedule_event.ORGANIZER' => 'organizer',
				'schedule_event.OWNER_KUSER_ID' => 'owner_kuser_id',
				'schedule_event.PRIORITY' => 'priority',
				'schedule_event.SEQUENCE' => 'sequence',
				'schedule_event.RECURRENCE_TYPE' => 'recurrence_type',
				'schedule_event.DURATION' => 'duration',
				'schedule_event.CREATED_AT' => 'created_at',
				'schedule_event.UPDATED_AT' => 'updated_at',
			);
		}
		return self::$orderFields;
	}

	protected static $skipFields;

	public static function getIndexSkipFieldsList()
	{
		if (!self::$skipFields)
		{
			self::$skipFields = array(
				'schedule_event.ID',
			);
		}
		return self::$skipFields;
	}

	protected static $conditionToKeep;

	public static function getSphinxConditionsToKeep()
	{
		if (!self::$conditionToKeep)
		{
			self::$conditionToKeep = array(
			);
		}
		return self::$conditionToKeep;
	}

	protected static $apiCompareAttributesMap;

	public static function getApiCompareAttributesMap()
	{
		if (!self::$apiCompareAttributesMap)
		{
			self::$apiCompareAttributesMap = array(
			);
		}
		return self::$apiCompareAttributesMap;
	}

	protected static $apiMatchAttributesMap;

	public static function getApiMatchAttributesMap()
	{
		if (!self::$apiMatchAttributesMap)
		{
			self::$apiMatchAttributesMap = array(
			);
		}
		return self::$apiMatchAttributesMap;
	}

	public static function getSphinxOptimizationMap()
	{
		return array(
		);
	}

	public static function getSphinxOptimizationValues()
	{
		return array(
		);
	}

	public static function doCountOnPeer(Criteria $c)
	{
		return ScheduleEventPeer::doCount($c);
	}

}

