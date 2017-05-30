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
	 * @return KalturaESearchResultArray
	 */
	function searchAction (KalturaESearchOperator $searchOperator)
	{
		if (!$searchOperator->operator)
			$searchOperator->operator = KalturaSearchOperatorType::SEARCH_AND;
		//TODO: should we allow doesnt contain without a specific contains
		$coreSearchOperator = $searchOperator->toObject();
		/**
		 * @var ESearchOperator $coreSearchOperator
		 */
		$entrySearch = new kEntrySearch();
		$elasticResults = $entrySearch->doSearch($coreSearchOperator);//TODO: handle error flow
		$coreResults = elasticSearchUtils::transformElasticToObject($elasticResults);

		return KalturaESearchResultArray::fromDbArray($coreResults);
	}


}


?>


