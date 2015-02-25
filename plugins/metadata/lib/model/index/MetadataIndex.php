<?php

/**
 * Auto-generated index class for Metadata
*/
class MetadataIndex extends BaseIndexObject
{
	const PLUGINS_DATA = "plugins_data";

	public static function getObjectName()
	{
		return 'metadata';
	}

	public static function getObjectIndexName()
	{
		return 'metadata';
	}

	public static function getSphinxIdField()
	{
		return 'metadata_id';
	}

	public static function getPropelIdField()
	{
		return MetadataPeer::ID;
	}

	public static function getIdField()
	{
		return null;
	}

	public static function getDefaultCriteriaFilter()
	{
		return MetadataPeer::getCriteriaFilter();
	}

	protected static $fieldsMap;

	public static function getIndexFieldsMap()
	{
		if (!self::$fieldsMap)
		{
			self::$fieldsMap = array(
				'metadata_id' => 'id',
				'created_at' => 'createdAt',
				'updated_at' => 'updatedAt',
				'version' => 'version',
				'metadata_profile_id' => 'metadataProfileId',
				'metadata_profile_version' => 'metadataProfileVersion',
				'partner_id' => 'partnerId',
				'object_id' => 'objectId',
				'object_type' => 'objectType',
				'metadata_status' => 'status',
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
				'metadata_id' => IIndexable::FIELD_TYPE_INTEGER,
				'created_at' => IIndexable::FIELD_TYPE_DATETIME,
				'updated_at' => IIndexable::FIELD_TYPE_DATETIME,
				'version' => IIndexable::FIELD_TYPE_INTEGER,
				'metadata_profile_id' => IIndexable::FIELD_TYPE_INTEGER,
				'metadata_profile_version' => IIndexable::FIELD_TYPE_INTEGER,
				'partner_id' => IIndexable::FIELD_TYPE_INTEGER,
				'object_id' => IIndexable::FIELD_TYPE_STRING,
				'object_type' => IIndexable::FIELD_TYPE_INTEGER,
				'metadata_status' => IIndexable::FIELD_TYPE_INTEGER,
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
				'metadata.ID' => 'metadata_id',
				'metadata.CREATED_AT' => 'created_at',
				'metadata.UPDATED_AT' => 'updated_at',
				'metadata.VERSION' => 'version',
				'metadata.METADATA_PROFILE_ID' => 'metadata_profile_id',
				'metadata.METADATA_PROFILE_VERSION' => 'metadata_profile_version',
				'metadata.PARTNER_ID' => 'partner_id',
				'metadata.OBJECT_ID' => 'object_id',
				'metadata.OBJECT_TYPE' => 'object_type',
				'metadata.STATUS' => 'metadata_status',
				'metadata.PLUGINS_DATA' => 'plugins_data',
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
				"plugins_data",
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
				'metadata.CREATED_AT' => 'created_at',
				'metadata.UPDATED_AT' => 'updated_at',
				'metadata.VERSION' => 'version',
				'metadata.METADATA_PROFILE_VERSION' => 'metadata_profile_version',
				'metadata.OBJECT_TYPE' => 'object_type',
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
				'metadata.ID',
				'metadata.OBJECT_ID',
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
		return MetadataPeer::doCount($c);
	}

}

