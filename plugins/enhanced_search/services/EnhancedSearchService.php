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
		// CALL NADAVS class here with coreSearchOperator

		$results = new KalturaEnhancedSearchResultArray();
//		$results = KalturaEnhancedSearchResultArray::fromDbArray(array());
		return $results;
	}


}


