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
		list($coreResults, $objectCount, $aggregations) = $this->initAndSearch($entrySearch, $searchParams, $pager);
		$response = new KalturaESearchEntryResponse();
		$response->objects = KalturaESearchEntryResultArray::fromDbArray($coreResults, $this->getResponseProfile());
		$response->totalCount = $objectCount;
		if($aggregations)
		{
			$aggregationResponse = new KalturaESearchAggregationResponse();
			$response->aggregations = $aggregationResponse->resultToApi($aggregations);
		}
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
	 * Creates a batch job that sends an email with a link to download a CSV containing a list of entries
	 *
	 * @action entryExportToCsv
	 * @actionAlias media.exportToCsv
	 * @param KalturaMediaEsearchExportToCsvJobData $data job data indicating filter to pass to the job
	 * @return string
	 *
	 * @throws APIErrors::USER_EMAIL_NOT_FOUND
	 */
	public function entryExportToCsvAction (KalturaMediaEsearchExportToCsvJobData $data)
	{
		if(!$data->userName || !$data->userMail)
			throw new KalturaAPIException(APIErrors::USER_EMAIL_NOT_FOUND, '');
		
		$kJobdData = $data->toObject(new kMediaEsearchExportToCsvJobData());
		
		kJobsManager::addExportCsvJob($kJobdData, $this->getPartnerId(), ElasticSearchPlugin::getExportTypeCoreValue(EsearchMediaEntryExportObjectType::ESEARCH_MEDIA));
		
		return $data->userMail;
	}

	/**
	 * @param kBaseSearch $coreSearchObject
	 * @param $searchParams
	 * @param $pager
	 * @return array
	 */
	protected function initAndSearch($coreSearchObject, $searchParams, $pager)
	{
		list($coreSearchOperator, $objectStatusesArr, $objectId, $kPager, $coreOrder, $aggregations) =
			self::initSearchActionParams($searchParams, $pager);
		$elasticResults = $coreSearchObject->doSearch($coreSearchOperator, $kPager, $objectStatusesArr, $objectId, $coreOrder, $aggregations);

		list($coreResults, $objectCount, $aggregationsResult) = kESearchCoreAdapter::transformElasticToCoreObject($elasticResults, $coreSearchObject);
		return array($coreResults, $objectCount, $aggregationsResult);
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

		return array($coreParams->getSearchOperator(), $objectStatusesArr, $coreParams->getObjectId(), $kPager, $coreParams->getOrderBy(), $coreParams->getAggregations());
	}

}