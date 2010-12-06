<?php
/**
 * Media Info service
 *
 * @service mediaInfo
 * @package api
 * @subpackage extServices
 */
class MediaInfoService extends KalturaBaseService
{
	public function initService($partnerId, $puserId, $ksStr, $serviceName, $action)
	{
		parent::initService($partnerId, $puserId, $ksStr, $serviceName, $action);

		// since plugin might be using KS impersonation, we need to validate the requesting
		// partnerId from the KS and not with the $_POST one
		if(!AdminConsolePlugin::isAllowedPartner($this->getPartnerId()))
			throw new KalturaAPIException(SystemUserErrors::SERVICE_FORBIDDEN);
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
