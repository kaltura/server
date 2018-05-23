<?php

/**
 * Auto-generated index class for EntryVendorTask
*/
class EntryVendorTaskIndex extends BaseIndexObject
{
	const CATALOG_ITEM_DATA = "catalog_item_data";

	public static function getObjectName()
	{
		return 'entry_vendor_task';
	}

	public static function getObjectIndexName()
	{
		return 'entry_vendor_task';
	}

	public static function getSphinxIdField()
	{
		return 'int_id';
	}

	public static function getPropelIdField()
	{
		return EntryVendorTaskPeer::ID;
	}

	public static function getIdField()
	{
		return EntryVendorTaskPeer::ID;
	}

	public static function getDefaultCriteriaFilter()
	{
		return EntryVendorTaskPeer::getCriteriaFilter();
	}

	protected static $fieldsMap;

	public static function getIndexFieldsMap()
	{
		if (!self::$fieldsMap)
		{
			self::$fieldsMap = array(
				'int_id' => 'id',
				'created_at' => 'createdAt',
				'updated_at' => 'updatedAt',
				'queue_time' => 'queueTime',
				'finish_time' => 'finishTime',
				'partner_id' => 'partnerId',
				'vendor_partner_id' => 'vendorPartnerId',
				'entry_id' => 'entryId',
				'task_status' => 'status',
				'price' => 'price',
				'catalog_item_id' => 'catalogItemId',
				'reach_profile_id' => 'reachProfileId',
				'kuser_id' => 'kuserId',
				'user_id' => 'userId',
				'context' => 'context',
				'notes' => 'notes',
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
				'int_id' => IIndexable::FIELD_TYPE_INTEGER,
				'created_at' => IIndexable::FIELD_TYPE_DATETIME,
				'updated_at' => IIndexable::FIELD_TYPE_DATETIME,
				'queue_time' => IIndexable::FIELD_TYPE_DATETIME,
				'finish_time' => IIndexable::FIELD_TYPE_DATETIME,
				'partner_id' => IIndexable::FIELD_TYPE_INTEGER,
				'vendor_partner_id' => IIndexable::FIELD_TYPE_STRING,
				'entry_id' => IIndexable::FIELD_TYPE_STRING,
				'task_status' => IIndexable::FIELD_TYPE_UINT,
				'price' => IIndexable::FIELD_TYPE_UINT,
				'catalog_item_id' => IIndexable::FIELD_TYPE_STRING,
				'reach_profile_id' => IIndexable::FIELD_TYPE_STRING,
				'kuser_id' => IIndexable::FIELD_TYPE_STRING,
				'user_id' => IIndexable::FIELD_TYPE_STRING,
				'context' => IIndexable::FIELD_TYPE_STRING,
				'notes' => IIndexable::FIELD_TYPE_STRING,
				'catalog_item_data' => IIndexable::FIELD_TYPE_STRING,
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
				'entry_vendor_task.ID' => 'int_id',
				'entry_vendor_task.CREATED_AT' => 'created_at',
				'entry_vendor_task.UPDATED_AT' => 'updated_at',
				'entry_vendor_task.QUEUE_TIME' => 'queue_time',
				'entry_vendor_task.FINISH_TIME' => 'finish_time',
				'entry_vendor_task.PARTNER_ID' => 'partner_id',
				'entry_vendor_task.VENDOR_PARTNER_ID' => 'vendor_partner_id',
				'entry_vendor_task.ENTRY_ID' => 'entry_id',
				'entry_vendor_task.STATUS' => 'task_status',
				'entry_vendor_task.PRICE' => 'price',
				'entry_vendor_task.CATALOG_ITEM_ID' => 'catalog_item_id',
				'entry_vendor_task.REACH_PROFILE_ID' => 'reach_profile_id',
				'entry_vendor_task.KUSER_ID' => 'kuser_id',
				'entry_vendor_task.USER_ID' => 'user_id',
				'entry_vendor_task.CONTEXT' => 'context',
				'entry_vendor_task.NOTES' => 'notes',
				'entry_vendor_task.CATALOG_ITEM_DATA' => 'catalog_item_data',
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
				"vendor_partner_id",
				"catalog_item_id",
				"reach_profile_id",
				"kuser_id",
				"user_id",
				"context",
				"notes",
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
				'entry_vendor_task.ID' => 'int_id',
				'entry_vendor_task.CREATED_AT' => 'created_at',
				'entry_vendor_task.UPDATED_AT' => 'updated_at',
				'entry_vendor_task.QUEUE_TIME' => 'queue_time',
				'entry_vendor_task.FINISH_TIME' => 'finish_time',
				'entry_vendor_task.STATUS' => 'task_status',
				'entry_vendor_task.PRICE' => 'price',
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
				'entry_vendor_task.ID',
				'entry_vendor_task.ENTRY_ID',
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
				'entry_vendor_task.PARTNER_ID',
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
		return EntryVendorTaskPeer::doCount($c);
	}

}

