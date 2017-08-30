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
	 * @param KalturaESearchParams $searchParams
	 * @param KalturaPager $pager
	 * @return KalturaESearchResponse
	 */
	function searchEntryAction (KalturaESearchParams $searchParams, KalturaPager $pager = null)
	{
		$entrySearch = new kEntrySearch();
		list($coreResults, $objectCount) = $this->initAndSearch($entrySearch, $searchParams, $pager);
		$response = new KalturaESearchResponse();
		$response->objects = KalturaESearchEntryResultArray::fromDbArray($coreResults);
		$response->totalCount = $objectCount;
		return $response;
	}

	/**
	 *
	 * @action searchCategory
	 * @param KalturaESearchParams $searchParams
	 * @param KalturaPager $pager
	 * @return KalturaESearchResponse
	 */
	function searchCategoryAction (KalturaESearchParams $searchParams, KalturaPager $pager = null)
	{
		$categorySearch = new kCategorySearch();
		list($coreResults, $objectCount) = $this->initAndSearch($categorySearch, $searchParams, $pager);
		$response = new KalturaESearchResponse();
		$response->objects = KalturaESearchCategoryResultArray::fromDbArray($coreResults);
		$response->totalCount = $objectCount;
		return $response;
	}

	/**
	 *
	 * @action searchUser
	 * @param KalturaESearchParams $searchParams
	 * @param KalturaPager $pager
	 * @return KalturaESearchResponse
	 */
	function searchUserAction (KalturaESearchParams $searchParams, KalturaPager $pager = null)
	{
		$userSearch = new kUserSearch();
		list($coreResults, $objectCount) = $this->initAndSearch($userSearch, $searchParams, $pager);
		$response = new KalturaESearchResponse();
		$response->objects = KalturaESearchUserResultArray::fromDbArray($coreResults);
		$response->totalCount = $objectCount;
		return $response;
	}

	/**
	 *
	 * @action getAllowedSearchTypes
	 * @param KalturaESearchItem $searchItem
	 * @return KalturaKeyValueArray
	 * @throws KalturaAPIException
	 */
	function getAllowedSearchTypesAction (KalturaESearchItem $searchItem)
	{
		$coreSearchItem = $searchItem->toObject();
		$coreSearchItemClass = get_class($coreSearchItem);
		$allowedSearchMap = $coreSearchItemClass::getAllowedSearchTypesForField();

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

	private function initSearchActionParams(KalturaESearchParams $searchParams, KalturaPager $pager = null)
	{
		$searchOperator = $searchParams->searchOperator;

		if (!$searchOperator->operator)
			$searchOperator->operator = KalturaSearchOperatorType::SEARCH_AND;

		$coreSearchOperator = $searchOperator->toObject();

		$objectStatusesArr = array();
		if (!empty($searchParams->objectStatuses))
			$objectStatusesArr = explode(',', $searchParams->objectStatuses);

		$kPager = null;
		if($pager)
			$kPager = $pager->toObject();

		$coreOrder = null;
		$order = $searchParams->orderBy;
		if($order)
			$coreOrder = $order->toObject();

		return array($coreSearchOperator, $objectStatusesArr, $kPager, $coreOrder);
	}

	private function initAndSearch($coreSearchObject, $searchParams, $pager)
	{
		list($coreSearchOperator, $objectStatusesArr, $kPager, $coreOrder) = $this->initSearchActionParams($searchParams, $pager);

		try
		{
			$elasticResults = $coreSearchObject->doSearch($coreSearchOperator, $objectStatusesArr, $kPager, $coreOrder);
		}
		catch (kESearchException $e)
		{
			$this->handleSearchException($e);
		}

		list($coreResults, $objectCount) = kESearchCoreAdapter::transformElasticToCoreObject($elasticResults, $coreSearchObject->getPeerName());
		return array($coreResults, $objectCount);
	}

	private function handleSearchException($exception)
	{
		$code = $exception->getCode();
		$data = $exception->getData();
		switch($code)
		{
			case kESearchException::SEARCH_TYPE_NOT_ALLOWED_ON_FIELD:
				throw new KalturaAPIException(KalturaESearchErrors::SEARCH_TYPE_NOT_ALLOWED_ON_FIELD, $data['itemType'], $data['fieldName']);
			case kESearchException::EMPTY_SEARCH_TERM_NOT_ALLOWED:
				throw new KalturaAPIException(KalturaESearchErrors::EMPTY_SEARCH_TERM_NOT_ALLOWED, $data['fieldName'], $data['itemType']);
			case kESearchException::SEARCH_TYPE_NOT_ALLOWED_ON_UNIFIED_SEARCH:
				throw new KalturaAPIException(KalturaESearchErrors::SEARCH_TYPE_NOT_ALLOWED_ON_UNIFIED_SEARCH, $data['itemType']);
			default:
				throw new KalturaAPIException(KalturaESearchErrors::INTERNAL_SERVERL_ERROR);
		}
	}

}
