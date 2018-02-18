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
		ESearchCuePointFieldName::TYPE => array('ESearchItemType::EXACT_MATCH'=> ESearchItemType::EXACT_MATCH),
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

	public static function getAllowedSearchTypesForField()
	{
		return array_merge(self::$allowed_search_types_for_field, parent::getAllowedSearchTypesForField());
	}

	protected function createSingleItemSearchQuery($boolOperator, &$cuePointBoolQuery, $allowedSearchTypes, &$queryAttributes)
	{
		$this->validateItemInput();

		switch ($this->getItemType())
		{
			case ESearchItemType::EXACT_MATCH:
				$query = $this->getCuePointExactMatchQuery($allowedSearchTypes, $queryAttributes);
				break;
			case ESearchItemType::PARTIAL:
				$query = $this->getCuePointPartialQuery($queryAttributes);
				break;
			case ESearchItemType::STARTS_WITH:
				$query = $this->getCuePointPrefixQuery($this->getFieldName(), $allowedSearchTypes, $queryAttributes);
				break;
			case ESearchItemType::EXISTS:
				$query = $this->getCuePointExistsQuery($this->getFieldName(), $allowedSearchTypes, $queryAttributes);
				break;
			case ESearchItemType::RANGE:
				$query = $this->getCuePointRangeQuery($this->getFieldName(), $allowedSearchTypes, $queryAttributes);
				break;
			default:
				KalturaLog::log("Undefined item type[".$this->getItemType()."]");
		}

		if($boolOperator == kESearchBoolQuery::MUST_KEY && !array_key_exists($this->getFieldName(), self::$field_boost_values))
			$cuePointBoolQuery->addToFilter($query);
		else
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

	public function getNestedQueryName(&$queryAttributes)
	{
		return ESearchItemDataType::CUE_POINTS.self::QUERY_NAME_DELIMITER.self::DEFAULT_GROUP_NAME.self::QUERY_NAME_DELIMITER.$queryAttributes->getNestedQueryNameIndex();
	}

	protected function getCuePointExactMatchQuery($allowedSearchTypes, &$queryAttributes)
	{
		$cuePointExactMatch = kESearchQueryManager::getExactMatchQuery($this, $this->getFieldName(), $allowedSearchTypes, $queryAttributes);
		return $cuePointExactMatch;
	}

	protected function getCuePointPartialQuery(&$queryAttributes)
	{
		$cuePointPartial = kESearchQueryManager::getPartialQuery($this, $this->getFieldName(), $queryAttributes);
		return $cuePointPartial;
	}

	protected function getCuePointPrefixQuery($allowedSearchTypes, &$queryAttributes)
	{
		$cuePointPrefix = kESearchQueryManager::getPrefixQuery($this, $this->getFieldName(), $allowedSearchTypes, $queryAttributes);
		return $cuePointPrefix;
	}

	protected function getCuePointExistsQuery($allowedSearchTypes, &$queryAttributes)
	{
		$cuePointExists = kESearchQueryManager::getExistsQuery($this, $this->getFieldName(), $allowedSearchTypes, $queryAttributes);
		return $cuePointExists;
	}

	protected function getCuePointRangeQuery($allowedSearchTypes, &$queryAttributes)
	{
		$cuePointRange = kESearchQueryManager::getRangeQuery($this, $this->getFieldName(), $allowedSearchTypes, $queryAttributes);
		return $cuePointRange;
	}

}
