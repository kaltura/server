<?php
/**
 * @package plugins.elasticSearch
 * @subpackage model.items
 */
abstract class ESearchBaseCategoryEntryItem extends ESearchItem
{

	const CATEGORY_SEPARATOR = '>';
	const CATEGORY_IDS_MAPPING_FIELD = 'categories_ids';
	const CATEGORY_NAMES_MAPPING_FIELD = 'categories_names';

	/**
	 * @var ESearchCategoryEntryFieldName
	 */
	protected $fieldName;

	/**
	 * @var string
	 */
	protected $searchTerm;

	/**
	 * @var int
	 */
	protected $categoryEntryStatus;

	private static $allowed_search_types_for_field = array(
		ESearchCategoryEntryFieldName::ID => array('ESearchItemType::EXACT_MATCH'=> ESearchItemType::EXACT_MATCH, 'ESearchItemType::EXISTS' => ESearchItemType::EXISTS),
		ESearchCategoryEntryFieldName::NAME => array('ESearchItemType::EXACT_MATCH'=> ESearchItemType::EXACT_MATCH, 'ESearchItemType::STARTS_WITH'=> ESearchItemType::STARTS_WITH, ESearchUnifiedItem::UNIFIED),
		ESearchCategoryEntryFieldName::FULL_IDS => array('ESearchItemType::EXACT_MATCH'=> ESearchItemType::EXACT_MATCH, 'ESearchItemType::STARTS_WITH'=> ESearchItemType::STARTS_WITH),
		ESearchCategoryEntryFieldName::ANCESTOR_ID => array('ESearchItemType::EXACT_MATCH'=> ESearchItemType::EXACT_MATCH),
		ESearchCategoryEntryFieldName::ANCESTOR_NAME => array('ESearchItemType::EXACT_MATCH'=> ESearchItemType::EXACT_MATCH, 'ESearchItemType::STARTS_WITH'=> ESearchItemType::STARTS_WITH, ESearchUnifiedItem::UNIFIED),
	);

	/**
	 * @return ESearchCategoryEntryFieldName
	 */
	public function getFieldName()
	{
		return $this->fieldName;
	}

	/**
	 * @param ESearchCategoryEntryFieldName $fieldName
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
	 * @return int
	 */
	public function getCategoryEntryStatus()
	{
		return $this->categoryEntryStatus;
	}

	/**
	 * @param int $categoryEntryStatus
	 */
	public function setCategoryEntryStatus($categoryEntryStatus)
	{
		$this->categoryEntryStatus = $categoryEntryStatus;
	}

	public static function getAllowedSearchTypesForField()
	{
		return array_merge(self::$allowed_search_types_for_field, parent::getAllowedSearchTypesForField());
	}

	public static function createSearchQuery($eSearchItemsArr, $boolOperator, &$queryAttributes, $eSearchOperatorType = null)
	{
		$categoryEntryQuery = array();
		$allowedSearchTypes = ESearchBaseCategoryEntryItem::getAllowedSearchTypesForField();
		$queryAttributes->setScopeToGlobal();
		foreach ($eSearchItemsArr as $categoryEntrySearchItem)
		{
			self::getSingleItemSearchQuery($categoryEntrySearchItem, $categoryEntryQuery, $allowedSearchTypes, $queryAttributes);
		}

		return $categoryEntryQuery;
	}

	public static function getSingleItemSearchQuery($categoryEntrySearchItem, &$categoryEntryQuery, $allowedSearchTypes, &$queryAttributes)
	{
		$categoryEntrySearchItem->validateItemInput();
		switch ($categoryEntrySearchItem->getItemType())
		{
			case ESearchItemType::EXACT_MATCH:
				$categoryEntryQuery[] = static::getCategoryEntryExactMatchQuery($categoryEntrySearchItem, $allowedSearchTypes, $queryAttributes);
				break;
			case ESearchItemType::STARTS_WITH:
				$categoryEntryQuery[] = static::getCategoryEntryPrefixQuery($categoryEntrySearchItem, $allowedSearchTypes, $queryAttributes);
				break;
			case ESearchItemType::EXISTS:
				$categoryEntryQuery[] = static::getCategoryEntryExistsQuery($categoryEntrySearchItem, $allowedSearchTypes, $queryAttributes);
				break;
			default:
				KalturaLog::log("Undefined item type[".$categoryEntrySearchItem->getItemType()."]");
		}
	}

	protected static function getCategoryEntryExactMatchQuery($categoryEntrySearchItem, $allowedSearchTypes, &$queryAttributes)
	{
		$categoryEntrySearchItem->transformData();
		return kESearchQueryManager::getExactMatchQuery($categoryEntrySearchItem, $categoryEntrySearchItem->getFieldName(), $allowedSearchTypes, $queryAttributes);
	}

	protected static function getCategoryEntryPrefixQuery($categoryEntrySearchItem, $allowedSearchTypes, &$queryAttributes)
	{
		$categoryEntrySearchItem->transformData();
		return kESearchQueryManager::getPrefixQuery($categoryEntrySearchItem, $categoryEntrySearchItem->getFieldName(), $allowedSearchTypes, $queryAttributes);
	}

	protected static function getCategoryEntryExistsQuery($categoryEntrySearchItem, $allowedSearchTypes, &$queryAttributes)
	{

	}

	public function shouldAddLanguageSearch()
	{
		return false;
	}

	public function getItemMappingFieldsDelimiter()
	{

	}

}
