<?php
/**
 * @package plugins.elasticSearch
 * @subpackage model.items
 */
class ESearchCategoryEntryAncestorNameItem extends ESearchBaseCategoryEntryItem implements IESearchCategoryEntryItem
{

	private static $allowed_search_types_for_field = array(
		ESearchCategoryEntryFieldName::ANCESTOR_NAME => array('ESearchItemType::EXACT_MATCH'=> ESearchItemType::EXACT_MATCH, 'ESearchItemType::STARTS_WITH'=> ESearchItemType::STARTS_WITH, ESearchUnifiedItem::UNIFIED),
	);

	public static function getAllowedSearchTypesForField()
	{
		return array_merge(self::$allowed_search_types_for_field, parent::getAllowedSearchTypesForField());
	}

	public function transformData()
	{
		$this->setFieldName(self::CATEGORY_NAMES_MAPPING_FIELD);
		$categoryEntryStatus = $this->getCategoryEntryStatusSearchValue();
		$this->setSearchTerm(elasticSearchUtils::formatParentCategoryNameStatus($this->getSearchTerm(), $categoryEntryStatus));
	}

}
