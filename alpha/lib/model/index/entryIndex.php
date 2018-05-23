<?php

/**
 * Auto-generated index class for entry
*/
class entryIndex extends BaseIndexObject
{
	const DYNAMIC_ATTRIBUTES = "dynamic_attributes";

	const FIRST_BROADCAST = "first_broadcast";

	const PLUGINS_DATA = "plugins_data";

	const SEARCH_TEXT = "search_text";

	public static function getObjectName()
	{
		return 'entry';
	}

	public static function getObjectIndexName()
	{
		return 'entry';
	}

	public static function getSphinxIdField()
	{
		return 'str_entry_id';
	}

	public static function getPropelIdField()
	{
		return entryPeer::ID;
	}

	public static function getIdField()
	{
		return entryPeer::ID;
	}

	public static function getDefaultCriteriaFilter()
	{
		return entryPeer::getCriteriaFilter();
	}

	protected static $fieldsMap;

	public static function getIndexFieldsMap()
	{
		if (!self::$fieldsMap)
		{
			self::$fieldsMap = array(
				'entry_id' => 'id',
				'str_entry_id' => 'id',
				'int_entry_id' => 'indexedId',
				'name' => 'name',
				'tags' => 'tags',
				'categories' => 'categoriesEntryIds',
				'flavor_params' => 'flavorParamsIds',
				'kshow_id' => 'kshowId',
				'group_id' => 'groupId',
				'description' => 'description',
				'admin_tags' => 'adminTags',
				'duration_type' => 'durationType',
				'reference_id' => 'referenceIdWithMd5',
				'replacing_entry_id' => 'replacingEntryId',
				'replaced_entry_id' => 'replacedEntryId',
				'roots' => 'roots',
				'kuser_id' => 'kuserId',
				'puser_id' => 'puserId',
				'entry_status' => 'status',
				'type' => 'type',
				'media_type' => 'mediaType',
				'views' => 'views',
				'partner_id' => 'partnerId',
				'moderation_status' => 'moderationStatus',
				'display_in_search' => 'displayInSearch',
				'length_in_msecs' => 'lengthInMsecs',
				'access_control_id' => 'accessControlId',
				'moderation_count' => 'moderationCount',
				'rank' => 'rank',
				'total_rank' => 'totalRank',
				'plays' => 'plays',
				'partner_sort_value' => 'partnerSortValue',
				'replacement_status' => 'replacementStatus',
				'sphinx_match_optimizations' => 'sphinxMatchOptimizations',
				'created_at' => 'createdAt',
				'updated_at' => 'updatedAt',
				'modified_at' => 'modifiedAt',
				'media_date' => 'mediaDate',
				'start_date' => 'startDate',
				'end_date' => 'endDate',
				'available_from' => 'availableFrom',
				'last_played_at' => 'lastPlayedAt',
				'entitled_kusers_publish' => 'entitledKusersPublish',
				'entitled_kusers_edit' => 'entitledKusersEdit',
				'entitled_kusers' => 'entitledKusers',
				'entitled_kusers_view' => 'entitledKusersView',
				'privacy_by_contexts' => 'privacyByContexts',
				'creator_kuser_id' => 'creatorKuserId',
				'creator_puser_id' => 'creatorPuserId',
				'dynamic_attributes' => 'dynamicAttributes',
				'user_names' => 'userNames',
				'source' => 'source',
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
				'entry_id' => IIndexable::FIELD_TYPE_STRING,
				'str_entry_id' => IIndexable::FIELD_TYPE_STRING,
				'int_entry_id' => IIndexable::FIELD_TYPE_UINT,
				'name' => IIndexable::FIELD_TYPE_STRING,
				'tags' => IIndexable::FIELD_TYPE_STRING,
				'categories' => IIndexable::FIELD_TYPE_STRING,
				'flavor_params' => IIndexable::FIELD_TYPE_STRING,
				'kshow_id' => IIndexable::FIELD_TYPE_STRING,
				'group_id' => IIndexable::FIELD_TYPE_STRING,
				'description' => IIndexable::FIELD_TYPE_STRING,
				'admin_tags' => IIndexable::FIELD_TYPE_STRING,
				'duration_type' => IIndexable::FIELD_TYPE_STRING,
				'reference_id' => IIndexable::FIELD_TYPE_STRING,
				'replacing_entry_id' => IIndexable::FIELD_TYPE_STRING,
				'replaced_entry_id' => IIndexable::FIELD_TYPE_STRING,
				'roots' => IIndexable::FIELD_TYPE_STRING,
				'kuser_id' => IIndexable::FIELD_TYPE_STRING,
				'puser_id' => IIndexable::FIELD_TYPE_STRING,
				'entry_status' => IIndexable::FIELD_TYPE_INTEGER,
				'type' => IIndexable::FIELD_TYPE_UINT,
				'media_type' => IIndexable::FIELD_TYPE_UINT,
				'views' => IIndexable::FIELD_TYPE_UINT,
				'partner_id' => IIndexable::FIELD_TYPE_INTEGER,
				'moderation_status' => IIndexable::FIELD_TYPE_UINT,
				'display_in_search' => IIndexable::FIELD_TYPE_INTEGER,
				'length_in_msecs' => IIndexable::FIELD_TYPE_UINT,
				'access_control_id' => IIndexable::FIELD_TYPE_UINT,
				'moderation_count' => IIndexable::FIELD_TYPE_UINT,
				'rank' => IIndexable::FIELD_TYPE_UINT,
				'total_rank' => IIndexable::FIELD_TYPE_UINT,
				'plays' => IIndexable::FIELD_TYPE_UINT,
				'partner_sort_value' => IIndexable::FIELD_TYPE_INTEGER,
				'replacement_status' => IIndexable::FIELD_TYPE_UINT,
				'sphinx_match_optimizations' => IIndexable::FIELD_TYPE_STRING,
				'created_at' => IIndexable::FIELD_TYPE_DATETIME,
				'updated_at' => IIndexable::FIELD_TYPE_DATETIME,
				'modified_at' => IIndexable::FIELD_TYPE_DATETIME,
				'media_date' => IIndexable::FIELD_TYPE_DATETIME,
				'start_date' => IIndexable::FIELD_TYPE_DATETIME,
				'end_date' => IIndexable::FIELD_TYPE_DATETIME,
				'available_from' => IIndexable::FIELD_TYPE_DATETIME,
				'last_played_at' => IIndexable::FIELD_TYPE_DATETIME,
				'entitled_kusers_publish' => IIndexable::FIELD_TYPE_STRING,
				'entitled_kusers_edit' => IIndexable::FIELD_TYPE_STRING,
				'entitled_kusers' => IIndexable::FIELD_TYPE_STRING,
				'entitled_kusers_view' => IIndexable::FIELD_TYPE_STRING,
				'privacy_by_contexts' => IIndexable::FIELD_TYPE_STRING,
				'creator_kuser_id' => IIndexable::FIELD_TYPE_STRING,
				'creator_puser_id' => IIndexable::FIELD_TYPE_STRING,
				'dynamic_attributes' => IIndexable::FIELD_TYPE_JSON,
				'user_names' => IIndexable::FIELD_TYPE_STRING,
				'source' => IIndexable::FIELD_TYPE_UINT,
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
				'tags',
				'categories',
				'flavor_params',
				'kshow_id',
				'group_id',
				'description',
				'admin_tags',
				'reference_id',
				'replacing_entry_id',
				'replaced_entry_id',
				'roots',
				'entitled_kusers_publish',
				'entitled_kusers_edit',
				'entitled_kusers',
				'entitled_kusers_view',
				'privacy_by_contexts',
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
				'entry.ENTRY_ID' => 'entry_id',
				'entry.STR_ENTRY_ID' => 'str_entry_id',
				'entry.ID' => 'int_entry_id',
				'entry.NAME' => 'name',
				'entry.TAGS' => 'tags',
				'entry.CATEGORIES_IDS' => 'categories',
				'entry.FLAVOR_PARAMS_IDS' => 'flavor_params',
				'entry.KSHOW_ID' => 'kshow_id',
				'entry.GROUP_ID' => 'group_id',
				'entry.DESCRIPTION' => 'description',
				'entry.ADMIN_TAGS' => 'admin_tags',
				'entry.DURATION_TYPE' => 'duration_type',
				'entry.REFERENCE_ID' => 'reference_id',
				'entry.REPLACING_ENTRY_ID' => 'replacing_entry_id',
				'entry.REPLACED_ENTRY_ID' => 'replaced_entry_id',
				'entry.ROOTS' => 'roots',
				'entry.KUSER_ID' => 'kuser_id',
				'entry.PUSER_ID' => 'puser_id',
				'entry.STATUS' => 'entry_status',
				'entry.TYPE' => 'type',
				'entry.MEDIA_TYPE' => 'media_type',
				'entry.VIEWS' => 'views',
				'entry.PARTNER_ID' => 'partner_id',
				'entry.MODERATION_STATUS' => 'moderation_status',
				'entry.DISPLAY_IN_SEARCH' => 'display_in_search',
				'entry.LENGTH_IN_MSECS' => 'length_in_msecs',
				'entry.ACCESS_CONTROL_ID' => 'access_control_id',
				'entry.MODERATION_COUNT' => 'moderation_count',
				'entry.RANK' => 'rank',
				'entry.TOTAL_RANK' => 'total_rank',
				'entry.PLAYS' => 'plays',
				'entry.PARTNER_SORT_VALUE' => 'partner_sort_value',
				'entry.REPLACEMENT_STATUS' => 'replacement_status',
				'entry.SPHINX_MATCH_OPTIMIZATIONS' => 'sphinx_match_optimizations',
				'entry.CREATED_AT' => 'created_at',
				'entry.UPDATED_AT' => 'updated_at',
				'entry.MODIFIED_AT' => 'modified_at',
				'entry.MEDIA_DATE' => 'media_date',
				'entry.START_DATE' => 'start_date',
				'entry.END_DATE' => 'end_date',
				'entry.AVAILABLE_FROM' => 'available_from',
				'entry.LAST_PLAYED_AT' => 'last_played_at',
				'entry.ENTITLED_KUSERS_PUBLISH' => 'entitled_kusers_publish',
				'entry.ENTITLED_KUSERS_EDIT' => 'entitled_kusers_edit',
				'entry.ENTITLED_KUSERS' => 'entitled_kusers',
				'entry.ENTITLED_KUSERS_VIEW' => 'entitled_kusers_view',
				'entry.PRIVACY_BY_CONTEXTS' => 'privacy_by_contexts',
				'entry.CREATOR_KUSER_ID' => 'creator_kuser_id',
				'entry.CREATOR_PUSER_ID' => 'creator_puser_id',
				'entry.DYNAMIC_ATTRIBUTES' => 'dynamic_attributes',
				'entry.FIRST_BROADCAST' => 'dynamic_attributes.first_broadcast',
				'entry.USER_NAMES' => 'user_names',
				'entry.SOURCE' => 'source',
				'entry.PLUGINS_DATA' => 'plugins_data',
				'entry.SEARCH_TEXT' => '(name,tags,description,entry_id,reference_id,roots,puser_id)',
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
				"name",
				"tags",
				"categories_ids",
				"flavor_params_ids",
				"description",
				"admin_tags",
				"duration_type",
				"reference_id",
				"replacing_entry_id",
				"replaced_entry_id",
				"roots",
				"entitled_kusers_publish",
				"entitled_kusers_edit",
				"entitled_kusers_view",
				"dynamic_attributes",
				"plugins_data",
				"search_text",
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
				'entry.NAME' => 'name',
				'entry.KUSER_ID' => 'kuser_id',
				'entry.STATUS' => 'entry_status',
				'entry.TYPE' => 'type',
				'entry.MEDIA_TYPE' => 'media_type',
				'entry.VIEWS' => 'views',
				'entry.PARTNER_ID' => 'partner_id',
				'entry.MODERATION_STATUS' => 'moderation_status',
				'entry.DISPLAY_IN_SEARCH' => 'display_in_search',
				'entry.LENGTH_IN_MSECS' => 'length_in_msecs',
				'entry.ACCESS_CONTROL_ID' => 'access_control_id',
				'entry.MODERATION_COUNT' => 'moderation_count',
				'entry.RANK' => 'rank',
				'entry.TOTAL_RANK' => 'total_rank',
				'entry.PLAYS' => 'plays',
				'entry.PARTNER_SORT_VALUE' => 'partner_sort_value',
				'entry.CREATED_AT' => 'created_at',
				'entry.UPDATED_AT' => 'updated_at',
				'entry.MODIFIED_AT' => 'modified_at',
				'entry.MEDIA_DATE' => 'media_date',
				'entry.START_DATE' => 'start_date',
				'entry.END_DATE' => 'end_date',
				'entry.AVAILABLE_FROM' => 'available_from',
				'entry.LAST_PLAYED_AT' => 'last_played_at',
				'entry.FIRST_BROADCAST' => 'dynamic_attributes.first_broadcast',
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
				'entry.ID',
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
				'entry.PARTNER_ID',
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
				'status' => 'entry_status',
				'type' => 'type',
				'mediaType' => 'media_type',
				'views' => 'views',
				'partnerId' => 'partner_id',
				'moderationStatus' => 'moderation_status',
				'msDuration' => 'length_in_msecs',
				'accessControlId' => 'access_control_id',
				'moderationCount' => 'moderation_count',
				'rank' => 'rank',
				'totalRank' => 'total_rank',
				'plays' => 'plays',
				'partnerSortValue' => 'partner_sort_value',
				'replacementStatus' => 'replacement_status',
				'createdAt' => 'created_at',
				'updatedAt' => 'updated_at',
				'mediaDate' => 'media_date',
				'startDate' => 'start_date',
				'endDate' => 'end_date',
				'lastPlayedAt' => 'last_played_at',
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
				'id' => 'entry_id',
				'name' => 'name',
				'tags' => 'tags',
				'categoriesIds' => 'categories',
				'flavorParamsIds' => 'flavor_params',
				'groupId' => 'group_id',
				'description' => 'description',
				'adminTags' => 'admin_tags',
				'durationType' => 'duration_type',
				'referenceId' => 'reference_id',
				'replacingEntryId' => 'replacing_entry_id',
				'replacedEntryId' => 'replaced_entry_id',
				'userId' => 'puser_id',
				'creatorId' => 'creator_puser_id',
				'searchText' => '(name,tags,description,entry_id,reference_id,roots,puser_id)',
			);
		}
		return self::$apiMatchAttributesMap;
	}

	//This function is generated based on index elements in the relevant IndexSchema.xml
	public static function getSphinxOptimizationMap()
	{
		return array(
			array("P%sST%s","entry.PARTNER_ID","entry.STATUS"),
			array("%s","entry.ID"),
			array("isLive%s","dynamic_attributes.isLive"),
		);
	}

	//This function is generated based on index elements in the relevant IndexSchema.xml
	public static function getSphinxOptimizationValues()
	{
		return array(
			array("P%sST%s","getPartnerId","getStatus"),
			array("%s","getId"),
			array("isLive%s","getDynamicAttributes.isLive"),
		);
	}

	public static function doCountOnPeer(Criteria $c)
	{
		return entryPeer::doCount($c);
	}

	//This function is generated based on cacheInvalidationKey elements in the relevant IndexSchema.xml
	public static function getCacheInvalidationKeys($object = null)
	{
		if (is_null($object))
			return array(array("entry:id=%s", entryPeer::ID), array("entry:partnerId=%s", entryPeer::PARTNER_ID));
		else
			return array("entry:id=".strtolower($object->getId()), "entry:partnerId=".strtolower($object->getPartnerId()));
	}

}

