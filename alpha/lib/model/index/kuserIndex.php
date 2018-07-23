<?php

/**
 * Auto-generated index class for kuser
*/
class kuserIndex extends BaseIndexObject
{
	const DYNAMIC_ATTRIBUTES = "dynamic_attributes";

	const FIRST_NAME_OR_LAST_NAME = "first_name_or_last_name";

	const PUSER_ID_OR_SCREEN_NAME = "puser_id_or_screen_name";

	const PLUGINS_DATA = "plugins_data";

	public static function getObjectName()
	{
		return 'kuser';
	}

	public static function getObjectIndexName()
	{
		return 'kuser';
	}

	public static function getSphinxIdField()
	{
		return 'id';
	}

	public static function getPropelIdField()
	{
		return kuserPeer::ID;
	}

	public static function getIdField()
	{
		return null;
	}

	public static function getDefaultCriteriaFilter()
	{
		return kuserPeer::getCriteriaFilter();
	}

	protected static $fieldsMap;

	public static function getIndexFieldsMap()
	{
		if (!self::$fieldsMap)
		{
			self::$fieldsMap = array(
				'login_data_id' => 'loginDataId',
				'is_admin' => 'isAdmin',
				'screen_name' => 'screenName',
				'full_name' => 'fullName',
				'first_name' => 'firstName',
				'last_name' => 'lastName',
				'email' => 'email',
				'about_me' => 'aboutMe',
				'tags' => 'tags',
				'entries' => 'entries',
				'storage_size' => 'storageSize',
				'kuser_status' => 'status',
				'created_at' => 'createdAt',
				'updated_at' => 'updatedAt',
				'partner_id' => 'partnerId',
				'display_in_search' => 'displayInSearch',
				'partner_data' => 'partnerData',
				'puser_id' => 'puserId',
				'indexed_partner_data_int' => 'indexedPartnerDataInt',
				'indexed_partner_data_string' => 'indexedPartnerDataString',
				'permission_names' => 'indexedPermissionNames',
				'role_ids' => 'indexedRoleIds',
				'type' => 'type',
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
				'login_data_id' => IIndexable::FIELD_TYPE_UINT,
				'is_admin' => IIndexable::FIELD_TYPE_UINT,
				'screen_name' => IIndexable::FIELD_TYPE_STRING,
				'full_name' => IIndexable::FIELD_TYPE_STRING,
				'first_name' => IIndexable::FIELD_TYPE_STRING,
				'last_name' => IIndexable::FIELD_TYPE_STRING,
				'email' => IIndexable::FIELD_TYPE_STRING,
				'about_me' => IIndexable::FIELD_TYPE_STRING,
				'tags' => IIndexable::FIELD_TYPE_STRING,
				'entries' => IIndexable::FIELD_TYPE_UINT,
				'storage_size' => IIndexable::FIELD_TYPE_UINT,
				'kuser_status' => IIndexable::FIELD_TYPE_UINT,
				'created_at' => IIndexable::FIELD_TYPE_DATETIME,
				'updated_at' => IIndexable::FIELD_TYPE_DATETIME,
				'partner_id' => IIndexable::FIELD_TYPE_INTEGER,
				'display_in_search' => IIndexable::FIELD_TYPE_INTEGER,
				'partner_data' => IIndexable::FIELD_TYPE_STRING,
				'puser_id' => IIndexable::FIELD_TYPE_STRING,
				'indexed_partner_data_int' => IIndexable::FIELD_TYPE_INTEGER,
				'indexed_partner_data_string' => IIndexable::FIELD_TYPE_STRING,
				'permission_names' => IIndexable::FIELD_TYPE_STRING,
				'role_ids' => IIndexable::FIELD_TYPE_STRING,
				'dynamic_attributes' => IIndexable::FIELD_TYPE_JSON,
				'plugins_data' => IIndexable::FIELD_TYPE_STRING,
				'type' => IIndexable::FIELD_TYPE_UINT,
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
				'puser_id',
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
				'kuser.LOGIN_DATA_ID' => 'login_data_id',
				'kuser.IS_ADMIN' => 'is_admin',
				'kuser.SCREEN_NAME' => 'screen_name',
				'kuser.FULL_NAME' => 'full_name',
				'kuser.FIRST_NAME' => 'first_name',
				'kuser.LAST_NAME' => 'last_name',
				'kuser.EMAIL' => 'email',
				'kuser.ABOUT_ME' => 'about_me',
				'kuser.TAGS' => 'tags',
				'kuser.ENTRIES' => 'entries',
				'kuser.STORAGE_SIZE' => 'storage_size',
				'kuser.STATUS' => 'kuser_status',
				'kuser.CREATED_AT' => 'created_at',
				'kuser.UPDATED_AT' => 'updated_at',
				'kuser.PARTNER_ID' => 'partner_id',
				'kuser.DISPLAY_IN_SEARCH' => 'display_in_search',
				'kuser.PARTNER_DATA' => 'partner_data',
				'kuser.PUSER_ID' => 'puser_id',
				'kuser.INDEXED_PARTNER_DATA_INT' => 'indexed_partner_data_int',
				'kuser.INDEXED_PARTNER_DATA_STRING' => 'indexed_partner_data_string',
				'kuser.PERMISSION_NAMES' => 'permission_names',
				'kuser.ROLE_IDS' => 'role_ids',
				'kuser.DYNAMIC_ATTRIBUTES' => 'dynamic_attributes',
				'kuser.FIRST_NAME_OR_LAST_NAME' => '(full_name,last_name)',
				'kuser.PUSER_ID_OR_SCREEN_NAME' => '(puser_id,screen_name)',
				'kuser.PLUGINS_DATA' => 'plugins_data',
				'kuser.TYPE' => 'type',
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
				"screen_name",
				"full_name",
				"first_name",
				"last_name",
				"email",
				"about_me",
				"tags",
				"partner_data",
				"puser_id",
				"indexed_partner_data_string",
				"permission_names",
				"role_ids",
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
				'kuser.LOGIN_DATA_ID' => 'login_data_id',
				'kuser.IS_ADMIN' => 'is_admin',
				'kuser.CREATED_AT' => 'created_at',
				'kuser.UPDATED_AT' => 'updated_at',
				'kuser.DISPLAY_IN_SEARCH' => 'display_in_search',
				'kuser.INDEXED_PARTNER_DATA_INT' => 'indexed_partner_data_int',
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
		return kuserPeer::doCount($c);
	}

	//This function is generated based on cacheInvalidationKey elements in the relevant IndexSchema.xml
	public static function getCacheInvalidationKeys($object = null)
	{
		if (is_null($object))
			return array(array("kuser:id=%s", kuserPeer::ID), array("kuser:partnerId=%s", kuserPeer::PARTNER_ID));
		else
			return array("kuser:id=".strtolower($object->getId()), "kuser:partnerId=".strtolower($object->getPartnerId()));
	}

}

