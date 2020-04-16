<?php
/**
 * @package plugins.elasticSearch
 * @subpackage model.filters
 */

class ESearchEntryQueryFromFilter extends ESearchQueryFromFilter
{
	const ASC = '+';
	const DESC = '-';
	const STATUS_EQ_FILTER = '_eq_status';
	const STATUS_IN_FILTER = '_in_status';
	const STATUS_NOT_EQ_FILTER = '_not_status';
	const STATUS_NOT_IN_FILTER = '_notin_status';
	const MODERATION_STATUS_EQ_FILTER = '_eq_moderation_status';
	const MODERATION_STATUS_IN_FILTER = '_in_moderation_status';
	const MODERATION_STATUS_NOT_EQ_FILTER = '_not_moderation_status';
	const MODERATION_STATUS_NOT_IN_FILTER = '_notin_moderation_status';
	const ID_EQUAL_FILTER = '_eq_id';
	const REDIRECT_FROM_ENTRY_ID_EQUAL_FILTER = '_eq_redirect_from_entry_id';
	const DURATION_TYPE_FILTER_NAME = '_matchor_duration_type';
	const EXTERNAL_SOURCE_TYPE_EQUAL = '_like_plugins_data';
	const EXTERNAL_SOURCE_TYPE_IN = '_mlikeor_plugins_data';
	const SHORT_DURATION_LOWER_BOUND = 0;
	const SHORT_DURATION_UPPER_BOUND = 240000; // 4 minutes in ms
	const MEDIUM_DURATION_UPPER_BOUND = 1200000; // 20 minutes in ms
	const COMMA_SEPARATOR = ',';

	protected static $puserFields = array(
		ESearchEntryFilterFields::USER_ID,
		ESearchEntryFilterFields::CREATOR_ID,
		ESearchEntryFilterFields::ENTITLED_USER_EDIT,
		ESearchEntryFilterFields::ENTITLED_USER_PUBLISH,
		ESearchEntryFilterFields::ENTITLED_USER_VIEW
	);

	protected static $supportedSearchFields = array(
		ESearchEntryFilterFields::ID,
		ESearchEntryFilterFields::STATUS,
		ESearchEntryFilterFields::USER_ID,
		ESearchEntryFilterFields::GROUP_ID,
		ESearchEntryFilterFields::CREATOR_ID,
		ESearchEntryFilterFields::NAME,
		ESearchEntryFilterFields::TAGS,
		ESearchEntryFilterFields::TAGS_NAME,
		ESearchEntryFilterFields::ADMIN_TAGS,
		ESearchEntryFilterFields::TAGS_ADMIN_TAGS,
		ESearchEntryFilterFields::TAGS_ADMIN_TAGS_NAME,
		ESearchEntryFilterFields::CONVERSION_PROFILE_ID,
		ESearchEntryFilterFields::REDIRECT_ENTRY_ID,
		ESearchEntryFilterFields::ENTITLED_USER_EDIT,
		ESearchEntryFilterFields::ENTITLED_USER_PUBLISH,
		ESearchEntryFilterFields::ENTITLED_USER_VIEW,
		ESearchEntryFilterFields::DISPLAY_IN_SEARCH,
		ESearchEntryFilterFields::PARENT_ENTRY_ID,
		ESearchEntryFilterFields::MEDIA_TYPE,
		ESearchEntryFilterFields::SOURCE_TYPE,
		ESearchEntryFilterFields::LENGTH_IN_MSECS,
		ESearchEntryFilterFields::TYPE,
		ESearchEntryFilterFields::MODERATION_STATUS,
		ESearchEntryFilterFields::CREATED_AT,
		ESearchEntryFilterFields::UPDATED_AT,
		ESearchEntryFilterFields::ACCESS_CONTROL_ID,
		ESearchEntryFilterFields::USER_NAMES,
		ESearchEntryFilterFields::START_DATE,
		ESearchEntryFilterFields::END_DATE,
		ESearchEntryFilterFields::REFERENCE_ID,
		ESearchEntryFilterFields::ROOT_ENTRY_ID,
		ESearchEntryFilterFields::DURATION,
		ESearchEntryFilterFields::CATEGORIES,
		ESearchEntryFilterFields::CATEGORIES_IDS,
		ESearchEntryFilterFields::CATEGORIES_ANCESTOR_ID,
		ESearchEntryFilterFields::CATEGORIES_FULL_NAME,
		ESearchEntryFilterFields::PARTNER_SORT_VALUE,
		ESearchEntryFilterFields::SEARCH_TEXT,
		ESearchEntryFilterFields::FREE_TEXT,
		ESearchEntryFilterFields::TOTAL_RANK,
		ESearchEntryFilterFields::RANK,
		ESearchEntryFilterFields::LAST_PLAYED_AT,
		ESearchEntryFilterFields::PLAYS,
		ESearchEntryFilterFields::DURATION_TYPE,
		ESearchEntryFilterFields::EXTERNAL_SOURCE_TYPE,
	);

