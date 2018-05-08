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
	 * @param KalturaESearchEntryParams $searchParams
	 * @param KalturaPager $pager
	 * @return KalturaESearchEntryResponse
	 */
	function searchEntryAction(KalturaESearchEntryParams $searchParams, KalturaPager $pager = null)
	{
		$entrySearch = new kEntrySearch();
		list($coreResults, $objectCount) = $this->initAndSearch($entrySearch, $searchParams, $pager);
		$response = new KalturaESearchEntryResponse();
		$response->objects = KalturaESearchEntryResultArray::fromDbArray($coreResults);
		$response->totalCount = $objectCount;
		return $response;
	}

	/**
	 *
	 * @action searchCategory
	 * @param KalturaESearchCategoryParams $searchParams
	 * @param KalturaPager $pager
	 * @return KalturaESearchCategoryResponse
	 */
	function searchCategoryAction(KalturaESearchCategoryParams $searchParams, KalturaPager $pager = null)
	{
		$categorySearch = new kCategorySearch();
		list($coreResults, $objectCount) = $this->initAndSearch($categorySearch, $searchParams, $pager);
		$response = new KalturaESearchCategoryResponse();
		$response->objects = KalturaESearchCategoryResultArray::fromDbArray($coreResults);
		$response->totalCount = $objectCount;
		return $response;
	}

	/**
	 *
	 * @action searchUser
	 * @param KalturaESearchUserParams $searchParams
	 * @param KalturaPager $pager
	 * @return KalturaESearchUserResponse
	 */
	function searchUserAction(KalturaESearchUserParams $searchParams, KalturaPager $pager = null)
	{
		$userSearch = new kUserSearch();
		list($coreResults, $objectCount) = $this->initAndSearch($userSearch, $searchParams, $pager);
		$response = new KalturaESearchUserResponse();
		$response->objects = KalturaESearchUserResultArray::fromDbArray($coreResults);
		$response->totalCount = $objectCount;
		return $response;
	}

	/**
	 * @param $searchParams
	 * @param KalturaPager|null $pager
	 * @return array
	 * @throws KalturaAPIException
	 */
	private function initSearchActionParams($searchParams, KalturaPager $pager = null)
	{
		$searchOperator = $searchParams->searchOperator;
		if (!$searchOperator)
			throw new KalturaAPIException(KalturaESearchErrors::EMPTY_SEARCH_OPERATOR_NOT_ALLOWED);

		if (!$searchOperator->operator)
			$searchOperator->operator = KalturaSearchOperatorType::SEARCH_AND;

		$coreSearchOperator = $searchOperator->toObject();

		$objectStatusesArr = array();
		if (!empty($searchParams->objectStatuses))
			$objectStatusesArr = explode(',', $searchParams->objectStatuses);

		$kPager = null;
		if ($pager)
			$kPager = $pager->toObject();

		$coreOrder = null;
		$order = $searchParams->orderBy;
		if ($order)
			$coreOrder = $order->toObject();

		return array($coreSearchOperator, $objectStatusesArr, $searchParams->objectId, $kPager, $coreOrder);
	}

	/**
	 * @param kBaseSearch $coreSearchObject
	 * @param $searchParams
	 * @param $pager
	 * @return array
	 */
	private function initAndSearch($coreSearchObject, $searchParams, $pager)
	{
		try
		{
			list($coreSearchOperator, $objectStatusesArr, $objectId, $kPager, $coreOrder) = $this->initSearchActionParams($searchParams, $pager);
			$elasticResults = $coreSearchObject->doSearch($coreSearchOperator, $objectStatusesArr, $objectId, $kPager, $coreOrder);
		} catch (kESearchException $e)
		{
			$this->handleSearchException($e);
		}

		list($coreResults, $objectCount) = kESearchCoreAdapter::transformElasticToCoreObject($elasticResults,
			$coreSearchObject->getPeerName(), $coreSearchObject->getPeerRetrieveFunctionName(),
			$coreSearchObject->getQueryAttributes()->getQueryHighlightsAttributes());
		return array($coreResults, $objectCount);
	}

	private function handleSearchException($exception)
	{
		$code = $exception->getCode();
		$data = $exception->getData();
		switch ($code)
		{
			case kESearchException::SEARCH_TYPE_NOT_ALLOWED_ON_FIELD:
				throw new KalturaAPIException(KalturaESearchErrors::SEARCH_TYPE_NOT_ALLOWED_ON_FIELD, $data['itemType'], $data['fieldName']);
			case kESearchException::EMPTY_SEARCH_TERM_NOT_ALLOWED:
				throw new KalturaAPIException(KalturaESearchErrors::EMPTY_SEARCH_TERM_NOT_ALLOWED, $data['fieldName'], $data['itemType']);
			case kESearchException::SEARCH_TYPE_NOT_ALLOWED_ON_UNIFIED_SEARCH:
				throw new KalturaAPIException(KalturaESearchErrors::SEARCH_TYPE_NOT_ALLOWED_ON_UNIFIED_SEARCH, $data['itemType']);
			case kESearchException::EMPTY_SEARCH_ITEMS_NOT_ALLOWED:
				throw new KalturaAPIException(KalturaESearchErrors::EMPTY_SEARCH_ITEMS_NOT_ALLOWED);
			case kESearchException::UNMATCHING_BRACKETS:
				throw new KalturaAPIException(KalturaESearchErrors::UNMATCHING_BRACKETS);
			case kESearchException::MISSING_QUERY_OPERAND:
				throw new KalturaAPIException(KalturaESearchErrors::MISSING_QUERY_OPERAND);
			case kESearchException::UNMATCHING_QUERY_OPERAND:
				throw new KalturaAPIException(KalturaESearchErrors::UNMATCHING_QUERY_OPERAND);
			case kESearchException::CONSECUTIVE_OPERANDS_MISMATCH:
				throw new KalturaAPIException(KalturaESearchErrors::CONSECUTIVE_OPERANDS_MISMATCH);
			case kESearchException::INVALID_FIELD_NAME:
				throw new KalturaAPIException(KalturaESearchErrors::INVALID_FIELD_NAME, $data['fieldName']);
			case kESearchException::INVALID_METADATA_FORMAT:
				throw new kESearchException(KalturaESearchErrors::INVALID_METADATA_FORMAT);
			case kESearchException::INVALID_METADATA_FIELD:
				throw new kESearchException(KalturaESearchErrors::INVALID_METADATA_FIELD, $data['fieldName']);
			case kESearchException::INVALID_MIXED_SEARCH_TYPES:
				throw new kESearchException(KalturaESearchErrors::INVALID_MIXED_SEARCH_TYPES, $data['fieldName'], $data['fieldValue']);
			case kESearchException::MISSING_MANDATORY_PARAMETERS_IN_ORDER_ITEM:
				throw new KalturaAPIException(KalturaESearchErrors::MISSING_MANDATORY_PARAMETERS_IN_ORDER_ITEM);
			case kESearchException::MIXED_SEARCH_ITEMS_IN_NESTED_OPERATOR_NOT_ALLOWED:
				throw new KalturaAPIException(KalturaESearchErrors::MIXED_SEARCH_ITEMS_IN_NESTED_OPERATOR_NOT_ALLOWED);

			default:
				throw new KalturaAPIException(KalturaESearchErrors::INTERNAL_SERVERL_ERROR);
		}
	}
}