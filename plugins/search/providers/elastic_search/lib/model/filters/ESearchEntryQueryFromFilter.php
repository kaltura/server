<?php

class ESearchEntryQueryFromFilter extends ESearchQueryFromFilter
{
	const ASC = '+';
	const DESC = '-';

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
		ESearchEntryFilterFields::FREE_TEXT
	);


	protected static function getSupportedFields()
	{
		return self::$supportedSearchFields;
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
			ESearchEntryFilterFields::PLAYS => ESearchEntryOrderByFieldName::PLAYS,
			ESearchEntryFilterFields::VIEWS => ESearchEntryOrderByFieldName::VIEWS
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

	protected function getMediaEntryElasticOrderBy($field)
	{	//TODO: VALIDATE ALL OF THEM
		$fieldsMap = array(
			KalturaMediaEntryOrderBy::MEDIA_TYPE_ASC,
			KalturaMediaEntryOrderBy::MEDIA_TYPE_DESC,
			KalturaMediaEntryOrderBy::PLAYS_ASC,
			KalturaMediaEntryOrderBy::PLAYS_DESC,
			KalturaMediaEntryOrderBy::VIEWS_ASC,
			KalturaMediaEntryOrderBy::VIEWS_DESC,
			KalturaMediaEntryOrderBy::DURATION_ASC,
			KalturaMediaEntryOrderBy::DURATION_DESC,
			KalturaMediaEntryOrderBy::NAME_ASC,
			KalturaMediaEntryOrderBy::NAME_DESC,
			KalturaMediaEntryOrderBy::CREATED_AT_ASC,
			KalturaMediaEntryOrderBy::CREATED_AT_DESC,
			KalturaMediaEntryOrderBy::UPDATED_AT_ASC,
			KalturaMediaEntryOrderBy::UPDATED_AT_DESC,
			KalturaMediaEntryOrderBy::START_DATE_ASC,
			KalturaMediaEntryOrderBy::START_DATE_DESC,
			KalturaMediaEntryOrderBy::END_DATE_ASC,
			KalturaMediaEntryOrderBy::END_DATE_DESC,
			KalturaMediaEntryOrderBy::PARTNER_SORT_VALUE_ASC,
			KalturaMediaEntryOrderBy::PARTNER_SORT_VALUE_DESC,
		);

		if(in_array($field, $fieldsMap))
		{
			return $field;
		}
		else
		{
			return null;
		}
	}

	public function createElasticQueryFromFilter(baseObjectFilter $filter)
	{
		$this->init();
		$kEsearchOrderBy = null;
		foreach($filter->fields as $field => $fieldValue)
		{
			if ($field === entryFilter::ORDER && !is_null($fieldValue) && $fieldValue!= '')
			{
				$kEsearchOrderBy = $this->getKESearchOrderBy($fieldValue);
				continue;
			}
			list($operator, $fieldName, $fieldValue, $shouldContinue) = $this->splitIntoParameters($filter, $field, $fieldValue);
			if ($shouldContinue)
			{
				continue;
			}
			$this->addingFieldPartIntoQuery($operator, $fieldName, $fieldValue);
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
		list( , $operator, $fieldName) = $fieldParts;

		list($operator, $fieldName) = self::handlingFreeTextField($field, $operator, $fieldName);
		if(!in_array($fieldName, static::getSupportedFields()) || is_null($fieldValue) || $fieldValue === '')
		{
			return array(null , null, null, true);
		}
		if ($fieldName === ESearchEntryFilterFields::STATUS && ($operator === baseObjectFilter::EQ ||$operator === baseObjectFilter::IN  ))
		{
			self::$validStatuses = explode(',',$fieldValue);
			return array(null , null, null, true);
		}
		$fieldValue = self::changeFieldValueByPuserIdAndDuration($fieldName, $filter, $fieldValue);
		return array($operator, $fieldName, $fieldValue, false);
	}

	protected static function handlingFreeTextField($field, $operator, $fieldName)
	{
		if ($field === ESearchEntryFilterFields::FREE_TEXT)
		{
			$operator = baseObjectFilter::IN;
			$fieldName = $field;
		}
		return array($operator, $fieldName);
	}

	protected static function changeFieldValueByPuserIdAndDuration($fieldName, $filter, $fieldValue)
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
		if (!$this->getMediaEntryElasticOrderBy($fieldValue))
		{
			return null;
		}
		$eSearchOrderByItem = new ESearchEntryOrderByItem();
		$sortField = $this->getSphinxToElasticFieldName(substr($fieldValue,1));
		$eSearchOrderByItem->setSortField($sortField);
		$fieldNameSortOrder = $fieldValue[0];
		if ($fieldNameSortOrder === self::DESC)
		{
			$eSearchOrderByItem->setSortOrder(ESearchSortOrder::ORDER_BY_DESC);
		}
		else if ($fieldNameSortOrder === self::ASC)
		{
			$eSearchOrderByItem->setSortOrder(ESearchSortOrder::ORDER_BY_ASC);
		}
		$orderItems = array($eSearchOrderByItem);
		$kEsearchOrderBy = new ESearchOrderBy();
		$kEsearchOrderBy->setOrderItems($orderItems);
		return $kEsearchOrderBy;

	}
}