<?php

/***
 * eSearchQuery parser will take a string-like query and will transform it to an eSearchParams object so it can be used for eSearch queries.
 * The process:
 * 1. Parse a string query and validate its format - will return an tree-like array representing the eSearchParams format
 * 2. Create an eSearchParams objects from the tree-like array - traversing the array and creating the eSearchParams items as we go.
 * Example string query contains valid complex objects:
 *      NOT (~entry_id"0_xafasda" And ^entry_tags:"my tags")
 *      OR (_metadata:"{xpath:demo,METADATA_PROFILE_ID:1214,term:myTerm,metadata_Field_Id:123"})
 *      OR (entry_length_in_msecs:"[get 20 ; lt 100]'
 *          AND entry_description )
 *      OR _all:"find this in all"
 */

/**
 * @package plugins.elasticSearch
 * @subpackage model
 */
class kESearchQueryParser
{
	const NOT_OPERAND = "NOT";
	const AND_OPERAND = "AND";
	const OR_OPERAND = "OR";
	const STARTS_WITH = '^';
	const PARTIAL = '~';
	const XPATH = 'XPATH';
	const METADATA_PROFILE_ID = 'METADATA_PROFILE_ID';
	const METADATA_FIELD_ID = 'METADATA_FIELD_ID';
	const TERM = 'TERM';
	const RANGE_ITEM_MIN_LENGTH = 3;
	const RANGE_CODE_MAX_LENGTH = 3;
	const RANGE_CODE_MIN_LENGTH = 2;
	const LESS_THAN = 'LT';
	const LESS_THAN_OR_EQUAL = 'LTE';
	const GREATER_THAN = 'GT';
	const GREATER_THAN_OR_EQUAL = 'GTE';

	/**
	 * @param KalturaESearchQuery $kEsearchQuery
	 * @return KalturaESearchParams
	 * @throws kESearchException
	 */
	public static function buildKESearchParamsFromKESearchQuery($kEsearchQuery)
	{
		$kESearchParams = new KalturaESearchParams();
		// in case of a free text query (wihtout fields or brackets or special commands - a simple unified search object will be created
		if (self::isFreeTextQuery($kEsearchQuery->eSerachQuery))
			$kESearchParams->searchOperator = self::createSimpleUnifiedSearchParam($kEsearchQuery->eSerachQuery);
		else
		{
			$parsedQuery = self::parseKESearchQuery($kEsearchQuery->eSerachQuery);
			$kESearchParams->searchOperator = self::createKESearchParams($parsedQuery);
		}
		return $kESearchParams;
	}

	/**
	 * @param $eSearchQuery
	 * @return bool
	 */
	private static function isFreeTextQuery($eSearchQuery)
	{
		$aValid = array('_');
		return ctype_alnum(str_replace($aValid, '', $eSearchQuery));
	}

	/**
	 * @param $eSearchQuery
	 * @return KalturaESearchOperator
	 */
	private static function createSimpleUnifiedSearchParam($eSearchQuery)
	{
		$kESearchObject = new KalturaESearchOperator();
		$kSearchItems = new KalturaESearchBaseItemArray();
		$kSearchItem = new KalturaESearchUnifiedItem();
		$kSearchItem->searchTerm = $eSearchQuery;
		$kSearchItems[] = $kSearchItem;
		$kESearchObject->searchItems = $kSearchItems;

		return $kESearchObject;
	}

