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
		//TODO: should we allow doesnt contain without a specific contains
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


}


?>


