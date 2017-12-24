<?php
/**
 * @package plugins.elasticSearch
 * @subpackage model.items
 */
class ESearchCategoryEntryFullIdsItem extends ESearchBaseCategoryEntryItem implements IESearchCategoryEntryItem
{

	private static $allowed_search_types_for_field = array(
		ESearchCategoryEntryFieldName::FULL_IDS => array('ESearchItemType::EXACT_MATCH'=> ESearchItemType::EXACT_MATCH, 'ESearchItemType::STARTS_WITH'=> ESearchItemType::STARTS_WITH),
	);

	public static function getAllowedSearchTypesForField()
	{
		return array_merge(self::$allowed_search_types_for_field, parent::getAllowedSearchTypesForField());
	}

	public function transformData()
	{
		$this->setFieldName(self::CATEGORY_IDS_MAPPING_FIELD);
		$categoryEntryStatus = $this->getCategoryEntryStatusSearchValue();
		$this->setSearchTerm(elasticSearchUtils::formatCategoryFullIdStatus($this->getSearchTerm(), $categoryEntryStatus));
	}

}
