<?php
/**
 * @package plugins.elasticSearch
 * @subpackage model.items
 */
class ESearchCuePointItem extends ESearchNestedObjectItem
{

	const INNER_HITS_CONFIG_KEY = 'cuePointsInnerHitsSize';
	const NESTED_QUERY_PATH = 'cue_points';
	const HIGHLIGHT_CONFIG_KEY = 'cuepointMaxNumberOfFragments';

	/**
	 * @var ESearchCuePointFieldName
	 */
	protected $fieldName;

	/**
	 * @var string
	 */
	protected $searchTerm;

	/**
	 * @var CuePointType
	 */
	protected $cuePointType;

	private static $allowed_search_types_for_field = array(
		ESearchCuePointFieldName::ID => array('ESearchItemType::EXACT_MATCH'=> ESearchItemType::EXACT_MATCH, ESearchUnifiedItem::UNIFIED),
		ESearchCuePointFieldName::NAME => array('ESearchItemType::EXACT_MATCH'=> ESearchItemType::EXACT_MATCH, 'ESearchItemType::PARTIAL'=> ESearchItemType::PARTIAL, 'ESearchItemType::STARTS_WITH'=> ESearchItemType::STARTS_WITH, "ESearchItemType::EXISTS"=> ESearchItemType::EXISTS, ESearchUnifiedItem::UNIFIED),
		ESearchCuePointFieldName::TEXT => array('ESearchItemType::EXACT_MATCH'=> ESearchItemType::EXACT_MATCH, 'ESearchItemType::PARTIAL'=> ESearchItemType::PARTIAL, 'ESearchItemType::STARTS_WITH'=> ESearchItemType::STARTS_WITH, "ESearchItemType::EXISTS"=> ESearchItemType::EXISTS, ESearchUnifiedItem::UNIFIED),
		ESearchCuePointFieldName::TAGS => array('ESearchItemType::EXACT_MATCH'=> ESearchItemType::EXACT_MATCH, 'ESearchItemType::PARTIAL'=> ESearchItemType::PARTIAL, 'ESearchItemType::STARTS_WITH'=> ESearchItemType::STARTS_WITH, "ESearchItemType::EXISTS"=> ESearchItemType::EXISTS, ESearchUnifiedItem::UNIFIED),
		ESearchCuePointFieldName::START_TIME => array("ESearchItemType::EXISTS"=> ESearchItemType::EXISTS, 'ESearchItemType::RANGE'=>ESearchItemType::RANGE),
		ESearchCuePointFieldName::END_TIME => array("ESearchItemType::EXISTS"=> ESearchItemType::EXISTS, 'ESearchItemType::RANGE'=>ESearchItemType::RANGE),
		ESearchCuePointFieldName::SUB_TYPE => array('ESearchItemType::EXACT_MATCH'=> ESearchItemType::EXACT_MATCH, "ESearchItemType::EXISTS"=> ESearchItemType::EXISTS),
		ESearchCuePointFieldName::QUESTION => array('ESearchItemType::EXACT_MATCH'=> ESearchItemType::EXACT_MATCH, 'ESearchItemType::PARTIAL'=> ESearchItemType::PARTIAL, 'ESearchItemType::STARTS_WITH'=> ESearchItemType::STARTS_WITH, "ESearchItemType::EXISTS"=> ESearchItemType::EXISTS, ESearchUnifiedItem::UNIFIED),
		ESearchCuePointFieldName::ANSWERS => array('ESearchItemType::EXACT_MATCH'=> ESearchItemType::EXACT_MATCH, 'ESearchItemType::PARTIAL'=> ESearchItemType::PARTIAL, 'ESearchItemType::STARTS_WITH'=> ESearchItemType::STARTS_WITH, "ESearchItemType::EXISTS"=> ESearchItemType::EXISTS, ESearchUnifiedItem::UNIFIED),
		ESearchCuePointFieldName::HINT => array('ESearchItemType::EXACT_MATCH'=> ESearchItemType::EXACT_MATCH, 'ESearchItemType::PARTIAL'=> ESearchItemType::PARTIAL, 'ESearchItemType::STARTS_WITH'=> ESearchItemType::STARTS_WITH, "ESearchItemType::EXISTS"=> ESearchItemType::EXISTS, ESearchUnifiedItem::UNIFIED),
		ESearchCuePointFieldName::EXPLANATION => array('ESearchItemType::EXACT_MATCH'=> ESearchItemType::EXACT_MATCH, 'ESearchItemType::PARTIAL'=> ESearchItemType::PARTIAL, 'ESearchItemType::STARTS_WITH'=> ESearchItemType::STARTS_WITH, "ESearchItemType::EXISTS"=> ESearchItemType::EXISTS, ESearchUnifiedItem::UNIFIED),
	);

