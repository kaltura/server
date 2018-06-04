<?php

/**
 * Auto-generated index class for CuePoint
*/
class CuePointIndex extends BaseIndexObject
{
	const PLUGINS_DATA = "plugins_data";

	public static function getObjectName()
	{
		return 'cue_point';
	}

	public static function getObjectIndexName()
	{
		return 'cue_point';
	}

	public static function getSphinxIdField()
	{
		return 'str_cue_point_id';
	}

	public static function getPropelIdField()
	{
		return CuePointPeer::ID;
	}

	public static function getIdField()
	{
		return CuePointPeer::ID;
	}

	public static function getDefaultCriteriaFilter()
	{
		return CuePointPeer::getCriteriaFilter();
	}

	protected static $fieldsMap;

	public static function getIndexFieldsMap()
	{
		if (!self::$fieldsMap)
		{
			self::$fieldsMap = array(
				'parent_id' => 'parentId',
				'entry_id' => 'entryId',
				'name' => 'name',
				'system_name' => 'systemName',
				'text' => 'text',
				'tags' => 'tags',
				'roots' => 'roots',
				'int_cue_point_id' => 'indexedId',
				'cue_point_int_id' => 'intId',
				'partner_id' => 'partnerId',
				'start_time' => 'startTime',
				'end_time' => 'endTime',
				'duration' => 'duration',
				'cue_point_status' => 'status',
				'cue_point_type' => 'typeStr',
				'sub_type' => 'subType',
				'kuser_id' => 'kuserId',
				'partner_sort_value' => 'partnerSortValue',
				'depth' => 'depth',
				'children_count' => 'childrenCount',
				'direct_children_count' => 'directChildrenCount',
				'force_stop' => 'forceStop',
				'created_at' => 'createdAt',
				'updated_at' => 'updatedAt',
				'str_entry_id' => 'entryId',
				'str_cue_point_id' => 'id',
				'is_public' => 'isPublicStr',
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
				'parent_id' => IIndexable::FIELD_TYPE_STRING,
				'entry_id' => IIndexable::FIELD_TYPE_STRING,
				'name' => IIndexable::FIELD_TYPE_STRING,
				'system_name' => IIndexable::FIELD_TYPE_STRING,
				'text' => IIndexable::FIELD_TYPE_STRING,
				'tags' => IIndexable::FIELD_TYPE_STRING,
				'roots' => IIndexable::FIELD_TYPE_STRING,
				'int_cue_point_id' => IIndexable::FIELD_TYPE_UINT,
				'cue_point_int_id' => IIndexable::FIELD_TYPE_UINT,
				'partner_id' => IIndexable::FIELD_TYPE_INTEGER,
				'start_time' => IIndexable::FIELD_TYPE_UINT,
				'end_time' => IIndexable::FIELD_TYPE_UINT,
				'duration' => IIndexable::FIELD_TYPE_UINT,
				'cue_point_status' => IIndexable::FIELD_TYPE_UINT,
				'cue_point_type' => IIndexable::FIELD_TYPE_STRING,
				'sub_type' => IIndexable::FIELD_TYPE_UINT,
				'kuser_id' => IIndexable::FIELD_TYPE_STRING,
				'partner_sort_value' => IIndexable::FIELD_TYPE_INTEGER,
				'depth' => IIndexable::FIELD_TYPE_UINT,
				'children_count' => IIndexable::FIELD_TYPE_UINT,
				'direct_children_count' => IIndexable::FIELD_TYPE_UINT,
				'force_stop' => IIndexable::FIELD_TYPE_UINT,
				'created_at' => IIndexable::FIELD_TYPE_DATETIME,
				'updated_at' => IIndexable::FIELD_TYPE_DATETIME,
				'str_entry_id' => IIndexable::FIELD_TYPE_STRING,
				'str_cue_point_id' => IIndexable::FIELD_TYPE_STRING,
				'is_public' => IIndexable::FIELD_TYPE_STRING,
				'plugins_data' => IIndexable::FIELD_TYPE_STRING,
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

	protected static $searchableFieldsMap;

	public static function getIndexSearchableFieldsMap()
	{
		if (!self::$searchableFieldsMap)
		{
			self::$searchableFieldsMap = array(
				'cue_point.PARENT_ID' => 'parent_id',
				'cue_point.ENTRY_ID' => 'entry_id',
				'cue_point.NAME' => 'name',
				'cue_point.SYSTEM_NAME' => 'system_name',
				'cue_point.TEXT' => 'text',
				'cue_point.TAGS' => 'tags',
				'cue_point.ROOTS' => 'roots',
				'cue_point.ID' => 'int_cue_point_id',
				'cue_point.INT_ID' => 'cue_point_int_id',
				'cue_point.PARTNER_ID' => 'partner_id',
				'cue_point.START_TIME' => 'start_time',
				'cue_point.END_TIME' => 'end_time',
				'cue_point.DURATION' => 'duration',
				'cue_point.STATUS' => 'cue_point_status',
				'cue_point.TYPE' => 'cue_point_type',
				'cue_point.SUB_TYPE' => 'sub_type',
				'cue_point.KUSER_ID' => 'kuser_id',
				'cue_point.PARTNER_SORT_VALUE' => 'partner_sort_value',
				'cue_point.DEPTH' => 'depth',
				'cue_point.CHILDREN_COUNT' => 'children_count',
				'cue_point.DIRECT_CHILDREN_COUNT' => 'direct_children_count',
				'cue_point.FORCE_STOP' => 'force_stop',
				'cue_point.CREATED_AT' => 'created_at',
				'cue_point.UPDATED_AT' => 'updated_at',
				'cue_point.STR_ENTRY_ID' => 'str_entry_id',
				'cue_point.STR_CUE_POINT_ID' => 'str_cue_point_id',
				'cue_point.IS_PUBLIC' => 'is_public',
				'cue_point.PLUGINS_DATA' => 'plugins_data',
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
				"name",
				"system_name",
				"text",
				"tags",
				"roots",
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
				'cue_point.START_TIME' => 'start_time',
				'cue_point.END_TIME' => 'end_time',
				'cue_point.DURATION' => 'duration',
				'cue_point.PARTNER_SORT_VALUE' => 'partner_sort_value',
				'cue_point.DEPTH' => 'depth',
				'cue_point.CHILDREN_COUNT' => 'children_count',
				'cue_point.DIRECT_CHILDREN_COUNT' => 'direct_children_count',
				'cue_point.CREATED_AT' => 'created_at',
				'cue_point.UPDATED_AT' => 'updated_at',
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
				'cue_point.ENTRY_ID',
				'cue_point.ID',
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
				'cue_point.ENTRY_ID',
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

	//This function is generated based on index elements in the relevant IndexSchema.xml
	public static function getSphinxOptimizationMap()
	{
		return array(
		);
	}

	//This function is generated based on index elements in the relevant IndexSchema.xml
	public static function getSphinxOptimizationValues()
	{
		return array(
		);
	}

	public static function doCountOnPeer(Criteria $c)
	{
		return CuePointPeer::doCount($c);
	}

	//This function is generated based on cacheInvalidationKey elements in the relevant IndexSchema.xml
	public static function getCacheInvalidationKeys($object = null)
	{
		if (is_null($object))
			return array(array("cue_point:id=%s", CuePointPeer::ID), array("cue_point:entryId=%s", CuePointPeer::ENTRY_ID), array("cue_point:partnerId=%s", CuePointPeer::PARTNER_ID));
		else
			return array("cue_point:id=".strtolower($object->getId()), "cue_point:entryId=".strtolower($object->getEntryId()), "cue_point:partnerId=".strtolower($object->getPartnerId()));
	}

}