	protected static $specialFields = array(
		ESearchEntryFilterFields::FREE_TEXT,
		self::DURATION_TYPE_FILTER_NAME,
		self::EXTERNAL_SOURCE_TYPE_EQUAL,
		self::EXTERNAL_SOURCE_TYPE_IN,

	);

	protected static $timeFields = array(
		ESearchEntryFilterFields::CREATED_AT,
		ESearchEntryFilterFields::UPDATED_AT,
		ESearchEntryFilterFields::START_DATE,
		ESearchEntryFilterFields::END_DATE,
		ESearchEntryFilterFields::LAST_PLAYED_AT,
	);

	protected function getTimeFields()
	{
		return self::$timeFields;
	}

	protected static function getSupportedFields()
	{
		return self::$supportedSearchFields;
	}

	protected static function getSpecialFields()
	{
		return self::$specialFields;
	}

	protected static $entryNestedFields = array(
		ESearchMetadataFieldName::PROFILE_ID,
	);

	protected static function getNestedQueryFields()
	{
		return self::$entryNestedFields;
	}

	protected function getSphinxToElasticFieldName($field)
	{
		$fieldsMap = array(
			ESearchEntryFilterFields::ID  => ESearchEntryFieldName::ID,
			ESearchEntryFilterFields::USER_ID => ESearchEntryFieldName::USER_ID,
			ESearchEntryFilterFields::GROUP_ID => ESearchEntryFieldName::USER_ID,
			ESearchEntryFilterFields::CREATOR_ID => ESearchEntryFieldName::CREATOR_ID,
			ESearchEntryFilterFields::NAME => ESearchEntryFieldName::NAME,
			ESearchEntryFilterFields::TAGS => ESearchEntryFieldName::TAGS,
			ESearchEntryFilterFields::TAGS_NAME => ESearchEntryFieldName::TAGS,
			ESearchEntryFilterFields::ADMIN_TAGS => ESearchEntryFieldName::ADMIN_TAGS,
			ESearchEntryFilterFields::TAGS_ADMIN_TAGS => ESearchEntryFieldName::ADMIN_TAGS,
			ESearchEntryFilterFields::TAGS_ADMIN_TAGS_NAME => ESearchEntryFieldName::ADMIN_TAGS,
			ESearchEntryFilterFields::CONVERSION_PROFILE_ID => ESearchEntryFieldName::CONVERSION_PROFILE_ID,
			ESearchEntryFilterFields::REDIRECT_ENTRY_ID => ESearchEntryFieldName::REDIRECT_ENTRY_ID,
			ESearchEntryFilterFields::ENTITLED_USER_EDIT => ESearchEntryFieldName::ENTITLED_USER_EDIT,
			ESearchEntryFilterFields::ENTITLED_USER_PUBLISH => ESearchEntryFieldName::ENTITLED_USER_PUBLISH,
			ESearchEntryFilterFields::ENTITLED_USER_VIEW => ESearchEntryFieldName::ENTITLED_USER_VIEW,
			ESearchEntryFilterFields::DISPLAY_IN_SEARCH => ESearchEntryFieldName::DISPLAY_IN_SEARCH,
			ESearchEntryFilterFields::PARENT_ENTRY_ID => ESearchEntryFieldName::PARENT_ENTRY_ID,
			ESearchEntryFilterFields::MEDIA_TYPE => ESearchEntryFieldName::MEDIA_TYPE,
			ESearchEntryFilterFields::SOURCE_TYPE => ESearchEntryFieldName::SOURCE_TYPE,
			ESearchEntryFilterFields::LENGTH_IN_MSECS => ESearchEntryFieldName::LENGTH_IN_MSECS,
			ESearchEntryFilterFields::TYPE => ESearchEntryFieldName::ENTRY_TYPE,
			ESearchEntryFilterFields::MODERATION_STATUS => ESearchEntryFieldName::MODERATION_STATUS,
			ESearchEntryFilterFields::CREATED_AT => ESearchEntryFieldName::CREATED_AT,
			ESearchEntryFilterFields::UPDATED_AT => ESearchEntryFieldName::UPDATED_AT,
			ESearchEntryFilterFields::ACCESS_CONTROL_ID => ESearchEntryFieldName::ACCESS_CONTROL_ID,
			ESearchEntryFilterFields::USER_NAMES => ESearchEntryFieldName::USER_NAMES,
			ESearchEntryFilterFields::START_DATE => ESearchEntryFieldName::START_DATE,
			ESearchEntryFilterFields::END_DATE => ESearchEntryFieldName::END_DATE,
			ESearchEntryFilterFields::REFERENCE_ID => ESearchEntryFieldName::REFERENCE_ID,
			ESearchEntryFilterFields::ROOT_ENTRY_ID => ESearchEntryFieldName::ROOT_ID,
			ESearchEntryFilterFields::DURATION => ESearchEntryFieldName::LENGTH_IN_MSECS,
			ESearchEntryFilterFields::CATEGORIES => ESearchCategoryEntryNameItem::CATEGORY_NAMES_MAPPING_FIELD,
			ESearchEntryFilterFields::CATEGORIES_IDS => ESearchCategoryEntryIdItem::CATEGORY_IDS_MAPPING_FIELD,
			ESearchEntryFilterFields::CATEGORIES_ANCESTOR_ID => ESearchCategoryEntryFieldName::ANCESTOR_ID,
			ESearchEntryFilterFields::CATEGORIES_FULL_NAME => ESearchCategoryEntryFieldName::FULL_IDS,
			ESearchEntryFilterFields::PARTNER_SORT_VALUE => ESearchEntryFieldName::PARTNER_SORT_VALUE,
			ESearchEntryFilterFields::SEARCH_TEXT => ESearchUnifiedItem::UNIFIED,
			ESearchEntryFilterFields::FREE_TEXT => ESearchUnifiedItem::UNIFIED,
			ESearchEntryFilterFields::TOTAL_RANK => ESearchEntryOrderByFieldName::VOTES,
			ESearchEntryFilterFields::RANK => ESearchEntryOrderByFieldName::RANK,
			ESearchEntryFilterFields::VOTES => ESearchEntryOrderByFieldName::VOTES,
			ESearchEntryFilterFields::LAST_PLAYED_AT => ESearchEntryOrderByFieldName::LAST_PLAYED_AT,
			ESearchEntryFilterFields::PLAYS => ESearchEntryFieldName::PLAYS,
			ESearchEntryFilterFields::EXTERNAL_SOURCE_TYPE => ESearchEntryFieldName::EXTERNAL_SOURCE_TYPE,
		);

		if(array_key_exists($field, $fieldsMap))
		{
			return $fieldsMap[$field];
		}
		else
		{
			return null;
		}
	}