	protected static $field_boost_values = array(
		ESearchCuePointFieldName::ID => 50,
		ESearchCuePointFieldName::NAME => 50,
		ESearchCuePointFieldName::TEXT => 50,
		ESearchCuePointFieldName::TAGS => 50,
		ESearchCuePointFieldName::QUESTION => 50,
		ESearchCuePointFieldName::ANSWERS => 50,
		ESearchCuePointFieldName::HINT => 50,
		ESearchCuePointFieldName::EXPLANATION => 50,
	);

	private static $multiLanguageFields = array();

	/**
	 * @return ESearchCuePointFieldName
	 */
	public function getFieldName()
	{
		return $this->fieldName;
	}

	/**
	 * @param ESearchCuePointFieldName $fieldName
	 */
	public function setFieldName($fieldName)
	{
		$this->fieldName = $fieldName;
	}

	/**
	 * @return string
	 */
	public function getSearchTerm()
	{
		return $this->searchTerm;
	}

	/**
	 * @param string $searchTerm
	 */
	public function setSearchTerm($searchTerm)
	{
		$this->searchTerm = $searchTerm;
	}

	/**
	 * @return CuePointType
	 */
	public function getCuePointType()
	{
		return $this->cuePointType;
	}

	/**
	 * @param CuePointType $cuePointType
	 */
	public function setCuePointType($cuePointType)
	{
		$this->cuePointType = $cuePointType;
	}

	public static function getAllowedSearchTypesForField()
	{
		return array_merge(self::$allowed_search_types_for_field, parent::getAllowedSearchTypesForField());
	}

	/**
	 * @param $eSearchItemsArr
	 * @param $boolOperator
	 * @param ESearchQueryAttributes $queryAttributes
	 * @param null $eSearchOperatorType
	 * @return array
	 */
	public static function createSearchQuery($eSearchItemsArr, $boolOperator, &$queryAttributes, $eSearchOperatorType = null)
	{
		return self::createNestedQueryForItems($eSearchItemsArr, $boolOperator, $queryAttributes);
	}

	public static function createSingleItemSearchQuery($cuePointSearchItem, $boolOperator, &$cuePointBoolQuery, $allowedSearchTypes, &$queryAttributes)
	{
		$cuePointSearchItem->validateItemInput();

		switch ($cuePointSearchItem->getItemType())
		{
			case ESearchItemType::EXACT_MATCH:
				$query = self::getCuePointExactMatchQuery($cuePointSearchItem, $allowedSearchTypes, $queryAttributes);
				break;
			case ESearchItemType::PARTIAL:
				$query = self::getCuePointPartialQuery($cuePointSearchItem, $cuePointSearchItem->getFieldName(), $queryAttributes);
				break;
			case ESearchItemType::STARTS_WITH:
				$query = self::getCuePointPrefixQuery($cuePointSearchItem, $cuePointSearchItem->getFieldName(), $allowedSearchTypes, $queryAttributes);
				break;
			case ESearchItemType::EXISTS:
				$query = self::getCuePointExistsQuery($cuePointSearchItem, $cuePointSearchItem->getFieldName(), $allowedSearchTypes, $queryAttributes);
				break;
			case ESearchItemType::RANGE:
				$query = self::getCuePointRangeQuery($cuePointSearchItem, $cuePointSearchItem->getFieldName(), $allowedSearchTypes, $queryAttributes);
				break;
			case null:
				$query = self::getCuePointItemTypeQuery($cuePointSearchItem, $queryAttributes);
				break;
			default:
				KalturaLog::log("Undefined item type[".$cuePointSearchItem->getItemType()."]");
		}

		$cuePointBoolQuery->addByOperatorType($boolOperator, $query);
	}

	public function shouldAddLanguageSearch()
	{
		if(in_array($this->getFieldName(), self::$multiLanguageFields))
			return true;

		return false;
	}

	public function getItemMappingFieldsDelimiter()
	{

	}

	public function getNestedQueryNames()
	{
		$queryNames = array();
		if ($this->getCuePointType())
		{
			$apiCuePointType = kPluginableEnumsManager::coreToApi('CuePointType', $this->getCuePointType());
			$queryName = ESearchItemDataType::CUE_POINTS . self::QUERY_NAME_DELIMITER . $apiCuePointType;

			if ($apiCuePointType == ThumbCuePointPlugin::getApiValue(ThumbCuePointType::THUMB))
			{
				$refClass = new ReflectionClass('KalturaThumbCuePointSubType');
				$subTypes = $refClass->getConstants();
				foreach ($subTypes as $subType)
					$queryNames[] = $queryName . self::SUBTYPE_DELIMITER . $subType;
			} else
				$queryNames[] = $queryName;
		} else
		{
			$queryNames[] = ESearchItemDataType::CUE_POINTS . self::QUERY_NAME_DELIMITER . self::DEFAULT_GROUP_NAME;
		}

		return $queryNames;
	}

