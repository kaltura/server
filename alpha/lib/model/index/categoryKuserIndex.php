<?php

/**
 * Auto-generated index class for categoryKuser
*/
class categoryKuserIndex extends BaseIndexObject
{
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
				'categoryKuser.CATEGORY_ID' => 'category_id',
				'categoryKuser.KUSER_ID' => 'kuser_id',
				'categoryKuser.CATEGORY_FULL_IDS' => 'category_full_ids',
				'categoryKuser.PERMISSION_NAMES' => 'permission_names',
				'categoryKuser.PUSER_ID' => 'puser_id',
				'categoryKuser.SCREEN_NAME' => 'screen_name',
				'categoryKuser.STATUS' => 'category_kuser_status',
				'categoryKuser.PARTNER_ID' => 'partner_id',
				'categoryKuser.UPDATE_METHOD' => 'update_method',
				'categoryKuser.CREATED_AT' => 'created_at',
				'categoryKuser.UPDATED_AT' => 'updated_at',
			);
		}
		return self::$searchableFieldsMap;
	}

	public static function getSearchFieldsEscapeTypeList()
	{
		if (!self::$searchEscapeTypes)
		{
			self::$searchEscapeTypes = array(
				'categoryKuser.CATEGORY_FULL_IDS' => SearchIndexFieldEscapeType::MD5_LOWER_CASE,
			);
		}
		return self::$searchEscapeTypes;
	}

	public static function getIndexFieldsEscapeTypeList()
	{
		if (!self::$indexEscapeTypes)
		{
			self::$indexEscapeTypes = array(
				'categoryKuser.CATEGORY_FULL_IDS' => SearchIndexFieldEscapeType::NO_ESCAPE,
			);
		}
		return self::$indexEscapeTypes;
	}

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

	public static function getIndexOrderList()
	{
		if (!self::$orderFields)
		{
			self::$orderFields = array(
				'categoryKuser.CREATED_AT' => 'created_at',
				'categoryKuser.UPDATED_AT' => 'updated_at',
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
		return categoryKuserPeer::doCount($c);
	}

}