	/**
	 * parseKESearchQuery Flow - recursive method to create an tree-like array by levels for the string query.
	 * 1. locate 1st level brackets ( ) example: id and ( tags and ( day and year ))) - will find the outer brackets only
	 * 2. accumulate tokens to string in order to handle the different parts without the inner brackets parts.
	 * 3. handle the accumulated part (before / in the middle / after the brackets)
	 *
	 * @param string $query
	 * @return array
	 * @throws kESearchException
	 */
	public static function parseKESearchQuery($query)
	{
		KalturaLog::debug("Parsing $query");
		//remove starting and trailing whitespaces
		$currentQuery = trim($query);
		// find next level inner queries within ( ) brackets - TODO add informative Example and description
		$innerQueriesMatcher = '~("|\').*?\1(*SKIP)(*FAIL)|\((?:[^()]|(?R))*\)~';
		preg_match_all($innerQueriesMatcher, $currentQuery, $innerQueries);

		$innerQueriesCounter = 0;
		$cursorLocation = 0;
		$eSearchQueryResult = array();
		$partialQuery = null;
		$levelOperand = null;
		$shouldBeOperand = false; // flag flip to control if next item should be an operand or a query part.

		//token iteration on current query
		while ($cursorLocation < strlen($currentQuery))
		{
			//Iterate and get tokens until finding an inner query
			if ($currentQuery[$cursorLocation] == '(')
			{
				//extract method
				//handle accumulated text until opening brackets
				if ($partialQuery)
				{
					self::handlePartialQueryAndAddToResult($partialQuery, $shouldBeOperand, $eSearchQueryResult, $levelOperand);
					$partialQuery = null;
				}
				if (empty($innerQueries[0]))
					throw new kESearchException('Un-matching brackets', kESearchException::UNMATCHING_BRACKETS);

				//get next inner query between ( ) brackets and parse it.
				$innerQuery = preg_replace('/(^\s*\()|(\)\s*$)/', '', $innerQueries[0][$innerQueriesCounter]);
				$innerQuery = trim($innerQuery);
				if ($innerQuery)
					$eSearchQueryResult[] = self::parseKESearchQuery($innerQuery);

				//move cursor location to end of inner query
				$cursorLocation = $cursorLocation + strlen($innerQueries[0][$innerQueriesCounter]);
				$innerQueriesCounter++;
			} else
			{
				$partialQuery = $partialQuery . $currentQuery[$cursorLocation];
				$cursorLocation++;
			}
		}
		//handling last part of query after last () brackets if exists
		if ($partialQuery)
			self::handlePartialQueryAndAddToResult($partialQuery, $shouldBeOperand, $eSearchQueryResult, $levelOperand);

		return $eSearchQueryResult;
	}

	/**
	 * handlePartialQueryAndAddToResult will handle any simple string with/without operands ( e.g. "id and tags and year )
	 * 1. trim the outer whitespaces and after every : (but not within quotes)
	 * 2. split words by whitespaces and iterate them one by one and:
	 *      a. validating OPERANDS order (keeping to by the level in the query)
	 *      b. splitting field and value by : and setting it in the right place in the tree like array
	 *
	 * @param $partialQuery
	 * @param $shouldBeOperand
	 * @param $eSearchQueryResult
	 * @param $levelOperand
	 * @throws kESearchException
	 */
	private static function handlePartialQueryAndAddToResult($partialQuery, &$shouldBeOperand, &$eSearchQueryResult, $levelOperand)
	{
		//trim outer whitespace
		$partialQuery = trim($partialQuery);
		//trim whitespaces after colon (:) - but not within quotes
		$partialQuery = preg_replace("/:\s+(?=(?:[^\"]*\"[^\"]*\")*[^\"]*$)/", ":", $partialQuery);
		//split by whitespaces but ignore within quotes
		$matches = preg_split('/".*?"(*SKIP)(*FAIL)|\s+/', $partialQuery);
		$operand = null;
		foreach ($matches as $match)
		{
			if (!in_array(strtoupper($match), array(self::AND_OPERAND, self::OR_OPERAND, self::NOT_OPERAND)))
			{
				if ($shouldBeOperand)
					throw new kESearchException('Missing operand', kESearchException::MISSING_QUERY_OPERAND);

				$currentQuery = str_getcsv($match, ":", "\"");
				$eSearchQueryResult[] = $currentQuery;
				$shouldBeOperand = true;
			} else
			{
				$match = strtoupper($match);
				if (!$levelOperand)
				{
					$levelOperand = in_array($match, array(self::AND_OPERAND, self::OR_OPERAND)) ? $match : null;
				} elseif ($levelOperand != $match && $match != self::NOT_OPERAND)
					throw new kESearchException('Un-matching query operand', kESearchException::UNMATCHING_QUERY_OPERAND);
				elseif (!$shouldBeOperand && $match != self::NOT_OPERAND)
					throw new kESearchException('Illegal consecutive operands', kESearchException::CONSECUTIVE_OPERANDS_MISMATCH);
				$eSearchQueryResult[] = $match;
				$shouldBeOperand = false;
			}
		}
	}

