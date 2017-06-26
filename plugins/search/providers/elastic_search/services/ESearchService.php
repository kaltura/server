<?php
/**
 * @service eSearch
 * @package plugins.elasticSearch
 * @subpackage api.services
 */
class ESearchService extends KalturaBaseService
{
	/**
	 *
	 * @action searchEntry
	 * @param KalturaESearchOperator $searchOperator
	 * @param string $entryStatuses
	 * @return KalturaESearchResultArray
	 */
	function searchEntryAction (KalturaESearchOperator $searchOperator, $entryStatuses = null)
	{
		list($coreSearchOperator, $entryStatusesArr) = $this->initSearchActionParams($searchOperator, $entryStatuses);
		/**
		 * @var ESearchOperator $coreSearchOperator
		 */

		$entrySearch = new kEntrySearch();
		$elasticResults = $entrySearch->doSearch($coreSearchOperator, $entryStatusesArr);//TODO: handle error flow
		$coreResults = elasticSearchUtils::transformElasticToObject($elasticResults);

		return KalturaESearchResultArray::fromDbArray($coreResults);
	}

	/**
	 *
	 * @action searchCategory
	 * @param KalturaESearchOperator $searchOperator
	 * @param string $categoryStatuses
	 * @return KalturaESearchResultArray
	 */
	function searchCategoryAction (KalturaESearchOperator $searchOperator, $categoryStatuses = null)
	{
		list($coreSearchOperator, $categoryStatusesArr) = $this->initSearchActionParams($searchOperator, $categoryStatuses);
		/**
		 * @var ESearchOperator $coreSearchOperator
		 */

		$categorySearch = new kEntrySearch(); //TODO change to category engine
		$elasticResults = $categorySearch->doSearch($coreSearchOperator, $categoryStatusesArr);//TODO: handle error flow
		$coreResults = elasticSearchUtils::transformElasticToObject($elasticResults);

		return KalturaESearchResultArray::fromDbArray($coreResults);
	}


	/**
	 *
	 * @action getAllowedSearchTypes
	 * @param KalturaESearchItem $searchItem
	 * @param string $fieldName
	 * @return KalturaKeyValueArray
	 * @throws KalturaAPIException
	 */
	function getAllowedSearchTypesAction (KalturaESearchItem $searchItem)
	{
		$coreSearchItem = $searchItem->toObject();
		$coreSearchItemClass = get_class($coreSearchItem);
		$allowedSearchMap = $coreSearchItemClass::getAallowedSearchTypesForField();

		$result = new KalturaKeyValueArray();
		if (isset($searchItem->fieldName))
		{
			foreach ($allowedSearchMap[$coreSearchItem->getFieldName()] as $searchTypeName => $searchTypeVal)
			{
				$currVal = new KalturaKeyValue();
				$currVal->key = $searchTypeName;
				$currVal->value = $searchTypeVal;
				$result[] = $currVal;
			}
		}
		return $result;
	}

	private function initSearchActionParams(KalturaESearchOperator $searchOperator, $ObjectStatuses = null)
	{
		if (!$searchOperator->operator)
			$searchOperator->operator = KalturaSearchOperatorType::SEARCH_AND;
		//TODO: should we allow doesnt contain without a specific contains
		$coreSearchOperator = $searchOperator->toObject();

		$entryStatusesArr = array();
		if (!empty($ObjectStatuses))
			$entryStatusesArr = explode(',', $ObjectStatuses);

		return array($coreSearchOperator, $entryStatusesArr);
	}


}


?>