	protected function getSphinxToElasticOrderBy($field)
	{
		$fieldsMap = array(
			self::ASC.ESearchEntryFilterFields::MEDIA_TYPE,
			self::DESC.ESearchEntryFilterFields::MEDIA_TYPE,
			KalturaMediaEntryOrderBy::PLAYS_ASC => self::ASC.ESearchEntryOrderByFieldName::PLAYS,
			KalturaMediaEntryOrderBy::PLAYS_DESC => self::DESC.ESearchEntryOrderByFieldName::PLAYS,
			KalturaMediaEntryOrderBy::VIEWS_ASC => self::ASC.ESearchEntryOrderByFieldName::VIEWS,
			KalturaMediaEntryOrderBy::VIEWS_DESC => self::DESC.ESearchEntryOrderByFieldName::VIEWS,
			KalturaMediaEntryOrderBy::DURATION_ASC => self::ASC.ESearchEntryFieldName::LENGTH_IN_MSECS,
			KalturaMediaEntryOrderBy::DURATION_DESC => self::DESC.ESearchEntryFieldName::LENGTH_IN_MSECS,
			KalturaMediaEntryOrderBy::NAME_ASC => self::ASC. ESearchEntryOrderByFieldName::NAME,
			KalturaMediaEntryOrderBy::NAME_DESC => self::DESC. ESearchEntryOrderByFieldName::NAME,
			KalturaMediaEntryOrderBy::CREATED_AT_ASC => self::ASC.ESearchEntryOrderByFieldName::CREATED_AT,
			KalturaMediaEntryOrderBy::CREATED_AT_DESC => self::DESC.ESearchEntryOrderByFieldName::CREATED_AT,
			KalturaMediaEntryOrderBy::UPDATED_AT_ASC => self::ASC.ESearchEntryOrderByFieldName::UPDATED_AT,
			KalturaMediaEntryOrderBy::UPDATED_AT_DESC => self::DESC.ESearchEntryOrderByFieldName::UPDATED_AT,
			KalturaMediaEntryOrderBy::RANK_ASC => self::ASC.ESearchEntryOrderByFieldName::RANK,
			KalturaMediaEntryOrderBy::RANK_DESC => self::DESC.ESearchEntryOrderByFieldName::RANK,
			self::ASC.ESearchEntryFilterFields::TOTAL_RANK => self::ASC.ESearchEntryOrderByFieldName::VOTES,
			self::DESC.ESearchEntryFilterFields::TOTAL_RANK => self::DESC.ESearchEntryOrderByFieldName::VOTES,
			KalturaMediaEntryOrderBy::START_DATE_ASC => self::ASC.ESearchEntryOrderByFieldName::START_DATE,
			KalturaMediaEntryOrderBy::START_DATE_DESC => self::DESC.ESearchEntryOrderByFieldName::START_DATE,
			KalturaMediaEntryOrderBy::END_DATE_ASC => self::ASC.ESearchEntryOrderByFieldName::END_DATE,
			KalturaMediaEntryOrderBy::END_DATE_DESC	=> self::DESC.ESearchEntryOrderByFieldName::END_DATE,
			KalturaMediaEntryOrderBy::RECENT_ASC => self::ASC.ESearchEntryOrderByFieldName::CREATED_AT,
			KalturaMediaEntryOrderBy::RECENT_DESC => self::DESC.ESearchEntryOrderByFieldName::CREATED_AT
		);

		if(in_array($field, $fieldsMap))
		{
			return $field;
		}
		else if (array_key_exists($field, $fieldsMap))
		{
			return $fieldsMap[$field];
		}
		else
		{
			return null;
		}
	}