	/**
	 * build KalturaESearchParams tree from a tree-like array representing a query by traversing the array in recursive way we will be able to build the result correctly
	 * 1. in case we a single item ( e.g. fieldName without any value - we will create a searchQueryItem with type EXIST
	 * 2. in case we have 2 items ( e.g. fieldName:value) we will create a searchQueryItem with types accordingly (PARTIAL/RANGE/EXACT_MATCH/STARTS_WITH) to the identifiers
	 *      ~fieldNamme = PARTIAL , ~fieldNamme = STARTS_WITH , fieldName:"[ $rangeType$ $rangeValue$]" = RANGE , default = EXACT_MATCH
	 * 3. in case we have more than 2 items we need to go deeper and create and eSearchOperator object to hold more than 1 eSearchObject so we will recurse.
	 *
	 * @param $queryItemArray
	 * @return KalturaESearchCaptionItem|KalturaESearchCategoryItem|KalturaESearchCuePointItem|KalturaESearchEntryItem|KalturaESearchMetadataItem|KalturaESearchOperator|KalturaESearchUserItem|null
	 * @throws kESearchException
	 */
	public static function createKESearchParams($queryItemArray)
	{
		//no query item to handle
		if (!$queryItemArray || count($queryItemArray) == 0)
			$kSearchItem = null;
		//Single item to handle - an inner query part or non-value search item
		elseif (count($queryItemArray) == 1)
			$kSearchItem = self::handleAndCreateSearchQueryItem($queryItemArray[0]);
		//double item to handle - meaning handling fieldName:fieldValue item
		elseif (count($queryItemArray) == 2 && $queryItemArray[0] != self::NOT_OPERAND && !(is_array($queryItemArray[0]) && is_array($queryItemArray[1])))
			$kSearchItem = self::handleAndCreateSearchQueryItem($queryItemArray[0], $queryItemArray[1]);
		else
			$kSearchItem = self::handleAndCreateOperatorQueryItem($queryItemArray);

		return $kSearchItem;
	}

	/**
	 * create a simple eSearchItem according to the different types and setting the type (EXACT_MATCH/PARTIAL/STARTS_WITH/RANGE) and term value accordingly.
	 * @param $fieldName
	 * @param null $fieldValue
	 * @return KalturaESearchCaptionItem|KalturaESearchCategoryItem|KalturaESearchCuePointItem|KalturaESearchEntryItem|KalturaESearchMetadataItem|KalturaESearchUserItem|null
	 */
	private static function CreateKESearchItem($fieldName, $fieldValue = null)
	{
		KalturaLog::debug("Creating Search Item for field [$fieldName] and value [$fieldValue]");

		$isPartial = self::isPartial($fieldName);
		$isStartsWith = self::isStartsWith($fieldName);

		$kSearchItem = self::getClassFromFieldName($fieldName);

		if ($kSearchItem)
		{
			if (is_null($fieldValue))
				$kSearchItem->itemType = KalturaESearchItemType::EXISTS;
			else
				self::handleAndSetTypeAndValue($kSearchItem, $fieldName, $fieldValue, $isPartial, $isStartsWith);
		}
		return $kSearchItem;
	}

	private static function isPartial(&$fieldName)
	{
		if (substr($fieldName, 0, 1) == self::PARTIAL)
		{
			$fieldName = substr($fieldName, 1);
			return true;
		}
		return false;
	}

	private static function isStartsWith(&$fieldName)
	{
		if (substr($fieldName, 0, 1) == self::STARTS_WITH)
		{
			$fieldName = substr($fieldName, 1);
			return true;
		}
		return false;
	}

	/**
	 * Handle setting the type and value - in case we create a metaData item we need to parse the value as json and handle the different fields
	 *
	 * @param $kSearchItem
	 * @param $fieldName
	 * @param $fieldValue
	 * @param $isPartial
	 * @param $isStartsWith
	 * @throws kESearchException
	 */
	private static function handleAndSetTypeAndValue($kSearchItem, $fieldName, $fieldValue, $isPartial, $isStartsWith)
	{
		if ($kSearchItem instanceof KalturaESearchMetadataItem)
			self::handleMetaDataItem($kSearchItem, $fieldName, $fieldValue, $isPartial, $isStartsWith);
		else
			self::validateAndSetTypeAndValue($kSearchItem, $fieldName, $fieldValue, $isPartial, $isStartsWith);
	}

