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
		$coreSearchOperator = $searchOperator->toObject();
		/**
		 * @var UltraSearchOperator $coreSearchOperator
		 */
		$subSearchQuery = kUltraQueryManager::createSearchQuery($coreSearchOperator);
		KalturaLog::debug("@@NA for debug [".print_r($subSearchQuery,true)."]");
		$results = kUltraSearch::doSearch($subSearchQuery);
		return KalturaUltraSearchResultArray::fromDbArray($results);
	}


}


?>


