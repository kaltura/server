<?php
/**
 * @package plugins.elasticSearch
 * @subpackage model.items
 */
class ESearchCategoryEntryAncestorIdItem extends ESearchBaseCategoryEntryItem implements IESearchCategoryEntryItem
{

	public function transformData()
	{
		$this->setFieldName(self::CATEGORY_IDS_MAPPING_FIELD);
		$categoryEntryStatus = $this->getCategoryEntryStatus();
		if(!$categoryEntryStatus)
			$categoryEntryStatus = CategoryEntryStatus::ACTIVE;
		$this->setSearchTerm(elasticSearchUtils::formatParentCategoryIdStatus($this->getSearchTerm(), $categoryEntryStatus));
	}

}
