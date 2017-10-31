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
	 * @param KalturaESearchObject $searchParams
	 * @param KalturaPager $pager
	 * @return KalturaESearchResponse
	 */
	function searchEntryAction(KalturaESearchObject $searchParams, KalturaPager $pager = null)
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
	 * @param KalturaESearchObject $searchParams
	 * @param KalturaPager $pager
	 * @return KalturaESearchResponse
	 */
	function searchCategoryAction(KalturaESearchObject $searchParams, KalturaPager $pager = null)
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
	 * @param KalturaESearchObject $searchParams
	 * @param KalturaPager $pager
	 * @return KalturaESearchResponse
	 */
	function searchUserAction(KalturaESearchObject $searchParams, KalturaPager $pager = null)
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
	function getAllowedSearchTypesAction(KalturaESearchItem $searchItem)
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

	private function initSearchActionParams(KalturaESearchObject $searchParams, KalturaPager $pager = null)
	{
		if ($searchParams instanceof KalturaESearchQuery)
			$searchParams = kESearchQueryParser::buildKESearchParamsFromKESearchQuery($searchParams);

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

		return array($coreSearchOperator, $objectStatusesArr, $kPager, $coreOrder);
	}

	private function initAndSearch($coreSearchObject, $searchParams, $pager)
	{
		try
		{
			list($coreSearchOperator, $objectStatusesArr, $kPager, $coreOrder) = $this->initSearchActionParams($searchParams, $pager);
			$elasticResults = $coreSearchObject->doSearch($coreSearchOperator, $objectStatusesArr, $kPager, $coreOrder);
		} catch (kESearchException $e)
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
		$code = $exception->getMessage();
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
			case kESearchException::INVALID_MIXED_SERACH_TYPES:
				throw new kESearchException(KalturaESearchErrors::INVALID_MIXED_SERACH_TYPES, $data['fieldName'], $data['fieldValue']);

			default:
				throw new KalturaAPIException(KalturaESearchErrors::INTERNAL_SERVERL_ERROR);
		}
	}
}