	public function createElasticQueryFromFilter(baseObjectFilter $filter)
	{
		$this->init();
		if(!$filter instanceof entryFilter)
		{
			throw new kCoreException(kCoreException::INVALID_QUERY);
		}

		$kEsearchOrderBy = null;
		$this->prepareEntriesCriteriaFilter($filter);
		foreach($filter->fields as $field => $fieldValue)
		{
			if(in_array($field, self::getSpecialFields()))
			{
				$this->handleSpecialFields($field, $fieldValue);
				continue;
			}

			if ($field === entryFilter::ORDER && !is_null($fieldValue) && $fieldValue!= '')
			{
				$kEsearchOrderBy = $this->getKESearchOrderBy($fieldValue);
				continue;
			}

			$fieldParts = $this->splitIntoParameters($filter, $field, $fieldValue);
			if (!$fieldParts)
			{
				continue;
			}

			list($operator, $fieldName, $fieldValue) = $fieldParts;
			$this->addingFieldPartIntoQuery($operator, $fieldName, $fieldValue);
		}

		$advanceFilterAdapter = new ESearchQueryFromAdvancedSearch();
		$advanceSearch = $advanceFilterAdapter->processAdvanceFilter($filter->getAdvancedSearch());
		if($advanceSearch)
		{
			$this->searchItems[] = $advanceSearch;
		}

		return $this->createFinalOperator($kEsearchOrderBy);
	}

