<?php
/**
 * @package plugins.elasticSearch
 * @subpackage model.items
 */
 class ESearchCategoryEntryIdItem extends ESearchBaseCategoryEntryItem implements IESearchCategoryEntryItem
{

	 public function transformData()
	 {
		 $this->setTransformedFieldName();
		 $categoryEntryStatus = $this->getCategoryEntryStatus();
		 if($this->getItemType() == ESearchItemType::EXISTS && $categoryEntryStatus)
			 $this->setSearchTerm(elasticSearchUtils::formatCategoryEntryStatus($categoryEntryStatus));
		 else
		 {
			 if(!$categoryEntryStatus)
				 $categoryEntryStatus = CategoryEntryStatus::ACTIVE;
			 $this->setSearchTerm(elasticSearchUtils::formatCategoryIdStatus($this->getSearchTerm(), $categoryEntryStatus));
		 }
	 }

	 protected function setTransformedFieldName()
	 {
		 $this->setFieldName(self::CATEGORY_IDS_MAPPING_FIELD);
	 }

	 protected static function getCategoryEntryExistsQuery($categoryEntrySearchItem, $allowedSearchTypes, &$queryAttributes)
	 {
		 $categoryEntrySearchItem->transformData();
		 $categoryEntryStatus = $categoryEntrySearchItem->getCategoryEntryStatus();
		 if($categoryEntryStatus)
			 return kESearchQueryManager::getExactMatchQuery($categoryEntrySearchItem, $categoryEntrySearchItem->getFieldName(), $allowedSearchTypes, $queryAttributes);

		 return kESearchQueryManager::getExistsQuery($categoryEntrySearchItem, $categoryEntrySearchItem->getFieldName(), $allowedSearchTypes, $queryAttributes);
	 }

 }
