<?php

/**
 * Auto-generated index class for Tag
 */
class TagIndex extends BaseIndexObject
{
	public static function getObjectName()
	{
		return 'tag';
	}

	public static function getObjectIndexName()
	{
		return 'tag';
	}

	public static function getSphinxIdField()
	{
		return 'int_id';
	}

	public static function getPropelIdField()
	{
		return TagPeer::ID;
	}

	public static function getIdField()
	{
		return null;
	}

	public static function getDefaultCriteriaFilter()
	{
		return TagPeer::getCriteriaFilter();
	}

	protected static $fieldsMap;

	public static function getIndexFieldsMap()
	{
		if (!self::$fieldsMap)
		{
			self::$fieldsMap = array(
				'int_id' => 'intId',
				'tag' => 'tagWithEqual',
				'partner_id' => 'partnerId',
				'object_type' => 'indexObjectType',
				'created_at' => 'createdAt',
				'instance_count' => 'instanceCount',
				'privacy_context' => 'indexPrivacyContext',
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
				'tag' => IIndexable::FIELD_TYPE_STRING,
				'partner_id' => IIndexable::FIELD_TYPE_STRING,
				'object_type' => IIndexable::FIELD_TYPE_STRING,
				'created_at' => IIndexable::FIELD_TYPE_DATETIME,
				'instance_count' => IIndexable::FIELD_TYPE_UINT,
				'privacy_context' => IIndexable::FIELD_TYPE_STRING,
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
				'tag.INT_ID' => 'int_id',
				'tag.TAG' => 'tag',
				'tag.PARTNER_ID' => 'partner_id',
				'tag.OBJECT_TYPE' => 'object_type',
				'tag.CREATED_AT' => 'created_at',
				'tag.INSTANCE_COUNT' => 'instance_count',
				'tag.PRIVACY_CONTEXT' => 'privacy_context',
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
				"tag",
				"partner_id",
				"object_type",
				"privacy_context",
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
				'tag.CREATED_AT' => 'created_at',
				'tag.INSTANCE_COUNT' => 'instance_count',
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
				'tag.TAG',
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
		return TagPeer::doCount($c);
	}

}

