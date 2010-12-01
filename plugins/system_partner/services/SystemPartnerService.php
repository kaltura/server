<?php
/**
 * System partner service
 *
 * @service systemPartner
 */
class SystemPartnerService extends KalturaBaseService
{
	public function initService($partnerId, $puserId, $ksStr, $serviceName, $action)
	{
		parent::initService($partnerId, $puserId, $ksStr, $serviceName, $action);
		
		// since plugin might be using KS impersonation, we need to validate the requesting
		// partnerId from the KS and not with the $_POST one
		if(!SystemPartnerPlugin::isAllowedPartner(kCurrentContext::$ks_partner_id))
			throw new KalturaAPIException(SystemPartnerErrors::SERVICE_FORBIDDEN);
	}

	
	/**
	 * Retrieve all info about partner
	 * This service gets partner id as parameter and accessable to the admin console partner only
	 * 
	 * @action get
	 * @param int $partnerIdX
	 * @return KalturaPartner
	 *
	 * @throws APIErrors::UNKNOWN_PARTNER_ID
	 */		
	function getAction($partnerId)
	{		
		$dbPartner = PartnerPeer::retrieveByPK( $partnerId );
		
		if ( ! $dbPartner )
			throw new KalturaAPIException ( APIErrors::UNKNOWN_PARTNER_ID , $partnerId );
			
		$partner = new KalturaPartner();
		$partner->fromPartner( $dbPartner );
		
		return $partner;
	}
	
	/**
	 * @action getUsage
	 * @param KalturaSystemPartnerUsageFilter $filter
	 * @param KalturaFilterPager $pager
	 * @return KalturaSystemPartnerUsageListResponse
	 */
	public function getUsageAction(KalturaPartnerFilter $partnerFilter = null, KalturaSystemPartnerUsageFilter $usageFilter = null, KalturaFilterPager $pager = null)
	{
		if (is_null($partnerFilter))
			$partnerFilter = new KalturaPartnerFilter();
		
		if (is_null($usageFilter))
		{
			$usageFilter = new KalturaSystemPartnerUsageFilter();
			$usageFilter->fromDate = time() - 60*60*24*30; // last 30 days
			$usageFilter->toDate = time();
		}
		
		if (is_null($pager))
			$pager = new KalturaFilterPager();

		$partnerFilterDb = new partnerFilter();
		$partnerFilter->toObject($partnerFilterDb);
		$partnerFilterDb->set('_gt_id', 0);
		
		// total count
		$c = new Criteria();
		$partnerFilterDb->attachToCriteria($c);
		$totalCount = PartnerPeer::doCount($c);
		
		// filter partners criteria
		$pager->attachToCriteria($c);
		$c->addAscendingOrderByColumn(PartnerPeer::ID);
		
		// select partners
		$partners = PartnerPeer::doSelect($c);
		$partnerIds = array();
		foreach($partners as &$partner)
			$partnerIds[] = $partner->getId();
		
		$items = array();
		if ( ! count($partnerIds ) )
		{
			// no partners fit the filter - don't fetch data	
			$totalCount = 0;
			// the items are set to an empty KalturaSystemPartnerUsageArray
		}
		else
		{
			$inputFilter = new reportsInputFilter (); 
			$inputFilter->from_date = ( $usageFilter->fromDate );
			$inputFilter->to_date = ( $usageFilter->toDate );
	
			list ( $reportHeader , $reportData , $totalCountNoNeeded ) = myReportsMgr::getTable( 
				null , 
				myReportsMgr::REPORT_TYPE_ADMIN_CONSOLE , 
				$inputFilter ,
				$pager->pageSize , 0 , // pageIndex is 0 because we are using specific ids 
				null  , // order by  
				implode("," , $partnerIds ) );
			
			$unsortedItems = array();
			foreach ( $reportData as $line )
			{
				$item = KalturaSystemPartnerUsageItem::fromString( $reportHeader , $line );
				if ( $item )	
					$unsortedItems[$item->partnerId] = $item;	
			}
					
			// create the items in the order of the partnerIds and create some dummy for ones that don't exist
			foreach ( $partnerIds as $partnerId )
			{
				if ( isset ( $unsortedItems[$partnerId] ))
					$items[] = $unsortedItems[$partnerId];
				else
				{
					// if no item for partner - get its details from the db
					$items[] = KalturaSystemPartnerUsageItem::fromPartner(PartnerPeer::retrieveByPK($partnerId));
				}  
			}
		}
		$response = new KalturaSystemPartnerUsageListResponse();
		$response->totalCount = $totalCount;
		$response->objects = $items;
		return $response;
	}
		

	
	/**
	 * @action list
	 * @param KalturaPartnerFilter $filter
	 * @param KalturaFilterPager $pager
	 * @return KalturaPartnerListResponse
	 */
	public function listAction(KalturaPartnerFilter $filter = null, KalturaFilterPager $pager = null)
	{
		if (is_null($filter))
			$filter = new KalturaPartnerFilter();
			
		if (is_null($pager))
			$pager = new KalturaFilterPager();

		$partnerFilter = new partnerFilter();
		$filter->toObject($partnerFilter);
		$partnerFilter->set('_gt_id', 0);
		
		$c = new Criteria();
		$partnerFilter->attachToCriteria($c);
		
		$totalCount = PartnerPeer::doCount($c);
		$pager->attachToCriteria($c);
		$list = PartnerPeer::doSelect($c);
		$newList = KalturaPartnerArray::fromPartnerArray($list);
		
		$response = new KalturaPartnerListResponse();
		$response->totalCount = $totalCount;
		$response->objects = $newList;
		return $response;
	}
	
