<?php
/**
 * @service ultraSearch
 * @package plugins.elasticSearch
 * @subpackage api.services
 */
class UltraSearchService extends KalturaBaseService
{
	/**
	 *
	 * @action search
	 * @param KalturaUltraSearchOperator $searchOperator
	 * @return KalturaUltraSearchResultArray
	 */
	function searchAction (KalturaUltraSearchOperator $searchOperator)
	{
		//TODO: should we allow doesnt contain without a specific contains
		$coreSearchOperator = $searchOperator->toObject();
		/**
		 * @var UltraSearchOperator $coreSearchOperator
		 */
		$entrySearch = new kEntrySearch();
		$elasticResults = $entrySearch->doSearch($coreSearchOperator);//TODO: handle error flow
		$coreResults = elasticSearchUtils::transformElasticToObject($elasticResults);

		return KalturaUltraSearchResultArray::fromDbArray($coreResults);
	}


}


?>