	/**
	 * @param KalturaESearchMetadataItem $kSearchItem
	 * @param $fieldName
	 * @param $fieldValue
	 * @throws kESearchException
	 */
	private static function handleMetaDataItem(KalturaESearchMetadataItem $kSearchItem, $fieldName, $fieldValue, $isPartial, $isStartsWith)
	{
		$fieldValue = preg_replace('/(?<!")(?<!\w)(\w+)(?!")(?!\w)/', '"$1"', $fieldValue); //fix to json format.
		$valueItems = json_decode($fieldValue);
		if (!$valueItems)
			throw new kESearchException('Illegal metadata format [use json format - {xpath:value, metadata_profile_id:value, term:value}]', kESearchException::INVALID_METADATA_FORMAT);
		foreach ($valueItems as $key => $value)
		{
			if (!in_array(strtoupper($key), array(self::XPATH, self::METADATA_PROFILE_ID, self::TERM, self::METADATA_FIELD_ID)))
			{
				$data = array();
				$data['fieldName'] = $key;
				throw new kESearchException('Illegal metadata field name', kESearchException::INVALID_METADATA_FIELD, $data);
			}

			switch (strtoupper($key))
			{
				case self::XPATH:
					$kSearchItem->xpath = $value;
					break;
				case self::METADATA_PROFILE_ID:
					$kSearchItem->metadataProfileId = $value;
					break;
				case self::METADATA_FIELD_ID:
					$kSearchItem->metadataFieldId = $value;
					break;
				case self::TERM:
				{
					self::validateAndSetTypeAndValue($kSearchItem, $fieldName, $value, $isPartial, $isStartsWith);
					break;
				}
			}
		}
	}

	/**
	 * @param KalturaESearchItem $kSearchItem
	 * @param $fieldName
	 * @param $fieldValue
	 * @param $isPartial
	 * @param $isStartsWith
	 * @throws kESearchException
	 */
	private static function validateAndSetTypeAndValue(KalturaESearchItem $kSearchItem, $fieldName, $fieldValue, $isPartial, $isStartsWith)
	{
		$rangeObject = self::createRangeObject($fieldValue);
		if (($isStartsWith || $isPartial) && $rangeObject)
		{
			$data = array();
			$data['fieldName'] = $fieldName;
			$data['fieldValue'] = $fieldValue;
			throw new kESearchException("Illegal mixed search item types [$fieldName:$fieldValue]", kESearchException::INVALID_MIXED_SERACH_TYPES, $data);
		}

		if ($rangeObject)
		{
			$kSearchItem->range = $rangeObject;
			$kSearchItem->itemType = KalturaESearchItemType::RANGE;
		} else
		{
			if ($isPartial)
				$kSearchItem->itemType = KalturaESearchItemType::PARTIAL;
			if ($isStartsWith)
				$kSearchItem->itemType = KalturaESearchItemType::STARTS_WITH;

			$kSearchItem->searchTerm = $fieldValue;
		}
	}


	/**
	 * @param $fieldValue
	 * @return KalturaESearchRange|null
	 */
	private static function createRangeObject($fieldValue)
	{
		//validate we are actually in a range object
		//match range pattern [ LT 12 ; GTE 43 ]
		$rangeItems = self::validateRangeFormatAndExtractItems($fieldValue);
		if (!$rangeItems)
			return null;

		$kESearchRangeObject = new KalturaESearchRange();
		foreach ($rangeItems as $rangeItem)
		{
			if (!self::validateAndSetRangeItem($rangeItem, $kESearchRangeObject))
				return null;
		}
		return $kESearchRangeObject;
	}

