<?php

/**
 * Auto-generated index class for CuePoint
*/
class CuePointIndex extends BaseIndexObject
{
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
				'cue_point_type' => 'type',
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
			);
		}
		return self::$fieldsMap;
	}

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
				'int_cue_point_id' => IIndexable::FIELD_TYPE_INTEGER,
				'cue_point_int_id' => IIndexable::FIELD_TYPE_INTEGER,
				'partner_id' => IIndexable::FIELD_TYPE_INTEGER,
				'start_time' => IIndexable::FIELD_TYPE_INTEGER,
				'end_time' => IIndexable::FIELD_TYPE_INTEGER,
				'duration' => IIndexable::FIELD_TYPE_INTEGER,
				'cue_point_status' => IIndexable::FIELD_TYPE_INTEGER,
				'cue_point_type' => IIndexable::FIELD_TYPE_INTEGER,
				'sub_type' => IIndexable::FIELD_TYPE_INTEGER,
				'kuser_id' => IIndexable::FIELD_TYPE_INTEGER,
				'partner_sort_value' => IIndexable::FIELD_TYPE_INTEGER,
				'depth' => IIndexable::FIELD_TYPE_INTEGER,
				'children_count' => IIndexable::FIELD_TYPE_INTEGER,
				'direct_children_count' => IIndexable::FIELD_TYPE_INTEGER,
				'force_stop' => IIndexable::FIELD_TYPE_INTEGER,
				'created_at' => IIndexable::FIELD_TYPE_DATETIME,
				'updated_at' => IIndexable::FIELD_TYPE_DATETIME,
				'str_entry_id' => IIndexable::FIELD_TYPE_STRING,
				'str_cue_point_id' => IIndexable::FIELD_TYPE_STRING,
			);
		}
		return self::$typesMap;
	}

	public static function getIndexNullableList()
	{
		if (!self::$nullableFields)
		{
			self::$nullableFields = array(
			);
		}
		return self::$nullableFields;
	}

	public static function getIndexSearchableFieldsMap()
	{
		if (!self::$searchableFieldsMap)
		{
			self::$searchableFieldsMap = array(
				'CuePoint.PARENT_ID' => 'parent_id',
				'CuePoint.ENTRY_ID' => 'entry_id',
				'CuePoint.NAME' => 'name',
				'CuePoint.SYSTEM_NAME' => 'system_name',
				'CuePoint.TEXT' => 'text',
				'CuePoint.TAGS' => 'tags',
				'CuePoint.ROOTS' => 'roots',
				'CuePoint.ID' => 'int_cue_point_id',
				'CuePoint.INT_ID' => 'cue_point_int_id',
				'CuePoint.PARTNER_ID' => 'partner_id',
				'CuePoint.START_TIME' => 'start_time',
				'CuePoint.END_TIME' => 'end_time',
				'CuePoint.DURATION' => 'duration',
				'CuePoint.STATUS' => 'cue_point_status',
				'CuePoint.TYPE' => 'cue_point_type',
				'CuePoint.SUB_TYPE' => 'sub_type',
				'CuePoint.KUSER_ID' => 'kuser_id',
				'CuePoint.PARTNER_SORT_VALUE' => 'partner_sort_value',
				'CuePoint.DEPTH' => 'depth',
				'CuePoint.CHILDREN_COUNT' => 'children_count',
				'CuePoint.DIRECT_CHILDREN_COUNT' => 'direct_children_count',
				'CuePoint.FORCE_STOP' => 'force_stop',
				'CuePoint.CREATED_AT' => 'created_at',
				'CuePoint.UPDATED_AT' => 'updated_at',
				'CuePoint.STR_ENTRY_ID' => 'str_entry_id',
				'CuePoint.STR_CUE_POINT_ID' => 'str_cue_point_id',
			);
		}
		return self::$searchableFieldsMap;
	}

	public static function getSearchFieldsEscapeTypeList()
	{
		if (!self::$searchEscapeTypes)
		{
			self::$searchEscapeTypes = array(
			);
		}
		return self::$searchEscapeTypes;
	}

	public static function getIndexFieldsEscapeTypeList()
	{
		if (!self::$indexEscapeTypes)
		{
			self::$indexEscapeTypes = array(
			);
		}
		return self::$indexEscapeTypes;
	}

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

	public static function getIndexOrderList()
	{
		if (!self::$orderFields)
		{
			self::$orderFields = array(
				'CuePoint.START_TIME' => 'start_time',
				'CuePoint.END_TIME' => 'end_time',
				'CuePoint.DURATION' => 'duration',
				'CuePoint.PARTNER_SORT_VALUE' => 'partner_sort_value',
				'CuePoint.DEPTH' => 'depth',
				'CuePoint.CHILDREN_COUNT' => 'children_count',
				'CuePoint.DIRECT_CHILDREN_COUNT' => 'direct_children_count',
				'CuePoint.CREATED_AT' => 'created_at',
				'CuePoint.UPDATED_AT' => 'updated_at',
			);
		}
		return self::$orderFields;
	}

	public static function getIndexSkipFieldsList()
	{
		if (!self::$skipFields)
		{
			self::$skipFields = array(
				'CuePoint.ENTRY_ID',
				'CuePoint.ID',
			);
		}
		return self::$skipFields;
	}

	public static function getSphinxConditionsToKeep()
	{
		if (!self::$conditionToKeep)
		{
			self::$conditionToKeep = array(
			);
		}
		return self::$conditionToKeep;
	}

	public static function doCountOnPeer(Criteria $c)
	{
		return CuePointPeer::doCount($c);
	}

}

