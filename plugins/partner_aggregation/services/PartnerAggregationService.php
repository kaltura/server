<?php
/**
 * Partner Aggregation service
 *
 * @service partnerAggregation
 * @package plugins.partnerAggregation
 * @subpackage api.services
 */
class PartnerAggregationService extends KalturaBaseService
{
	public function initService($serviceId, $serviceName, $actionName)
	{
		parent::initService($serviceId, $serviceName, $actionName);

		myPartnerUtils::addPartnerToCriteria(new DwhHourlyPartnerPeer(), $this->getPartnerId(), $this->private_partner_data, $this->partnerGroup());
	}
	
	/**
	 * List aggregated partner data
	 * 
	 * @action list
	 * @param KalturaDwhHourlyPartnerFilter $filter
	 * @param KalturaFilterPager $pager
	 * @return KalturaDwhHourlyPartnerListResponse
	 */
	function listAction(KalturaDwhHourlyPartnerFilter $filter, KalturaFilterPager $pager = null)
	{
		$filter->validatePropertyNotNull('aggregatedTimeLessThanOrEqual');
		$filter->validatePropertyNotNull('aggregatedTimeGreaterThanOrEqual');

		
		$c = new Criteria();			
		$dwhHourlyPartnerFilter = $filter->toObject();
		$dwhHourlyPartnerFilter->attachToCriteria($c);
		$count = DwhHourlyPartnerPeer::doCount($c);
		
		if ($pager)
			$pager->attachToCriteria($c);
		$list = DwhHourlyPartnerPeer::doSelect($c);
		
		$response = new KalturaDwhHourlyPartnerListResponse();
		$response->objects = KalturaDwhHourlyPartnerArray::fromDbArray($list);
		$response->totalCount = $count;
	
		return $response;
	}	
}
