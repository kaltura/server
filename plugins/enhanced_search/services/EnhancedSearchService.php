<?php
/**
 * @service enhancedSearch
 * @package plugins.enhancedSearch
 * @subpackage api.services
 */
class EnhancedSearchService extends KalturaBaseService
{
	/**
	 * Returns latest version and URL
	 *
	 * @action search
	 * @param KalturaEnhancedSearchParams $searchParams
	 * @return KalturaEnhancedSearchResultArray
	 */
//	function searchAction (KalturaEnhancedSearchParams $searchParams)
//	{
////		$results = new KalturaEnhancedSearchResultArray();
//		$results = KalturaEnhancedSearchResultArray::fromDbArray(array());
//		return $results;
//	}

	/**
	 *
	 *
	 * @action search
	 * @param KalturaEnhancedSearchOperator $searchOperator
	 * @return KalturaEnhancedSearchResultArray
	 */
	function searchAction (KalturaEnhancedSearchOperator $searchOperator)
	{
		$coreSearchOperator = $searchOperator->toObject();
		/**
		 * @var EnhancedSearchOperator $coreSearchOperator
		 */
		// CALL NADAVS class here with coreSearchOperator
		KalturaLog::debug("@@NA for debug [".print_r($coreSearchOperator->getSearchQuery(),true)."]");
		$results = new KalturaEnhancedSearchResultArray();
//		$results = KalturaEnhancedSearchResultArray::fromDbArray(array());
		return $results;
	}


}


