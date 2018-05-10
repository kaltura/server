<?php

/**
 * Auto-generated index class for categoryKuser
*/
class categoryKuserIndex extends BaseIndexObject
{
	public static function getObjectName()
	{
		return 'category_kuser';
	}

	public static function getObjectIndexName()
	{
		return 'category_kuser';
	}

	public static function getSphinxIdField()
	{
		return 'id';
	}

	public static function getPropelIdField()
	{
		return categoryKuserPeer::ID;
	}

	public static function getIdField()
	{
		return null;
	}

	public static function getDefaultCriteriaFilter()
	{
		return categoryKuserPeer::getCriteriaFilter();
	}

	protected static $fieldsMap;

	public static function getIndexFieldsMap()
	{
		if (!self::$fieldsMap)
		{
			self::$fieldsMap = array(
				'category_id' => 'categoryId',
				'kuser_id' => 'kuserId',
				'category_full_ids' => 'searchIndexCategoryFullIds',
				'permission_names' => 'searchIndexPermissionNames',
				'puser_id' => 'puserId',
				'screen_name' => 'screenName',
				'category_kuser_status' => 'searchIndexStatus',
				'partner_id' => 'partnerId',
				'update_method' => 'searchIndexUpdateMethod',
				'created_at' => 'createdAt',
				'updated_at' => 'updatedAt',
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
				'category_id' => IIndexable::FIELD_TYPE_STRING,
				'kuser_id' => IIndexable::FIELD_TYPE_STRING,
				'category_full_ids' => IIndexable::FIELD_TYPE_STRING,
				'permission_names' => IIndexable::FIELD_TYPE_STRING,
				'puser_id' => IIndexable::FIELD_TYPE_STRING,
				'screen_name' => IIndexable::FIELD_TYPE_STRING,
				'category_kuser_status' => IIndexable::FIELD_TYPE_STRING,
				'partner_id' => IIndexable::FIELD_TYPE_STRING,
				'update_method' => IIndexable::FIELD_TYPE_STRING,
				'created_at' => IIndexable::FIELD_TYPE_DATETIME,
				'updated_at' => IIndexable::FIELD_TYPE_DATETIME,
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
				'category_kuser.CATEGORY_ID' => 'category_id',
				'category_kuser.KUSER_ID' => 'kuser_id',
				'category_kuser.CATEGORY_FULL_IDS' => 'category_full_ids',
				'category_kuser.PERMISSION_NAMES' => 'permission_names',
				'category_kuser.PUSER_ID' => 'puser_id',
				'category_kuser.SCREEN_NAME' => 'screen_name',
				'category_kuser.STATUS' => 'category_kuser_status',
				'category_kuser.PARTNER_ID' => 'partner_id',
				'category_kuser.UPDATE_METHOD' => 'update_method',
				'category_kuser.CREATED_AT' => 'created_at',
				'category_kuser.UPDATED_AT' => 'updated_at',
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
				'category_kuser.CATEGORY_FULL_IDS' => SearchIndexFieldEscapeType::MD5_LOWER_CASE,
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
				'category_kuser.CATEGORY_FULL_IDS' => SearchIndexFieldEscapeType::NO_ESCAPE,
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
				"category_id",
				"kuser_id",
				"category_full_ids",
				"permission_names",
				"puser_id",
				"screen_name",
				"status",
				"update_method",
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
				'category_kuser.CREATED_AT' => 'created_at',
				'category_kuser.UPDATED_AT' => 'updated_at',
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
		return categoryKuserPeer::doCount($c);
	}
	
        public static function getCacheInvalidationKeys($object = null)
        {
                if (is_null($object))
                        return array(array("category_kuser:partnerId=%s", categoryKuserPeer::PARTNER_ID));
                else
                        return array("category_kuser:partnerId=" . strtolower($object->getPartnerId()));
        }
}