	/**
	 * @action updateStatus
	 * @param int $partnerId
	 * @param KalturaPartnerStatus $status
	 */
	public function updateStatusAction($partnerId, $status)
	{
		$dbPartner = PartnerPeer::retrieveByPK($partnerId);
		if (!$dbPartner)
			throw new KalturaAPIException(KalturaErrors::UNKNOWN_PARTNER_ID, $partnerId);
			
		$dbPartner->setStatus($status);
		$dbPartner->save();
		PartnerPeer::removePartnerFromCache($partnerId);
	}
	
	/**
	 * @action getAdminSession
	 * @param int $partnerId
	 * @return string
	 */
	public function getAdminSessionAction($partnerId)
	{
		$dbPartner = PartnerPeer::retrieveByPK($partnerId);
		if (!$dbPartner)
			throw new KalturaAPIException(KalturaErrors::UNKNOWN_PARTNER_ID, $partnerId);
			
		$ks = "";
		kSessionUtils::createKSessionNoValidations($dbPartner->getId(), "__ADMIN__", $ks, 86400, 2, "", "*");
		return $ks;
	}
	
	/**
	 * @action updateConfiguration
	 * @param int $partnerId
	 * @param KalturaSystemPartnerConfiguration $configuration
	 */
	public function updateConfigurationAction($partnerId, KalturaSystemPartnerConfiguration $configuration)
	{
		$dbPartner = PartnerPeer::retrieveByPK($partnerId);
		if (!$dbPartner)
			throw new KalturaAPIException(KalturaErrors::UNKNOWN_PARTNER_ID, $partnerId);

		$configuration->toUpdatableObject($dbPartner);
		$dbPartner->save();
		PartnerPeer::removePartnerFromCache($partnerId);
	}
	
	/**
	 * @action getConfiguration
	 * @param int $partnerId
	 * @return KalturaSystemPartnerConfiguration
	 */
	public function getConfigurationAction($partnerId)
	{
		$dbPartner = PartnerPeer::retrieveByPK($partnerId);
		if (!$dbPartner)
			throw new KalturaAPIException(KalturaErrors::UNKNOWN_PARTNER_ID, $partnerId);
			
		$configuration = new KalturaSystemPartnerConfiguration();
		$configuration->fromObject($dbPartner);
		return $configuration;
	}
	
	/**
	 * @action getPackages
	 * @return KalturaSystemPartnerPackageArray
	 */
	public function getPackages()
	{
		$partnerPackages = new PartnerPackages();
		$packages = $partnerPackages->listPackages();
		$partnerPackages = new KalturaSystemPartnerPackageArray();
		$partnerPackages->fromArray($packages);
		return $partnerPackages;
	}
}