	protected static function getCuePointExactMatchQuery($cuePointSearchItem, $allowedSearchTypes, &$queryAttributes)
	{
		$cuePointExactMatch = kESearchQueryManager::getExactMatchQuery($cuePointSearchItem, $cuePointSearchItem->getFieldName(), $allowedSearchTypes, $queryAttributes);
		if($cuePointSearchItem->getCuePointType())
		{
			$cuePointExactMatch = self::addFilterByTypeToQuery($cuePointSearchItem->getCuePointType(), $cuePointExactMatch, $queryAttributes->getObjectSubType());
		}

		return $cuePointExactMatch;
	}

	protected static function getCuePointPartialQuery($cuePointSearchItem, $fieldName, &$queryAttributes)
	{
		$cuePointPartial = kESearchQueryManager::getPartialQuery($cuePointSearchItem, $fieldName, $queryAttributes);
		if($cuePointSearchItem->getCuePointType())
		{
			$cuePointTypeQuery = new kESearchTermQuery(ESearchCuePointFieldName::TYPE, $cuePointSearchItem->getCuePointType());
			if ($queryAttributes->getObjectSubType())
			{
				$cuePointSubTypeQuery = new kESearchTermQuery(ESearchCuePointFieldName::SUB_TYPE, $queryAttributes->getObjectSubType());
				$cuePointPartial->addToFilter($cuePointSubTypeQuery);
			}
			$cuePointPartial->addToFilter($cuePointTypeQuery);
		}

		return $cuePointPartial;
	}

	protected static function getCuePointPrefixQuery($cuePointSearchItem, $fieldName, $allowedSearchTypes, &$queryAttributes)
	{
		$cuePointPrefix = kESearchQueryManager::getPrefixQuery($cuePointSearchItem, $fieldName, $allowedSearchTypes, $queryAttributes);
		if($cuePointSearchItem->getCuePointType())
		{
			$cuePointPrefix = self::addFilterByTypeToQuery($cuePointSearchItem->getCuePointType(), $cuePointPrefix, $queryAttributes->getObjectSubType());
		}

		return $cuePointPrefix;
	}

	protected static function getCuePointExistsQuery($cuePointSearchItem, $fieldName, $allowedSearchTypes, &$queryAttributes)
	{
		$cuePointExists = kESearchQueryManager::getExistsQuery($cuePointSearchItem, $fieldName, $allowedSearchTypes, $queryAttributes);
		if($cuePointSearchItem->getCuePointType())
		{
			$cuePointExists = self::addFilterByTypeToQuery($cuePointSearchItem->getCuePointType(), $cuePointExists, $queryAttributes->getObjectSubType());
		}

		return $cuePointExists;
	}

	protected static function getCuePointRangeQuery($cuePointSearchItem, $fieldName, $allowedSearchTypes, &$queryAttributes)
	{
		$cuePointRange = kESearchQueryManager::getRangeQuery($cuePointSearchItem, $fieldName, $allowedSearchTypes, $queryAttributes);
		if($cuePointSearchItem->getCuePointType())
		{
			$cuePointRange = self::addFilterByTypeToQuery($cuePointSearchItem->getCuePointType(), $cuePointRange, $queryAttributes->getObjectSubType());
		}

		return $cuePointRange;
	}

	private static function addFilterByTypeToQuery($cuePointType, &$query, $subtype = null)
	{
		$cuePointTypeQuery = new kESearchTermQuery(ESearchCuePointFieldName::TYPE, $cuePointType);
		$boolQuery = new kESearchBoolQuery();
		$boolQuery->addToFilter($cuePointTypeQuery);
		$boolQuery->addToMust($query);
		if ($subtype)
		{
			$cuePointSubTypeQuery = new kESearchTermQuery(ESearchCuePointFieldName::SUB_TYPE, $subtype);
			$boolQuery->addToFilter($cuePointSubTypeQuery);
		}
		return $boolQuery;
	}

	protected function validateItemInput()
	{
		if(!$this->shouldQueryByType())
		{
			parent::validateItemInput();
		}
	}

	private function shouldQueryByType()
	{
		return !($this->getSearchTerm() || $this->getItemType() || $this->getRange() || $this->getFieldName() || !$this->getCuePointType());
	}

	private static function getCuePointItemTypeQuery($cuePointSearchItem, &$queryAttributes)
	{
		$cuePointTypeQuery = new kESearchTermQuery(ESearchCuePointFieldName::TYPE, $cuePointSearchItem->getCuePointType());

		if ($queryAttributes->getObjectSubType())
		{
			$boolQuery = new kESearchBoolQuery();
			$boolQuery->addToFilter($cuePointTypeQuery);
			$cuePointSubTypeQuery = new kESearchTermQuery(ESearchCuePointFieldName::SUB_TYPE, $queryAttributes->getObjectSubType());
			$boolQuery->addToFilter($cuePointSubTypeQuery);
			return $boolQuery;
		}

		return $cuePointTypeQuery;
	}
}