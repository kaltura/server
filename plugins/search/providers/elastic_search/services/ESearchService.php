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
	 * @action search
	 * @param KalturaESearchOperator $searchOperator
	 * @param string $entryStatuses
	 * @return KalturaESearchResultArray
	 */
	function searchAction (KalturaESearchOperator $searchOperator, $entryStatuses = null)
	{
		if (!$searchOperator->operator)
			$searchOperator->operator = KalturaSearchOperatorType::SEARCH_AND;
		$coreSearchOperator = $searchOperator->toObject();
		/**
		 * @var ESearchOperator $coreSearchOperator
		 */
		$entryStatusesArr = array();
		if (!empty($entryStatuses))
			$entryStatusesArr = explode(',', $entryStatuses);
		$entrySearch = new kEntrySearch();
		$elasticResults = $entrySearch->doSearch($coreSearchOperator, $entryStatusesArr);//TODO: handle error flow
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


}


?>