	/**
	 * @param $fieldName
	 * @return KalturaESearchCaptionItem|KalturaESearchCategoryItem|KalturaESearchCuePointItem|KalturaESearchEntryItem|KalturaESearchMetadataItem|KalturaESearchUserItem|null
	 */
	private static function getClassFromFieldName($fieldName)
	{
		$kSearchItem = null;
		$fieldName = strtoupper($fieldName);
		if (defined('KalturaESearchEntryFieldName::' . $fieldName))
		{
			$kSearchItem = new KalturaESearchEntryItem();
			$kSearchItem->fieldName = constant("KalturaESearchEntryFieldName::$fieldName");
		}
		if (defined('KalturaESearchCaptionFieldName::' . $fieldName))
		{
			$kSearchItem = new KalturaESearchCaptionItem();
			$kSearchItem->fieldName = constant("KalturaESearchCaptionFieldName::$fieldName");
		}
		if (defined('KalturaESearchCategoryFieldName::' . $fieldName))
		{
			$kSearchItem = new KalturaESearchCategoryItem();
			$kSearchItem->fieldName = constant("KalturaESearchCategoryFieldName::$fieldName");
		}
		if (defined('KalturaESearchCuePointFieldName::' . $fieldName))
		{
			$kSearchItem = new KalturaESearchCuePointItem();
			$kSearchItem->fieldName = constant("KalturaESearchCuePointFieldName::$fieldName");
		}
		if (defined('KalturaESearchUserFieldName::' . $fieldName))
		{
			$kSearchItem = new KalturaESearchUserItem();
			$kSearchItem->fieldName = constant("KalturaESearchUserFieldName::$fieldName");
		}
		if ($fieldName == '_METADATA')
			$kSearchItem = new KalturaESearchMetadataItem();
		if ($fieldName == '_ALL')
			$kSearchItem = new KalturaESearchUnifiedItem();

		//default search item type
		if ($kSearchItem)
			$kSearchItem->itemType = KalturaESearchItemType::EXACT_MATCH;

		return $kSearchItem;
	}

	/**
	 * @param $arr
	 * @return KalturaESearchOperatorType|null
	 */
	private static function getLevelOperator($arr)
	{
		foreach ($arr as $part)
		{
			if ($part == self::AND_OPERAND)
				return KalturaESearchOperatorType::AND_OP;

			if ($part == self::OR_OPERAND)
				return KalturaESearchOperatorType::OR_OP;
		}
		return null;
	}


	/**
	 * @param string $rangeItem
	 * @param KalturaESearchRange $rangeESearchObject
	 * @return int|null
	 */
	private static function validateAndSetRangeItem($rangeItem, KalturaESearchRange $rangeObject)
	{
		$rangeItem = trim($rangeItem);

		// each range param must be XX followed by at least one digit
		if (strlen($rangeItem) < self::RANGE_ITEM_MIN_LENGTH)
			return false;

		$commandPart = substr($rangeItem, 0, self::RANGE_CODE_MAX_LENGTH);
		if (in_array(strtoupper($commandPart), array(self::LESS_THAN_OR_EQUAL, self::GREATER_THAN_OR_EQUAL)))
			$numberPart = substr($rangeItem, self::RANGE_CODE_MAX_LENGTH);
		else
		{
			$commandPart = substr($rangeItem, 0, self::RANGE_CODE_MIN_LENGTH);
			if (!in_array(strtoupper($commandPart), array(self::LESS_THAN, self::GREATER_THAN)))
				return false;
			$numberPart = substr($rangeItem, self::RANGE_CODE_MIN_LENGTH);
		}

		if (!is_numeric($numberPart))
			return false;

		switch (strtoupper($commandPart))
		{
			case self::LESS_THAN:
			{
				if ($rangeObject->lessThan)
					return false;
				else
					$rangeObject->lessThan = $numberPart;
				break;

			}
			case self::LESS_THAN_OR_EQUAL:
			{
				if ($rangeObject->lessThanOrEqual)
					return false;
				else
					$rangeObject->lessThanOrEqual = $numberPart;
				break;
			}
			case self::GREATER_THAN:
			{
				if ($rangeObject->greaterThan)
					return false;
				else
					$rangeObject->greaterThan = $numberPart;
				break;
			}
			case self::GREATER_THAN_OR_EQUAL:
			{
				if ($rangeObject->greaterThanOrEqual)
					return false;
				else
					$rangeObject->greaterThanOrEqual = $numberPart;
				break;
			}
			default:
				return false;
		}

		return true;
	}

