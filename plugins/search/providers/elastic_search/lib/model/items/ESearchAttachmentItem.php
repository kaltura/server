<?php
/**
 * @package plugins.elasticSearch
 * @subpackage model.items
 */
class ESearchAttachmentItem extends ESearchNestedObjectItem
{

	const INNER_HITS_CONFIG_KEY = 'attachmentInnerHitsSize';
	const NESTED_QUERY_PATH = 'attachment_assets';
	const HIGHLIGHT_CONFIG_KEY = 'attachmentMaxNumberOfFragments';

	private static $allowed_search_types_for_field = array(
		ESearchAttachmentFieldName::CONTENT => array('ESearchItemType::EXACT_MATCH'=> ESearchItemType::EXACT_MATCH,'ESearchItemType::PARTIAL'=> ESearchItemType::PARTIAL, 'ESearchItemType::STARTS_WITH'=> ESearchItemType::STARTS_WITH, "ESearchItemType::EXISTS"=> ESearchItemType::EXISTS, ESearchUnifiedItem::UNIFIED),
		ESearchAttachmentFieldName::FILE_NAME => array('ESearchItemType::EXACT_MATCH'=> ESearchItemType::EXACT_MATCH,'ESearchItemType::PARTIAL'=> ESearchItemType::PARTIAL),
		ESearchAttachmentFieldName::PAGE_NUMBER => array('ESearchItemType::RANGE'=>ESearchItemType::RANGE),
		ESearchAttachmentFieldName::ASSET_ID => array('ESearchItemType::EXACT_MATCH'=> ESearchItemType::EXACT_MATCH)
	);


	protected static $field_boost_values = array(
		ESearchAttachmentFieldName::CONTENT => 10,
	);

	protected static $searchHistoryFields = array(
		ESearchAttachmentFieldName::CONTENT,
	);

	/**
	 * @var string
	 */
	protected $searchTerm;

	/**
	 * @var ESearchAttachmentFieldName
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
	 * @return ESearchAttachmentFieldName
	 */
	public function getFieldName()
	{
		return $this->fieldName;
	}

	/**
	 * @param ESearchAttachmentFieldName $fieldName
	 */
	public function setFieldName($fieldName)
	{
		$this->fieldName = $fieldName;
	}

	public static function getAllowedSearchTypesForField()
	{
		return array_merge(self::$allowed_search_types_for_field, parent::getAllowedSearchTypesForField());
	}

	public function createSingleItemSearchQuery($boolOperator, &$attachmentBoolQuery, $allowedSearchTypes, &$queryAttributes)
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
		{
			$attachmentBoolQuery->addToFilter($query);
		}
		else
		{
			$attachmentBoolQuery->addByOperatorType($boolOperator, $query);
		}
	}

	public function shouldAddLanguageSearch()
	{
		return false;
	}

	public function getItemMappingFieldsDelimiter()
	{
		return elasticSearchUtils::UNDERSCORE_FIELD_DELIMITER;
	}

	public function getNestedQueryName(&$queryAttributes)
	{
		return ESearchItemDataType::ATTACHMENTS.self::QUERY_NAME_DELIMITER.self::DEFAULT_GROUP_NAME.self::QUERY_NAME_DELIMITER.$queryAttributes->getNestedQueryNameIndex();
	}

	protected static function getNestedSortOrder()
	{
		return array(ESearchAttachmentFieldName::PAGE_NUMBER);
	}

}