	protected function createFinalOperator($kEsearchOrderBy)
	{
		$this->addNestedQueryPart();
		$operator = new ESearchOperator();
		$operator->setOperator(ESearchOperatorType::AND_OP);
		$operator->setSearchItems($this->searchItems);
		return array($operator, $kEsearchOrderBy);
	}

	protected function addingFieldPartIntoQuery($operator, $fieldName, $fieldValue)
	{
		$searchItemType = $this->getSphinxToElasticSearchItemType($operator);
		$elasticFieldName = $this->getSphinxToElasticFieldName($fieldName);
		if($elasticFieldName && $searchItemType)
		{
			$this->AddFieldPartToQuery($searchItemType, $elasticFieldName, $fieldValue);
		}
	}

	protected function splitIntoParameters($filter, $field, $fieldValue )
	{
		$fieldParts = explode(entryFilter::FILTER_PREFIX, $field, 3);
		if (count($fieldParts) < 3)
		{
			return null;
		}

		list( , $operator, $fieldName) = $fieldParts;
		if(!in_array($fieldName, static::getSupportedFields()) || is_null($fieldValue) || $fieldValue === '')
		{
			return null;
		}

		if ($fieldName === ESearchEntryFilterFields::STATUS && ($operator === baseObjectFilter::EQ ||$operator === baseObjectFilter::IN  ))
		{
			self::$validStatuses = explode(',', $fieldValue);
			return null;
		}

		$fieldValue = self::translateFieldValue($fieldName, $filter, $fieldValue);
		return array($operator, $fieldName, $fieldValue);
	}

	protected function handleSpecialFields($field, $fieldValue)
	{
		if(empty($fieldValue))
		{
			return;
		}

		switch ($field)
		{
			case self::DURATION_TYPE_FILTER_NAME:
				$this->handleDurationType($fieldValue);
				break;
			case ESearchEntryFilterFields::FREE_TEXT:
				$this->handleFreeTextField($field, $fieldValue);
				break;
			case self::EXTERNAL_SOURCE_TYPE_IN:
				$this->handleExternalSourceTypeIn($fieldValue);
				break;
			case self::EXTERNAL_SOURCE_TYPE_EQUAL:
				$this->handleExternalSourceTypeEqual($fieldValue);
				break;
		}

	}

	protected function handleExternalSourceTypeEqual($fieldValue)
	{
		$fieldValue = ExternalMediaPlugin::getAPIExternalSourceTypeFromExternalSourceSearchData($fieldValue);
		$this->addingFieldPartIntoQuery(baseObjectFilter::LIKE, ESearchEntryFilterFields::EXTERNAL_SOURCE_TYPE, $fieldValue);
	}

