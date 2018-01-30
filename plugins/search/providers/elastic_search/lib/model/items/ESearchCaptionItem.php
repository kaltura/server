<?php
/**
 * @package plugins.elasticSearch
 * @subpackage model.items
 */
class ESearchCaptionItem extends ESearchNestedObjectItem
{

	const INNER_HITS_CONFIG_KEY = 'captionInnerHitsSize';
	const NESTED_QUERY_PATH = 'caption_assets.lines';
	const HIGHLIGHT_CONFIG_KEY = 'captionMaxNumberOfFragments';

	private static $allowed_search_types_for_field = array(
		ESearchCaptionFieldName::CONTENT => array('ESearchItemType::EXACT_MATCH'=> ESearchItemType::EXACT_MATCH,'ESearchItemType::PARTIAL'=> ESearchItemType::PARTIAL, 'ESearchItemType::STARTS_WITH'=> ESearchItemType::STARTS_WITH, "ESearchItemType::EXISTS"=> ESearchItemType::EXISTS, ESearchUnifiedItem::UNIFIED),
		ESearchCaptionFieldName::START_TIME => array('ESearchItemType::RANGE'=>ESearchItemType::RANGE),
		ESearchCaptionFieldName::END_TIME => array('ESearchItemType::RANGE'=>ESearchItemType::RANGE),
	);

	private static $multiLanguageFields = array(
		ESearchCaptionFieldName::CONTENT,
	);

	protected static $field_boost_values = array(
		ESearchCaptionFieldName::CONTENT => 10,
	);

	/**
	 * @var string
	 */
	protected $searchTerm;

	/**
	 * @var ESearchCaptionFieldName
	 */
	protected $fieldName;

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
	 * @return ESearchCaptionFieldName
	 */
	public function getFieldName()
	{
		return $this->fieldName;
	}

	/**
	 * @param ESearchCaptionFieldName $fieldName
	 */
	public function setFieldName($fieldName)
	{
		$this->fieldName = $fieldName;
	}

	public static function getAllowedSearchTypesForField()
	{
		return array_merge(self::$allowed_search_types_for_field, parent::getAllowedSearchTypesForField());
	}

	public function createSingleItemSearchQuery($boolOperator, &$captionBoolQuery, $allowedSearchTypes, &$queryAttributes)
	{
		$this->validateItemInput();
		switch ($this->getItemType())
		{
			case ESearchItemType::EXACT_MATCH:
				$query = kESearchQueryManager::getExactMatchQuery($this, $this->getFieldName(), $allowedSearchTypes, $queryAttributes);
				break;
			case ESearchItemType::PARTIAL:
				$query = kESearchQueryManager::getPartialQuery($this, $this->getFieldName(), $queryAttributes);
				break;
			case ESearchItemType::STARTS_WITH:
				$query = kESearchQueryManager::getPrefixQuery($this, $this->getFieldName(), $allowedSearchTypes, $queryAttributes);
				break;
			case ESearchItemType::EXISTS:
				$query = kESearchQueryManager::getExistsQuery($this, $this->getFieldName(), $allowedSearchTypes, $queryAttributes);
				break;
			case ESearchItemType::RANGE:
				$query = kESearchQueryManager::getRangeQuery($this, $this->getFieldName(), $allowedSearchTypes, $queryAttributes);
				break;
			default:
				KalturaLog::log("Undefined item type[".$this->getItemType()."]");
		}

		if($boolOperator == kESearchBoolQuery::MUST_KEY && !array_key_exists($this->getFieldName(), self::$field_boost_values))
			$captionBoolQuery->addToFilter($query);
		else
			$captionBoolQuery->addByOperatorType($boolOperator, $query);
	}

	public function shouldAddLanguageSearch()
	{
		if(in_array($this->getFieldName(), self::$multiLanguageFields))
			return true;

		return false;
	}

	public function getItemMappingFieldsDelimiter()
	{
		return elasticSearchUtils::UNDERSCORE_FIELD_DELIMITER;
	}

	public function getNestedQueryName(&$queryAttributes)
	{
		return ESearchItemDataType::CAPTION.self::QUERY_NAME_DELIMITER.self::DEFAULT_GROUP_NAME.self::QUERY_NAME_DELIMITER.$queryAttributes->getNestedQueryNameIndex();
	}

}
