<?php

/**
 * Auto-generated index class for EntryDistribution
*/
class EntryDistributionIndex extends BaseIndexObject
{
	const NEXT_REPORT = "next_report";

	const STR_ENTRY_ID = "str_entry_id";

	const INT_ENTRY_ID = "int_entry_id";

	public static function getObjectName()
	{
		return 'entry_distribution';
	}

	public static function getObjectIndexName()
	{
		return 'entry_distribution';
	}

	public static function getSphinxIdField()
	{
		return 'entry_distribution_id';
	}

	public static function getPropelIdField()
	{
		return EntryDistributionPeer::ID;
	}

	public static function getIdField()
	{
		return null;
	}

	public static function getDefaultCriteriaFilter()
	{
		return EntryDistributionPeer::getCriteriaFilter();
	}

	protected static $fieldsMap;

	public static function getIndexFieldsMap()
	{
		if (!self::$fieldsMap)
		{
			self::$fieldsMap = array(
				'entry_distribution_id' => 'id',
				'created_at' => 'createdAt',
				'updated_at' => 'updatedAt',
				'submitted_at' => 'submittedAt',
				'entry_id' => 'entryId',
				'partner_id' => 'partnerId',
				'distribution_profile_id' => 'distributionProfileId',
				'entry_distribution_status' => 'status',
				'dirty_status' => 'dirtyStatus',
				'thumb_asset_ids' => 'thumbAssetIds',
				'flavor_asset_ids' => 'flavorAssetIds',
				'asset_ids' => 'assetIds',
				'sunrise' => 'sunrise',
				'sunset' => 'sunset',
				'sun_status' => 'sunStatus',
				'remote_id' => 'remoteId',
				'plays' => 'plays',
				'views' => 'views',
				'error_type' => 'errorType',
				'error_number' => 'errorNumber',
				'last_report' => 'lastReport',
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
				'entry_distribution_id' => IIndexable::FIELD_TYPE_UINT,
				'created_at' => IIndexable::FIELD_TYPE_DATETIME,
				'updated_at' => IIndexable::FIELD_TYPE_DATETIME,
				'submitted_at' => IIndexable::FIELD_TYPE_DATETIME,
				'entry_id' => IIndexable::FIELD_TYPE_STRING,
				'partner_id' => IIndexable::FIELD_TYPE_INTEGER,
				'distribution_profile_id' => IIndexable::FIELD_TYPE_UINT,
				'entry_distribution_status' => IIndexable::FIELD_TYPE_UINT,
				'dirty_status' => IIndexable::FIELD_TYPE_UINT,
				'thumb_asset_ids' => IIndexable::FIELD_TYPE_STRING,
				'flavor_asset_ids' => IIndexable::FIELD_TYPE_STRING,
				'asset_ids' => IIndexable::FIELD_TYPE_STRING,
				'sunrise' => IIndexable::FIELD_TYPE_DATETIME,
				'sunset' => IIndexable::FIELD_TYPE_DATETIME,
				'sun_status' => IIndexable::FIELD_TYPE_UINT,
				'remote_id' => IIndexable::FIELD_TYPE_STRING,
				'plays' => IIndexable::FIELD_TYPE_UINT,
				'views' => IIndexable::FIELD_TYPE_UINT,
				'error_type' => IIndexable::FIELD_TYPE_UINT,
				'error_number' => IIndexable::FIELD_TYPE_INTEGER,
				'last_report' => IIndexable::FIELD_TYPE_DATETIME,
				'next_report' => IIndexable::FIELD_TYPE_DATETIME,
				'str_entry_id' => IIndexable::FIELD_TYPE_STRING,
				'int_entry_id' => IIndexable::FIELD_TYPE_UINT,
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
				'entry_distribution.ID' => 'entry_distribution_id',
				'entry_distribution.CREATED_AT' => 'created_at',
				'entry_distribution.UPDATED_AT' => 'updated_at',
				'entry_distribution.SUBMITTED_AT' => 'submitted_at',
				'entry_distribution.ENTRY_ID' => 'entry_id',
				'entry_distribution.PARTNER_ID' => 'partner_id',
				'entry_distribution.DISTRIBUTION_PROFILE_ID' => 'distribution_profile_id',
				'entry_distribution.STATUS' => 'entry_distribution_status',
				'entry_distribution.DIRTY_STATUS' => 'dirty_status',
				'entry_distribution.THUMB_ASSET_IDS' => 'thumb_asset_ids',
				'entry_distribution.FLAVOR_ASSET_IDS' => 'flavor_asset_ids',
				'entry_distribution.ASSET_IDS' => 'asset_ids',
				'entry_distribution.SUNRISE' => 'sunrise',
				'entry_distribution.SUNSET' => 'sunset',
				'entry_distribution.SUN_STATUS' => 'sun_status',
				'entry_distribution.REMOTE_ID' => 'remote_id',
				'entry_distribution.PLAYS' => 'plays',
				'entry_distribution.VIEWS' => 'views',
				'entry_distribution.ERROR_TYPE' => 'error_type',
				'entry_distribution.ERROR_NUMBER' => 'error_number',
				'entry_distribution.LAST_REPORT' => 'last_report',
				'entry_distribution.NEXT_REPORT' => 'next_report',
				'entry_distribution.STR_ENTRY_ID' => 'str_entry_id',
				'entry_distribution.INT_ENTRY_ID' => 'int_entry_id',
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
				"thumb_asset_ids",
				"flavor_asset_ids",
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
				'entry_distribution.CREATED_AT' => 'created_at',
				'entry_distribution.UPDATED_AT' => 'updated_at',
				'entry_distribution.SUBMITTED_AT' => 'submitted_at',
				'entry_distribution.SUNRISE' => 'sunrise',
				'entry_distribution.SUNSET' => 'sunset',
				'entry_distribution.PLAYS' => 'plays',
				'entry_distribution.VIEWS' => 'views',
				'entry_distribution.LAST_REPORT' => 'last_report',
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
		return EntryDistributionPeer::doCount($c);
	}

}