	protected function handleExternalSourceTypeIn($fieldValues)
	{
		$searchDataValues = explode(',', $fieldValues);
		$apiSourceTypes = array();
		foreach($searchDataValues as $searchDataValue)
		{
			$apiSourceTypes[] = ExternalMediaPlugin::getAPIExternalSourceTypeFromExternalSourceSearchData($searchDataValue);
		}

		$externalSourceTypeIn = implode(',', $apiSourceTypes);
		$this->addingFieldPartIntoQuery(baseObjectFilter::MULTI_LIKE_OR, ESearchEntryFilterFields::EXTERNAL_SOURCE_TYPE, $externalSourceTypeIn);
	}

	protected  function handleFreeTextField($field, $fieldValue)
	{
		$searchItem = null;
		$values = explode(self::NOT_OPERATOR, $fieldValue, 2);

		if (isset($values[1]) && trim($values[1]))
		{
			$searchItemNot = ESearchQueryFromAdvancedSearch::createNegativeQuery($this->createUnifiedSearchItem(trim($values[1])));
			$freeTextValue = trim($values[0]);
			if ($freeTextValue)
			{
				$searchItem = new ESearchOperator();
				$searchItem->setOperator(ESearchOperatorType::AND_OP);
				$freeTextSearchItem = $this->createUnifiedSearchItem($freeTextValue, true);
				$searchItem->setSearchItems(array($freeTextSearchItem, $searchItemNot));
			}
			else
			{
				$searchItem = $searchItemNot;
			}
		}

		elseif (isset($values[0]) && trim($values[0]))
		{
			$searchItem = $this->createUnifiedSearchItem(trim($values[0]), true);
		}

		if ($searchItem)
		{
			$this->searchItems[] = $searchItem;
		}
	}

	protected function createUnifiedSearchItem($value, $removeWildCard = false)
	{
		if ($removeWildCard)
		{
			$value = str_replace(self::WILDCARD_OPERATOR, '', $value);
		}

		$freeTextSearchExact = new ESearchUnifiedItem();
		$freeTextSearchExact->setItemType(ESearchItemType::EXACT_MATCH);
		$freeTextSearchExact->setSearchTerm($value);

		if (ESearchQueryFromAdvancedSearch::enclosedInQuotationMarks($value))
		{
			$value  = substr($value, 1, -1);
			$freeTextSearchExact->setSearchTerm($value);
			return $freeTextSearchExact;
		}
		$items = explode(self::COMMA_SEPARATOR, $value);
		$commaSeparetedSearchItems = array();
		if(count($items) > 1)
		{
			foreach ($items as $item)
			{
				$item = trim($item);
				if ($item)
				{
					$commaSeparetedSearchItems[] = $this->createUnifiedSearchItem($item);
				}
			}
		}
		$searchItem = new ESearchOperator();
		$searchItem->setOperator(ESearchOperatorType::OR_OP);
		$freeTextSearchItemPartial = new ESearchUnifiedItem();
		$freeTextSearchItemPartial->setItemType(ESearchItemType::PARTIAL);
		$freeTextSearchItemPartial->setSearchTerm($value);
		$textSearchItems = array_merge(array($freeTextSearchExact, $freeTextSearchItemPartial),$commaSeparetedSearchItems);
		$searchItem->setSearchItems($textSearchItems);

		return $searchItem;
	}