	/**
	 * @param $fieldValue
	 * @param $out
	 * Range format example - "[ LT 20 ; GTE 10 ]"
	 * @return array|null
	 */
	private static function validateRangeFormatAndExtractItems($value)
	{
		//validate outer [ ] brackets first location exists
		$a = '/\s*\[.*\]\s*/';
		preg_match($a, $value, $out);
		if (empty($out))
			return null;

		//check if we have more characters out of [ ] brackets
		$other = preg_replace($a, null, $value, 1);
		if ($other)
			return null;

		//get within [ ] brackets
		preg_match('/\[([^)(]+)\]/', $out[0], $out);
		if (empty($out) || empty($out[1]))
			return null;

		$rangeCommand = preg_replace('/\s+/', '', $out[1]);
		$rangeItems = explode(';', $rangeCommand);
		return $rangeItems;
	}

	/**
	 * @param $queryItem
	 * @param  $queryItemValue
	 * @return KalturaESearchCaptionItem|KalturaESearchCategoryItem|KalturaESearchCuePointItem|KalturaESearchEntryItem|KalturaESearchMetadataItem|KalturaESearchOperator|KalturaESearchUserItem|null
	 * @throws kESearchException
	 */
	private static function handleAndCreateSearchQueryItem($queryItem, $queryItemValue = null)
	{
		$kSearchItem = null;
		//If query item is array create inner query search items
		if (is_array($queryItem))
		{
			$kSearchItem = self::createKSearchOperatorObject();
			$innerObject = self::createKESearchParams($queryItem);
			if ($innerObject)
			{
				$innerObjectArray = new KalturaESearchBaseItemArray();
				$innerObjectArray[] = $innerObject;
				$kSearchItem->searchItems = $innerObjectArray;
			}
		} else
		{
			//create a single search item only with field name existance
			$kSearchItem = self::CreateKESearchItem($queryItem, $queryItemValue);
			if (!isset($kSearchItem))
			{
				$data = array();
				$data['fieldName'] = $queryItem;
				throw new kESearchException('Illegal query field name', kESearchException::INVALID_FIELD_NAME, $data);
			}
		}
		return $kSearchItem;
	}

	/**
	 * Create an eSearchOperator object to hold all the items of the same level ( allowed is the same AND/OR OperatorType for all item on the same level but NOT operator is allowed)
	 * 1. get the level operator and create the KSearchOperatorObject container
	 * 2. iterate through the level queryItemsArray and create the objects:
	 *  a. in case we create a not operator we will recurse to the createKESearchParams method since we need to create a deeper level in the tree
	 *  b. we will ignore the other operands in the same level (since they were already verified)
	 *     and we will call createKESearchParams for the items (which can be simple types or hold a deeper level for query commmands.
	 * @param $queryItemArray
	 * @return KalturaESearchOperator
	 */
	private static function handleAndCreateOperatorQueryItem($queryItemArray)
	{
		$kSearchItem = self::createKSearchOperatorObject(self::getLevelOperator($queryItemArray));
		$queryArrayIndex = 0;
		$innerObjects = new KalturaESearchBaseItemArray();
		while ($queryArrayIndex < count($queryItemArray))
		{
			if ($queryItemArray[$queryArrayIndex] == self::NOT_OPERAND)
			{
				$kNotItem = self::createKSearchOperatorObject(KalturaESearchOperatorType::NOT_OP);
				$innerObject = self::createKESearchParams($queryItemArray[$queryArrayIndex + 1]);
				if ($innerObject)
				{
					$kNotItemInnerObject = new KalturaESearchBaseItemArray();
					$kNotItemInnerObject[] = $innerObject;
					$kNotItem->searchItems = $kNotItemInnerObject;
				}
				$innerObjects[] = $kNotItem;
				$queryArrayIndex = $queryArrayIndex + 2;
			} else
			{
				if ($queryItemArray[$queryArrayIndex] != self::OR_OPERAND && $queryItemArray[$queryArrayIndex] != self::AND_OPERAND)
				{
					$innerObject = self::createKESearchParams($queryItemArray[$queryArrayIndex]);
					if ($innerObject)
						$innerObjects[] = $innerObject;
				}
				$queryArrayIndex++;
			}
		}

		$kSearchItem->searchItems = $innerObjects;
		return $kSearchItem;
	}

	/**
	 * @param $operator
	 * @return KalturaESearchOperator
	 */
	private static function createKSearchOperatorObject($operator = null)
	{
		$kSearchItem = new KalturaESearchOperator();
		if (!$operator)
			$operator = KalturaESearchOperatorType::AND_OP;
		$kSearchItem->operator = $operator;
		return $kSearchItem;
	}
}