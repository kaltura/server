<?php
/**
 * @package plugins.elasticSearch
 * @subpackage model.items
 */
 class ESearchCategoryEntryIdItem extends ESearchBaseCategoryEntryItem implements IESearchCategoryEntryItem
{
	 private static $allowed_search_types_for_field = array(
		 ESearchCategoryEntryFieldName::ID => array('ESearchItemType::EXACT_MATCH'=> ESearchItemType::EXACT_MATCH, 'ESearchItemType::EXISTS' => ESearchItemType::EXISTS),
	 );

	 public static function getAllowedSearchTypesForField()
	 {
		 return array_merge(self::$allowed_search_types_for_field, parent::getAllowedSearchTypesForField());
	 }

	 protected function getCategoryEntryStatusSearchValue()
	 {
		 $categoryEntryStatus = $this->getCategoryEntryStatus();
		 if(!$categoryEntryStatus)
		 {
			 if($this->getItemType() == ESearchItemType::EXISTS)
				 return null;

			 $categoryEntryStatus = CategoryEntryStatus::ACTIVE;
		 }

		 return $categoryEntryStatus;
	 }

	 public function transformData()
	 {
		 $this->setFieldName(self::CATEGORY_IDS_MAPPING_FIELD);
		 $categoryEntryStatus = $this->getCategoryEntryStatusSearchValue();
		 if($this->getItemType() == ESearchItemType::EXISTS)
			 $this->setSearchTerm(elasticSearchUtils::formatCategoryEntryStatus($categoryEntryStatus));
		 else
			 $this->setSearchTerm(elasticSearchUtils::formatCategoryIdStatus($this->getSearchTerm(), $categoryEntryStatus));
	 }

	 protected function getCategoryEntryExistsQuery($allowedSearchTypes, &$queryAttributes)
	 {
		 $this->transformData();
		 $categoryEntryStatus = $this->getCategoryEntryStatusSearchValue();
		 if($categoryEntryStatus)
			 return kESearchQueryManager::getExactMatchQuery($this, $this->getFieldName(), $allowedSearchTypes, $queryAttributes);

		 return kESearchQueryManager::getExistsQuery($this, $this->getFieldName(), $allowedSearchTypes, $queryAttributes);
	 }

 }
