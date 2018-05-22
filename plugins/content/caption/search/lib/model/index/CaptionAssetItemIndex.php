<?php

/**
 * Auto-generated index class for CaptionAssetItem
 */
class CaptionAssetItemIndex extends BaseIndexObject
{
	public static function getObjectName()
	{
		return 'caption_asset_item';
	}

	public static function getObjectIndexName()
	{
		return 'caption_item';
	}

	public static function getSphinxIdField()
	{
		return 'id';
	}

	public static function getPropelIdField()
	{
		return CaptionAssetItemPeer::ID;
	}

	public static function getIdField()
	{
		return null;
	}

	public static function getDefaultCriteriaFilter()
	{
		return CaptionAssetItemPeer::getCriteriaFilter();
	}

	protected static $fieldsMap;

	public static function getIndexFieldsMap()
	{
		if (!self::$fieldsMap)
		{
			self::$fieldsMap = array(
				'entry_id' => 'entryId',
				'caption_asset_id' => 'captionAssetId',
				'tags' => 'tags',
				'content' => 'content',
				'partner_description' => 'partnerDescription',
				'language' => 'language',
				'label' => 'label',
				'format' => 'format',
				'caption_params_id' => 'captionParamsId',
				'partner_id' => 'partnerId',
				'version' => 'version',
				'caption_asset_status' => 'status',
				'size' => 'size',
				'is_default' => 'default',
				'start_time' => 'startTime',
				'end_time' => 'endTime',
				'created_at' => 'createdAt',
				'updated_at' => 'updatedAt',
				'str_entry_id' => 'entryId',
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
				'caption_asset_id' => IIndexable::FIELD_TYPE_STRING,
				'tags' => IIndexable::FIELD_TYPE_STRING,
				'content' => IIndexable::FIELD_TYPE_STRING,
				'partner_description' => IIndexable::FIELD_TYPE_STRING,
				'language' => IIndexable::FIELD_TYPE_STRING,
				'label' => IIndexable::FIELD_TYPE_STRING,
				'format' => IIndexable::FIELD_TYPE_STRING,
				'caption_params_id' => IIndexable::FIELD_TYPE_UINT,
				'partner_id' => IIndexable::FIELD_TYPE_INTEGER,
				'version' => IIndexable::FIELD_TYPE_UINT,
				'caption_asset_status' => IIndexable::FIELD_TYPE_INTEGER,
				'size' => IIndexable::FIELD_TYPE_UINT,
				'is_default' => IIndexable::FIELD_TYPE_UINT,
				'start_time' => IIndexable::FIELD_TYPE_UINT,
				'end_time' => IIndexable::FIELD_TYPE_UINT,
				'created_at' => IIndexable::FIELD_TYPE_DATETIME,
				'updated_at' => IIndexable::FIELD_TYPE_DATETIME,
				'str_entry_id' => IIndexable::FIELD_TYPE_STRING,
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
				'caption_asset_item.ENTRY_ID' => 'entry_id',
				'caption_asset_item.CAPTION_ASSET_ID' => 'caption_asset_id',
				'caption_asset_item.TAGS' => 'tags',
				'caption_asset_item.CONTENT' => 'content',
				'caption_asset_item.PARTNER_DESCRIPTION' => 'partner_description',
				'caption_asset_item.LANGUAGE' => 'language',
				'caption_asset_item.LABEL' => 'label',
				'caption_asset_item.FORMAT' => 'format',
				'caption_asset_item.CAPTION_PARAMS_ID' => 'caption_params_id',
				'caption_asset_item.PARTNER_ID' => 'partner_id',
				'caption_asset_item.VERSION' => 'version',
				'caption_asset_item.STATUS' => 'caption_asset_status',
				'caption_asset_item.SIZE' => 'size',
				'caption_asset_item.IS_DEFAULT' => 'is_default',
				'caption_asset_item.START_TIME' => 'start_time',
				'caption_asset_item.END_TIME' => 'end_time',
				'caption_asset_item.CREATED_AT' => 'created_at',
				'caption_asset_item.UPDATED_AT' => 'updated_at',
				'caption_asset_item.STR_ENTRY_ID' => 'str_entry_id',
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
				"entry_id",
				"caption_asset_id",
				"tags",
				"content",
				"partner_description",
				"language",
				"label",
				"format",
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
				'caption_asset_item.SIZE' => 'size',
				'caption_asset_item.START_TIME' => 'start_time',
				'caption_asset_item.END_TIME' => 'end_time',
				'caption_asset_item.CREATED_AT' => 'created_at',
				'caption_asset_item.UPDATED_AT' => 'updated_at',
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
		return CaptionAssetItemPeer::doCount($c);
	}

}

