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
		// CALL NADAVS class here with coreSearchOperator
		KalturaLog::debug("@@NA for debug [".print_r($coreSearchOperator->getSearchQuery(),true)."]");
//		$results = new KalturaUltraSearchResultArray();
		$results = KalturaUltraSearchResultArray::fromDbArray(array());
		return $results;
	}


}