	protected function handleDurationType($fieldValue)
	{
		$searchItem = new ESearchOperator();
		$searchItem->setOperator(ESearchOperatorType::OR_OP);
		$durationTypeValues =  explode(',', $fieldValue);
		$durationTypesQueries = array();
		foreach($durationTypeValues as $durationType)
		{
			$item = new ESearchEntryItem();
			$item->setItemType(ESearchItemType::RANGE);
			$item->setFieldName(ESearchEntryFieldName::LENGTH_IN_MSECS);
			$range = new ESearchRange();
			switch ($durationType)
			{
				case durationType::NOT_AVAILABLE:
					$range->setLessThan(self::SHORT_DURATION_LOWER_BOUND);
					break;
				case durationType::SHORT:
					$range->setGreaterThanOrEqual(self::SHORT_DURATION_LOWER_BOUND);
					$range->setLessThanOrEqual(self::SHORT_DURATION_UPPER_BOUND);
					break;
				case durationType::MEDIUM:
					$range->setGreaterThan(self::SHORT_DURATION_UPPER_BOUND);
					$range->setLessThanOrEqual(self::MEDIUM_DURATION_UPPER_BOUND);
					break;
				case durationType::LONG:
					$range->setGreaterThan(self::MEDIUM_DURATION_UPPER_BOUND);
					break;
				default:
					KalturaLog::debug("Undefined duration type {$durationType}.");
					continue;
			}

			$item->setRange($range);
			$durationTypesQueries[] = $item;
		}

		if ($durationTypesQueries)
		{
			$searchItem->setSearchItems($durationTypesQueries);
			$this->searchItems[] = $searchItem;
		}
	}

	protected static function translateFieldValue($fieldName, $filter, $fieldValue)
	{
		if(in_array($fieldName, self::$puserFields))
		{
			$kuser = kuserPeer::getKuserByPartnerAndUid($filter->getPartnerSearchScope(), $fieldValue);
			if (!$kuser)
			{
				throw new KalturaAPIException(KalturaErrors::INVALID_USER_ID, $fieldValue);
			}

			$fieldValue = $kuser->getId();
		}
		else if ($fieldName === ESearchEntryFilterFields::DURATION)
		{
			$fieldValue = $fieldValue * 1000;
		}

		return $fieldValue;
	}

	protected function getKESearchOrderBy($fieldValue)
	{
		$fieldValue = $this->getSphinxToElasticOrderBy($fieldValue);
		if (!$fieldValue)
		{
			return null;
		}
		$eSearchOrderByItem = self::getESearchOrderByItem($fieldValue);
		$orderItems = array($eSearchOrderByItem);
		$kEsearchOrderBy = new ESearchOrderBy();
		$kEsearchOrderBy->setOrderItems($orderItems);
		return $kEsearchOrderBy;
	}

	protected static function getESearchOrderByItem($fieldValue)
	{
		$eSearchOrderByItem = new ESearchEntryOrderByItem();
		$eSearchOrderByItem->setSortField(substr($fieldValue,1));
		$fieldNameSortOrder = $fieldValue[0];
		if ($fieldNameSortOrder === self::DESC)
		{
			$eSearchOrderByItem->setSortOrder(ESearchSortOrder::ORDER_BY_DESC);
		}
		else if ($fieldNameSortOrder === self::ASC)
		{
			$eSearchOrderByItem->setSortOrder(ESearchSortOrder::ORDER_BY_ASC);
		}
		return $eSearchOrderByItem;
	}

	/**
	 * Set the default status to ready if other status filters are not specified
	 * @param entryFilter $filter
	 */
	protected function setDefaultStatus(entryFilter $filter)
	{
		if(!$filter->is_set(self::STATUS_EQ_FILTER) && !$filter->is_set(self::STATUS_IN_FILTER)
			&& !$filter->is_set(self::STATUS_NOT_EQ_FILTER) && !$filter->is_set(self::STATUS_NOT_IN_FILTER))
		{
			$filter->setStatusEquel(entryStatus::READY);
		}
	}

	/**
	 * Set the default moderation status to ready if other moderation status filters are not specified
	 * @param entryFilter $filter
	 */
	protected function setDefaultModerationStatus(entryFilter $filter)
	{
		if(!$filter->is_set(self::MODERATION_STATUS_EQ_FILTER) && !$filter->is_set(self::MODERATION_STATUS_IN_FILTER)
			&& !$filter->is_set(self::MODERATION_STATUS_NOT_EQ_FILTER) && !$filter->is_set(self::MODERATION_STATUS_NOT_IN_FILTER))
		{
			$moderationStatusesNotIn = array(
				entryModerationStatus::PENDING_MODERATION,
				entryModerationStatus::REJECTED);
			$filter->setModerationStatusNotIn($moderationStatusesNotIn);
		}
	}

