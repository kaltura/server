<?php
/**
 * @package plugins.elasticSearch
 * @subpackage model.items
 */
class ESearchCategoryEntryAncestorNameItem extends ESearchBaseCategoryEntryItem implements IESearchCategoryEntryItem
{

	public function transformData()
	{
		$this->setFieldName(self::CATEGORY_NAMES_MAPPING_FIELD);
		$categoryEntryStatus = $this->getCategoryEntryStatus();
		if(!$categoryEntryStatus)
			$categoryEntryStatus = CategoryEntryStatus::ACTIVE;
		$this->setSearchTerm(elasticSearchUtils::formatParentCategoryNameStatus($this->getSearchTerm(), $categoryEntryStatus));
	}

}
