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
		$response->objects = KalturaESearchEntryResultArray::fromDbArray($coreResults, $this->getResponseProfile());
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
		$response->objects = KalturaESearchCategoryResultArray::fromDbArray($coreResults, $this->getResponseProfile());
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
		$response->objects = KalturaESearchUserResultArray::fromDbArray($coreResults, $this->getResponseProfile());
		$response->totalCount = $objectCount;
		return $response;
	}

	/**
	 *
	 * @action searchGroup
	 * @param KalturaESearchGroupParams $searchParams
	 * @param KalturaPager $pager
	 * @return KalturaESearchGroupResponse
	 */
	function searchGroupAction(KalturaESearchGroupParams $searchParams, KalturaPager $pager = null)
	{
		$userSearch = new kUserSearch();
		list($coreResults, $objectCount) = $this->initAndSearch($userSearch, $searchParams, $pager);
		$response = new KalturaESearchGroupResponse();
		$response->objects = KalturaESearchGroupResultArray::fromDbArray($coreResults, $this->getResponseProfile());
		$response->totalCount = $objectCount;
		return $response;
	}

	/**
	 * @param kBaseSearch $coreSearchObject
	 * @param $searchParams
	 * @param $pager
	 * @return array
	 */
	protected function initAndSearch($coreSearchObject, $searchParams, $pager)
	{
		list($coreSearchOperator, $objectStatusesArr, $objectId, $kPager, $coreOrder) =
			self::initSearchActionParams($searchParams, $pager);
		$elasticResults = $coreSearchObject->doSearch($coreSearchOperator, $kPager, $objectStatusesArr, $objectId, $coreOrder);

		list($coreResults, $objectCount) = kESearchCoreAdapter::transformElasticToCoreObject($elasticResults, $coreSearchObject);
		return array($coreResults, $objectCount);
	}

	protected static function initSearchActionParams($searchParams, KalturaPager $pager = null)
	{
		/**
		 * @var ESearchParams $coreParams
		 */
		$coreParams = $searchParams->toObject();

		$objectStatusesArr = array();
		$objectStatuses = $coreParams->getObjectStatuses();
		if (!empty($objectStatuses))
		{
			$objectStatusesArr = explode(',', $objectStatuses);
		}

		$kPager = null;
		if ($pager)
		{
			$kPager = $pager->toObject();
		}

		return array($coreParams->getSearchOperator(), $objectStatusesArr, $coreParams->getObjectId(), $kPager, $coreParams->getOrderBy());
	}

}