	protected function prepareEntriesCriteriaFilter(entryFilter $filter)
	{
		if(!$filter->is_set(self::ID_EQUAL_FILTER) && !$filter->is_set(self::REDIRECT_FROM_ENTRY_ID_EQUAL_FILTER))
		{
			$this->setDefaultStatus($filter);
			$this->setDefaultModerationStatus($filter);
		}
	}

	protected function addCategoryMultiQuery($elasticFieldNames, $fieldValue, $operatorType, $statuses = array(null))
	{
		$values = $this->createValuesArray($fieldValue);
		if(count($values))
		{
			$innerSearchItems = array();
			foreach ($values as $value)
			{
				foreach ($statuses as $status)
				{
					$innerSearchItems[] = $this->getCategoryOperator($elasticFieldNames, $value, $status);
				}
			}

			$operator = $this->getEsearchOperatorByField($elasticFieldNames[0]);
			$operator->setOperator($operatorType);
			$operator->setSearchItems($innerSearchItems);
			return $operator;
		}
	}

	protected function getCategoryOperator($elasticFieldNames, $value, $status = null)
	{
		$searchItems = array();
		foreach ($elasticFieldNames as $elasticFieldName)
		{
			$searchItem = $this->addSearchItem($elasticFieldName, $value, ESearchItemType::EXACT_MATCH, false, $status);
			if ($status)
			{
				$searchItem->setCategoryEntryStatus($status);
			}
			$searchItems[] = $searchItem;
		}
		$operator = $this->getEsearchOperatorByField($elasticFieldNames[0]);
		$operator->setOperator(ESearchOperatorType::OR_OP);
		$operator->setSearchItems($searchItems);
		return $operator;
	}

	protected function getFullNameCategoryQuery($fieldValue)
	{
		$values = $this->createValuesArray($fieldValue);
		if(count($values))
		{
			$innerSearchItems = array();
			foreach ($values as $value)
			{
				$innerSearchItem = $this->getInnerSearchItemByValue($value);
				if ($innerSearchItem)
				{
					$innerSearchItems[] = $innerSearchItem;
				}
			}
			$operator = $this->getEsearchOperatorByField(ESearchCategoryEntryFieldName::FULL_IDS);
			$operator->setOperator(ESearchOperatorType::OR_OP);
			$operator->setSearchItems($innerSearchItems);
			return $operator;
		}
	}

	protected function getInnerSearchItemByValue($value)
	{
		$categoryIdDefault = category::CATEGORY_ID_THAT_DOES_NOT_EXIST;
		if(substr($value, -1) === '>') //value is parent, we should retrieve entries that doesn't belong directly to this category - but only to the sub categories.
		{
			$value = substr($value, 0, strlen($value) - 1);
			$category = categoryPeer::getByFullNameExactMatch($value);
			$categoryId = $category ? $category->getId(): $categoryIdDefault;
			return $this->addSearchItem(ESearchCategoryEntryFieldName::ANCESTOR_ID, $categoryId, ESearchItemType::EXACT_MATCH);
		}
		else	//we should retrieve entries that belong directly to this category or to a sub categories.
		{
			$category = categoryPeer::getByFullNameExactMatch($value);
			$categoryId = $category ? $category->getId(): $categoryIdDefault;
			return $this->getCategoryOperator(array(ESearchBaseCategoryEntryItem::CATEGORY_IDS_MAPPING_FIELD, ESearchCategoryEntryFieldName::ANCESTOR_ID), $categoryId);
		}
		return null;
	}


}
