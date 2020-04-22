<?php

/**
 * Sending beacons on objects
 *
 * @service beacon
 * @package plugins.beacon
 * @subpackage api.services
 */
class BeaconService extends KalturaBaseService
{
	public function initService($serviceId, $serviceName, $actionName)
	{
		if (($actionName == 'getLast' || $actionName == 'enhanceSearch') && !kCurrentContext::$is_admin_session)
			throw new KalturaAPIException(KalturaErrors::SERVICE_FORBIDDEN, $this->serviceName . '->' . $this->actionName);
		
		parent::initService($serviceId, $serviceName, $actionName);
	}
	
	/**
	 * @action add
	 * @param KalturaBeacon $beacon
	 * @param KalturaNullableBoolean $shouldLog
	 * @return bool
	 */
	public function addAction(KalturaBeacon $beacon, $shouldLog = KalturaNullableBoolean::FALSE_VALUE)
	{
		$beaconObj = $beacon->toInsertableObject();
		$res = $beaconObj->index($shouldLog);
		
		return $res;
	}
	
	/**
	 * @action list
	 * @param KalturaBeaconFilter $filter
	 * @param KalturaFilterPager $pager
	 * @return KalturaBeaconListResponse
	 * @throws KalturaAPIException
	 */
	public function listAction(KalturaBeaconFilter $filter = null, KalturaFilterPager $pager = null)
	{
		if (!$filter)
			$filter = new KalturaBeaconFilter();
		
		if (!$pager)
			$pager = new KalturaFilterPager();
		
		return $filter->getListResponse($pager);
	}
	
	/**
	 * @action enhanceSearch
	 * @param KalturaBeaconEnhanceFilter $filter
	 * @param KalturaFilterPager $pager
	 * @return KalturaBeaconListResponse
	 * @throws KalturaAPIException
	 */
	
	public function enhanceSearchAction(KalturaBeaconEnhanceFilter $filter = null, KalturaFilterPager $pager = null)
	{
		if (!$filter)
			$filter = new KalturaBeaconEnhanceFilter();
		
		if(!$pager)
			$pager = new KalturaFilterPager();
		
		return $filter->enhanceSearch($pager);
	}

	/**
	 * @action searchScheduledResource
	 * @param KalturaBeaconSearchParams $searchParams
	 * @param KalturaPager $pager
	 * @return KalturaBeaconListResponse
	 * @throws KalturaAPIException
	 */

	public function searchScheduledResourceAction(KalturaBeaconSearchParams $searchParams, KalturaPager $pager = null)
	{
		$scheduledResourceSearch = new kScheduledResourceSearch();
		$searchMgr = new kBeaconSearchQueryManger();
		$elasticResponse = $this->initAndSearch($scheduledResourceSearch, $searchParams, $pager);
		$totalCount = $searchMgr->getTotalCount($elasticResponse);
		$responseArray = $searchMgr->getHitsFromElasticResponse($elasticResponse);
		$response = new KalturaBeaconListResponse();
		$response->objects = KalturaBeaconArray::fromDbArray($responseArray);
		$response->totalCount = $totalCount;
		return $response;
	}

	/**
	 * @param kBaseSearch $coreSearchObject
	 * @param $searchParams
	 * @param $pager
	 * @return array
	 */
	private function initAndSearch($coreSearchObject, $searchParams, $pager)
	{
		list($coreParams, $kPager) = self::initSearchActionParams($searchParams, $pager);
		$elasticResults = $coreSearchObject->doSearch($coreParams->getSearchOperator(), $kPager, array(), $coreParams->getObjectId(), $coreParams->getOrderBy());
		return $elasticResults;
	}

	protected static function initSearchActionParams($searchParams, KalturaPager $pager = null)
	{
		$coreParams = $searchParams->toObject();
		$kPager = null;
		if ($pager)
		{
			$kPager = $pager->toObject();
		}

		return array($coreParams, $kPager);
	}

}