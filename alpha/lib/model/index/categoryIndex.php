<?php

/**
 * Auto-generated index class for category
 */
class categoryIndex extends BaseIndexObject
{
	const FREE_TEXT = "free_text";

	const NAME_REFERENCE_ID = "name_reference_id";

	const PLUGINS_DATA = "plugins_data";

	const DYNAMIC_ATTRIBUTES = "dynamic_attributes";

	public static function getObjectName()
	{
		return 'category';
	}

	public static function getObjectIndexName()
	{
		return 'category';
	}

	public static function getSphinxIdField()
	{
		return 'id';
	}

	public static function getPropelIdField()
	{
		return categoryPeer::ID;
	}

	public static function getIdField()
	{
		return null;
	}

	public static function getDefaultCriteriaFilter()
	{
		return categoryPeer::getCriteriaFilter();
	}

	protected static $fieldsMap;

	public static function getIndexFieldsMap()
	{
		if (!self::$fieldsMap)
		{
			self::$fieldsMap = array(
				'category_id' => 'id',
				'str_category_id' => 'id',
				'parent_id' => 'parentId',
				'partner_id' => 'partnerId',
				'name' => 'name',
				'full_name' => 'searchIndexfullName',
				'full_ids' => 'searchIndexfullIds',
				'description' => 'description',
				'tags' => 'tags',
				'category_status' => 'status',
				'kuser_id' => 'kuserId',
				'display_in_search' => 'optimizedDisplayInSearchIndex',
				'members' => 'membersByPermissionLevel',
				'depth' => 'depth',
				'reference_id' => 'referenceId',
				'privacy_context' => 'searchIndexprivacyContext',
				'privacy_contexts' => 'searchIndexPrivacyContexts',
				'members_count' => 'membersCount',
				'pending_members_count' => 'pendingMembersCount',
				'entries_count' => 'entriesCount',
				'direct_entries_count' => 'directEntriesCount',
				'direct_sub_categories_count' => 'directSubCategoriesCount',
				'privacy' => 'privacyPartnerIdx',
				'inheritance_type' => 'inheritanceType',
				'user_join_policy' => 'userJoinPolicy',
				'default_permission_level' => 'defaultPermissionLevel',
				'contribution_policy' => 'contributionPolicy',
				'inherited_parent_id' => 'inheritedParentId',
				'created_at' => 'createdAt',
				'updated_at' => 'updatedAt',
				'deleted_at' => 'deletedAt',
				'partner_sort_value' => 'partnerSortValue',
				'sphinx_match_optimizations' => 'sphinxMatchOptimizations',
				'aggregation_categories' => 'aggregationCategoriesIndexEngine',
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
				'category_id' => IIndexable::FIELD_TYPE_UINT,
				'str_category_id' => IIndexable::FIELD_TYPE_STRING,
				'parent_id' => IIndexable::FIELD_TYPE_UINT,
				'partner_id' => IIndexable::FIELD_TYPE_INTEGER,
				'name' => IIndexable::FIELD_TYPE_STRING,
				'full_name' => IIndexable::FIELD_TYPE_STRING,
				'full_ids' => IIndexable::FIELD_TYPE_STRING,
				'description' => IIndexable::FIELD_TYPE_STRING,
				'tags' => IIndexable::FIELD_TYPE_STRING,
				'category_status' => IIndexable::FIELD_TYPE_UINT,
				'kuser_id' => IIndexable::FIELD_TYPE_UINT,
				'display_in_search' => IIndexable::FIELD_TYPE_STRING,
				'members' => IIndexable::FIELD_TYPE_STRING,
				'plugins_data' => IIndexable::FIELD_TYPE_STRING,
				'depth' => IIndexable::FIELD_TYPE_UINT,
				'reference_id' => IIndexable::FIELD_TYPE_STRING,
				'privacy_context' => IIndexable::FIELD_TYPE_STRING,
				'privacy_contexts' => IIndexable::FIELD_TYPE_STRING,
				'members_count' => IIndexable::FIELD_TYPE_UINT,
				'pending_members_count' => IIndexable::FIELD_TYPE_UINT,
				'entries_count' => IIndexable::FIELD_TYPE_UINT,
				'direct_entries_count' => IIndexable::FIELD_TYPE_UINT,
				'direct_sub_categories_count' => IIndexable::FIELD_TYPE_UINT,
				'privacy' => IIndexable::FIELD_TYPE_STRING,
				'inheritance_type' => IIndexable::FIELD_TYPE_UINT,
				'user_join_policy' => IIndexable::FIELD_TYPE_UINT,
				'default_permission_level' => IIndexable::FIELD_TYPE_UINT,
				'contribution_policy' => IIndexable::FIELD_TYPE_UINT,
				'inherited_parent_id' => IIndexable::FIELD_TYPE_UINT,
				'created_at' => IIndexable::FIELD_TYPE_DATETIME,
				'updated_at' => IIndexable::FIELD_TYPE_DATETIME,
				'deleted_at' => IIndexable::FIELD_TYPE_DATETIME,
				'partner_sort_value' => IIndexable::FIELD_TYPE_INTEGER,
				'dynamic_attributes' => IIndexable::FIELD_TYPE_JSON,
				'sphinx_match_optimizations' => IIndexable::FIELD_TYPE_STRING,
				'aggregation_categories' => IIndexable::FIELD_TYPE_STRING,
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
				'description',
				'tags',
				'members',
				'reference_id',
				'privacy_contexts',
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
				'category.ID' => 'category_id',
				'category.CATEGORY_ID' => 'str_category_id',
				'category.PARENT_ID' => 'parent_id',
				'category.PARTNER_ID' => 'partner_id',
				'category.NAME' => 'name',
				'category.FULL_NAME' => 'full_name',
				'category.FULL_IDS' => 'full_ids',
				'category.DESCRIPTION' => 'description',
				'category.TAGS' => 'tags',
				'category.STATUS' => 'category_status',
				'category.KUSER_ID' => 'kuser_id',
				'category.DISPLAY_IN_SEARCH' => 'display_in_search',
				'category.FREE_TEXT' => '(str_category_id,name,tags,description,reference_id)',
				'category.NAME_REFERENCE_ID' => '(name,reference_id)',
				'category.MEMBERS' => 'members',
				'category.PLUGINS_DATA' => 'plugins_data',
				'category.DEPTH' => 'depth',
				'category.REFERENCE_ID' => 'reference_id',
				'category.PRIVACY_CONTEXT' => 'privacy_context',
				'category.PRIVACY_CONTEXTS' => 'privacy_contexts',
				'category.MEMBERS_COUNT' => 'members_count',
				'category.PENDING_MEMBERS_COUNT' => 'pending_members_count',
				'category.ENTRIES_COUNT' => 'entries_count',
				'category.DIRECT_ENTRIES_COUNT' => 'direct_entries_count',
				'category.DIRECT_SUB_CATEGORIES_COUNT' => 'direct_sub_categories_count',
				'category.PRIVACY' => 'privacy',
				'category.INHERITANCE_TYPE' => 'inheritance_type',
				'category.USER_JOIN_POLICY' => 'user_join_policy',
				'category.DEFAULT_PERMISSION_LEVEL' => 'default_permission_level',
				'category.CONTRIBUTION_POLICY' => 'contribution_policy',
				'category.INHERITED_PARENT_ID' => 'inherited_parent_id',
				'category.CREATED_AT' => 'created_at',
				'category.UPDATED_AT' => 'updated_at',
				'category.DELETED_AT' => 'deleted_at',
				'category.PARTNER_SORT_VALUE' => 'partner_sort_value',
				'category.DYNAMIC_ATTRIBUTES' => 'dynamic_attributes',
				'category.SPHINX_MATCH_OPTIMIZATIONS' => 'sphinx_match_optimizations',
				'category.AGGREGATION_CATEGORIES' => 'aggregation_categories',
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
				'category.FULL_NAME' => SearchIndexFieldEscapeType::MD5_LOWER_CASE,
				'category.FULL_IDS' => SearchIndexFieldEscapeType::MD5_LOWER_CASE,
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
				'category.FULL_IDS' => SearchIndexFieldEscapeType::NO_ESCAPE,
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
				"name",
				"full_name",
				"full_ids",
				"description",
				"tags",
				"free_text",
				"members",
				"privacy_context",
				"aggregation_categories",
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
				'category.ID' => 'category_id',
				'category.PARTNER_ID' => 'partner_id',
				'category.NAME' => 'name',
				'category.FULL_NAME' => 'full_name',
				'category.STATUS' => 'category_status',
				'category.KUSER_ID' => 'kuser_id',
				'category.DISPLAY_IN_SEARCH' => 'display_in_search',
				'category.DEPTH' => 'depth',
				'category.MEMBERS_COUNT' => 'members_count',
				'category.PENDING_MEMBERS_COUNT' => 'pending_members_count',
				'category.ENTRIES_COUNT' => 'entries_count',
				'category.DIRECT_ENTRIES_COUNT' => 'direct_entries_count',
				'category.DIRECT_SUB_CATEGORIES_COUNT' => 'direct_sub_categories_count',
				'category.CREATED_AT' => 'created_at',
				'category.UPDATED_AT' => 'updated_at',
				'category.PARTNER_SORT_VALUE' => 'partner_sort_value',
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
				'category.ID',
				'category.FULL_NAME',
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
				'category.PARTNER_ID',
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
			array("%s","category.ID"),
			array("PARENT%s","category.PARENT_ID"),
			array("PID%s","category.PARTNER_ID"),
		);
	}

	public static function getSphinxOptimizationValues()
	{
		return array(
			array("%s","getId"),
			array("PARENT%s","getParentId"),
			array("PID%s","getPartnerId"),
		);
	}

	public static function doCountOnPeer(Criteria $c)
	{
		return categoryPeer::doCount($c);
	}

	public static function getCacheInvalidationKeys($object = null)
	{
		if (is_null($object))
			return array(array("category:id=%s", categoryPeer::ID), array("category:partnerId=%s", categoryPeer::PARTNER_ID));
		else
			return array("category:id=".strtolower($object->getId()), "category:partnerId=".strtolower($object->getPartnerId()));
	}

}

