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

	private static $allowed_search_types_for_field = array();

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
			$categoryEntrySearchItem->getSingleItemSearchQuery($categoryEntryQuery, $allowedSearchTypes, $queryAttributes);
		}

		return $categoryEntryQuery;
	}

	public function getSingleItemSearchQuery(&$categoryEntryQuery, $allowedSearchTypes, &$queryAttributes)
	{
		$this->validateItemInput();
		switch ($this->getItemType())
		{
			case ESearchItemType::EXACT_MATCH:
				$categoryEntryQuery[] = $this->getCategoryEntryExactMatchQuery($allowedSearchTypes, $queryAttributes);
				break;
			case ESearchItemType::STARTS_WITH:
				$categoryEntryQuery[] = $this->getCategoryEntryPrefixQuery($allowedSearchTypes, $queryAttributes);
				break;
			case ESearchItemType::EXISTS:
				$categoryEntryQuery[] = $this->getCategoryEntryExistsQuery($allowedSearchTypes, $queryAttributes);
				break;
			default:
				KalturaLog::log("Undefined item type[".$this->getItemType()."]");
		}
	}

	protected function getCategoryEntryExactMatchQuery($allowedSearchTypes, &$queryAttributes)
	{
		$this->transformData();
		return kESearchQueryManager::getExactMatchQuery($this, $this->getFieldName(), $allowedSearchTypes, $queryAttributes);
	}

	protected function getCategoryEntryPrefixQuery($allowedSearchTypes, &$queryAttributes)
	{
		$this->transformData();
		return kESearchQueryManager::getPrefixQuery($this, $this->getFieldName(), $allowedSearchTypes, $queryAttributes);
	}

	protected function getCategoryEntryExistsQuery($allowedSearchTypes, &$queryAttributes)
	{

	}

	public function shouldAddLanguageSearch()
	{
		return false;
	}

	public function getItemMappingFieldsDelimiter()
	{

	}

	protected function getCategoryEntryStatusSearchValue()
	{
		$categoryEntryStatus = $this->getCategoryEntryStatus();
		if(!$categoryEntryStatus)
			return CategoryEntryStatus::ACTIVE;
		return $categoryEntryStatus;
	}

}
