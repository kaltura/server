<?php

/**
 * Auto-generated index class for Tag
*/
class TagIndex extends BaseIndexObject
{
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

	public static function getIndexFieldsMap()
	{
		if (!self::$fieldsMap)
		{
			self::$fieldsMap = array(
				'int_id' => 'intId',
				'tag' => 'tag',
				'partner_id' => 'partnerId',
				'object_type' => 'indexObjectType',
				'created_at' => 'createdAt',
				'instance_count' => 'instanceCount',
				'privacy_context' => 'indexPrivacyContext',
			);
		}
		return self::$fieldsMap;
	}

	public static function getIndexFieldTypesMap()
	{
		if (!self::$typesMap)
		{
			self::$typesMap = array(
				'int_id' => IIndexable::FIELD_TYPE_INTEGER,
				'tag' => IIndexable::FIELD_TYPE_STRING,
				'partner_id' => IIndexable::FIELD_TYPE_STRING,
				'object_type' => IIndexable::FIELD_TYPE_STRING,
				'created_at' => IIndexable::FIELD_TYPE_DATETIME,
				'instance_count' => IIndexable::FIELD_TYPE_INTEGER,
				'privacy_context' => IIndexable::FIELD_TYPE_STRING,
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
				'Tag.INT_ID' => 'int_id',
				'Tag.TAG' => 'tag',
				'Tag.PARTNER_ID' => 'partner_id',
				'Tag.OBJECT_TYPE' => 'object_type',
				'Tag.CREATED_AT' => 'created_at',
				'Tag.INSTANCE_COUNT' => 'instance_count',
				'Tag.PRIVACY_CONTEXT' => 'privacy_context',
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
				"tag",
				"partner_id",
				"object_type",
				"privacy_context",
			);
		}
		return self::$matchableFields;
	}

	public static function getIndexOrderList()
	{
		if (!self::$orderFields)
		{
			self::$orderFields = array(
				'Tag.CREATED_AT' => 'created_at',
				'Tag.INSTANCE_COUNT' => 'instance_count',
			);
		}
		return self::$orderFields;
	}

	public static function getIndexSkipFieldsList()
	{
		if (!self::$skipFields)
		{
			self::$skipFields = array(
				'Tag.TAG',
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
		return TagPeer::doCount($c);
	}

}

