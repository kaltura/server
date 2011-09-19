<?php
/**
 * Media Info service
 *
 * @service mediaInfo
 * @package api
 * @subpackage services
 */
class MediaInfoService extends KalturaBaseService
{
	public function initService($serviceId, $serviceName, $actionName)
	{
		parent::initService($serviceId, $serviceName, $actionName);
		myPartnerUtils::addPartnerToCriteria(new mediaInfoPeer(), $this->getPartnerId(), $this->private_partner_data, $this->partnerGroup());	
    }
	
	/**
	 * List media info objects by filter and pager
	 * 
	 * @action list
	 * @param KalturaMediaInfoFilter $filter
	 * @param KalturaFilterPager $pager
	 * @return KalturaMediaInfoListResponse
	 */
	function listAction(KalturaMediaInfoFilter $filter = null, KalturaFilterPager $pager = null)
	{
		if (!$filter)
			$filter = new KalturaMediaInfoFilter();

		if (!$pager)
			$pager = new KalturaFilterPager();
			
		$mediaInfoFilter = new MediaInfoFilter();
		
		$filter->toObject($mediaInfoFilter);

		$c = new Criteria();
		$mediaInfoFilter->attachToCriteria($c);
		
		$totalCount = mediaInfoPeer::doCount($c);
		
		$pager->attachToCriteria($c);
		$dbList = mediaInfoPeer::doSelect($c);
		
		$list = KalturaMediaInfoArray::fromDbArray($dbList);
		$response = new KalturaMediaInfoListResponse();
		$response->objects = $list;
		$response->totalCount = $totalCount;
		return $response;
	}
}
