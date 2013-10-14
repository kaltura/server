<?php

/**
 * Auto-generated index class for EntryDistribution
*/
class EntryDistributionIndex extends BaseIndexObject
{
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
				'next_report' => 'nextReport',
			);
		}
		return self::$fieldsMap;
	}

	public static function getIndexFieldTypesMap()
	{
		if (!self::$typesMap)
		{
			self::$typesMap = array(
				'entry_distribution_id' => IIndexable::FIELD_TYPE_INTEGER,
				'created_at' => IIndexable::FIELD_TYPE_DATETIME,
				'updated_at' => IIndexable::FIELD_TYPE_DATETIME,
				'submitted_at' => IIndexable::FIELD_TYPE_DATETIME,
				'entry_id' => IIndexable::FIELD_TYPE_STRING,
				'partner_id' => IIndexable::FIELD_TYPE_INTEGER,
				'distribution_profile_id' => IIndexable::FIELD_TYPE_INTEGER,
				'entry_distribution_status' => IIndexable::FIELD_TYPE_INTEGER,
				'dirty_status' => IIndexable::FIELD_TYPE_INTEGER,
				'thumb_asset_ids' => IIndexable::FIELD_TYPE_STRING,
				'flavor_asset_ids' => IIndexable::FIELD_TYPE_STRING,
				'asset_ids' => IIndexable::FIELD_TYPE_STRING,
				'sunrise' => IIndexable::FIELD_TYPE_DATETIME,
				'sunset' => IIndexable::FIELD_TYPE_DATETIME,
				'sun_status' => IIndexable::FIELD_TYPE_INTEGER,
				'remote_id' => IIndexable::FIELD_TYPE_STRING,
				'plays' => IIndexable::FIELD_TYPE_INTEGER,
				'views' => IIndexable::FIELD_TYPE_INTEGER,
				'error_type' => IIndexable::FIELD_TYPE_INTEGER,
				'error_number' => IIndexable::FIELD_TYPE_INTEGER,
				'last_report' => IIndexable::FIELD_TYPE_DATETIME,
				'next_report' => IIndexable::FIELD_TYPE_DATETIME,
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
				'EntryDistribution.ID' => 'entry_distribution_id',
				'EntryDistribution.CREATED_AT' => 'created_at',
				'EntryDistribution.UPDATED_AT' => 'updated_at',
				'EntryDistribution.SUBMITTED_AT' => 'submitted_at',
				'EntryDistribution.ENTRY_ID' => 'entry_id',
				'EntryDistribution.PARTNER_ID' => 'partner_id',
				'EntryDistribution.DISTRIBUTION_PROFILE_ID' => 'distribution_profile_id',
				'EntryDistribution.STATUS' => 'entry_distribution_status',
				'EntryDistribution.DIRTY_STATUS' => 'dirty_status',
				'EntryDistribution.THUMB_ASSET_IDS' => 'thumb_asset_ids',
				'EntryDistribution.FLAVOR_ASSET_IDS' => 'flavor_asset_ids',
				'EntryDistribution.ASSET_IDS' => 'asset_ids',
				'EntryDistribution.SUNRISE' => 'sunrise',
				'EntryDistribution.SUNSET' => 'sunset',
				'EntryDistribution.SUN_STATUS' => 'sun_status',
				'EntryDistribution.REMOTE_ID' => 'remote_id',
				'EntryDistribution.PLAYS' => 'plays',
				'EntryDistribution.VIEWS' => 'views',
				'EntryDistribution.ERROR_TYPE' => 'error_type',
				'EntryDistribution.ERROR_NUMBER' => 'error_number',
				'EntryDistribution.LAST_REPORT' => 'last_report',
				'EntryDistribution.NEXT_REPORT' => 'next_report',
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
				"thumb_asset_ids",
				"flavor_asset_ids",
			);
		}
		return self::$matchableFields;
	}

	public static function getIndexOrderList()
	{
		if (!self::$orderFields)
		{
			self::$orderFields = array(
				'EntryDistribution.CREATED_AT' => 'created_at',
				'EntryDistribution.UPDATED_AT' => 'updated_at',
				'EntryDistribution.SUBMITTED_AT' => 'submitted_at',
				'EntryDistribution.SUNRISE' => 'sunrise',
				'EntryDistribution.SUNSET' => 'sunset',
				'EntryDistribution.PLAYS' => 'plays',
				'EntryDistribution.VIEWS' => 'views',
				'EntryDistribution.LAST_REPORT' => 'last_report',
				'EntryDistribution.NEXT_REPORT' => 'next_report',
			);
		}
		return self::$orderFields;
	}

	public static function getIndexSkipFieldsList()
	{
		if (!self::$skipFields)
		{
			self::$skipFields = array(
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
		return EntryDistributionPeer::doCount($c);
	}

}

