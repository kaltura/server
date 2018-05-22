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
		return 'id';
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
				'created_at' => 'createdAt',
				'updated_at' => 'updatedAt',
				'version' => 'version',
				'metadata_profile_id' => 'metadataProfileId',
				'metadata_profile_version' => 'metadataProfileVersion',
				'partner_id' => 'partnerId',
				'object_id' => 'objectId',
				'object_type' => 'objectType',
				'metadata_status' => 'status',
				'sphinx_match_optimizations' => 'sphinxMatchOptimizations',
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
				'created_at' => IIndexable::FIELD_TYPE_DATETIME,
				'updated_at' => IIndexable::FIELD_TYPE_DATETIME,
				'version' => IIndexable::FIELD_TYPE_UINT,
				'metadata_profile_id' => IIndexable::FIELD_TYPE_UINT,
				'metadata_profile_version' => IIndexable::FIELD_TYPE_UINT,
				'partner_id' => IIndexable::FIELD_TYPE_INTEGER,
				'object_id' => IIndexable::FIELD_TYPE_STRING,
				'object_type' => IIndexable::FIELD_TYPE_UINT,
				'metadata_status' => IIndexable::FIELD_TYPE_UINT,
				'plugins_data' => IIndexable::FIELD_TYPE_STRING,
				'sphinx_match_optimizations' => IIndexable::FIELD_TYPE_STRING,
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
				'metadata.SPHINX_MATCH_OPTIMIZATIONS' => 'sphinx_match_optimizations',
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
				'metadata.CREATED_AT',
				'metadata.UPDATED_AT',
				'metadata.VERSION',
				'metadata.METADATA_PROFILE_ID',
				'metadata.METADATA_PROFILE_VERSION',
				'metadata.PARTNER_ID',
				'metadata.OBJECT_ID',
				'metadata.OBJECT_TYPE',
				'metadata.STATUS',
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
			array("P%s","metadata.PARTNER_ID"),
		);
	}

	public static function getSphinxOptimizationValues()
	{
		return array(
			array("P%s","getPartnerId"),
		);
	}

	public static function doCountOnPeer(Criteria $c)
	{
		return MetadataPeer::doCount($c);
	}

}

