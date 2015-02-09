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
		$this->applyPartnerFilterForClass('mediaInfo');
		$this->applyPartnerFilterForClass('asset');
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
	    myDbHelper::$use_alternative_con = myDbHelper::DB_HELPER_CONN_PROPEL2;
	    
		if (!$filter)
			$filter = new KalturaMediaInfoFilter();

		if (!$pager)
			$pager = new KalturaFilterPager();
			
		$mediaInfoFilter = new MediaInfoFilter();
		
		$filter->toObject($mediaInfoFilter);
		
		if ($filter->flavorAssetIdEqual)
		{
			// Since media_info table does not have partner_id column, enforce partner by getting the asset
			if (!assetPeer::retrieveById($filter->flavorAssetIdEqual))
				throw new KalturaAPIException(KalturaErrors::FLAVOR_ASSET_ID_NOT_FOUND, $filter->flavorAssetIdEqual);
		}

		$c = new Criteria();
		$mediaInfoFilter->attachToCriteria($c);
		
		$totalCount = mediaInfoPeer::doCount($c);
		
		$pager->attachToCriteria($c);
		$dbList = mediaInfoPeer::doSelect($c);
		
		$list = KalturaMediaInfoArray::fromDbArray($dbList, $this->getResponseProfile());
		$response = new KalturaMediaInfoListResponse();
		$response->objects = $list;
		$response->totalCount = $totalCount;
		return $response;
	}
}
