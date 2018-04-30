<?php
/**
 * Utility service for the Multi-publishers console
 * 
 * @service varConsole
 * @package plugins.varConsole
 * @subpackage api.services
 *
 */
class VarConsoleService extends KalturaBaseService
{
    const MAX_SUB_PUBLISHERS = 2000;
    
    public function initService($serviceId, $serviceName, $actionName)
    {
        parent::initService($serviceId, $serviceName, $actionName);
		
		if(!VarConsolePlugin::isAllowedPartner($this->getPartnerId()))
		{
		    throw new KalturaAPIException(KalturaErrors::FEATURE_FORBIDDEN, VarConsolePlugin::PLUGIN_NAME);
		}	
    }
    
    /**
     * Action which checks whther user login 
     * @action checkLoginDataExists
     * @actionAlias user.checkLoginDataExists
     * @param KalturaUserLoginDataFilter $filter
     * @return bool
     */
    public function checkLoginDataExistsAction (KalturaUserLoginDataFilter $filter)
    {
        if (!$filter)
	    {
	        $filter = new KalturaUserLoginDataFilter();
	        $filter->loginEmailEqual = $this->getPartner()->getAdminEmail();
	    }
	    
	    $userLoginDataFilter = new UserLoginDataFilter();
		$filter->toObject($userLoginDataFilter);
		
		$c = new Criteria();
		$userLoginDataFilter->attachToCriteria($c);
		
		$totalCount = UserLoginDataPeer::doCount($c);
		
		if ($totalCount)
		    return true;
		 
		return false;
    }
    
	/**
     * Function which calulates partner usage of a group of a VAR's sub-publishers
     * 
     * @action getPartnerUsage
     * @param KalturaPartnerFilter $partnerFilter
     * @param KalturaReportInputFilter $usageFilter
     * @param KalturaFilterPager $pager
     * @return KalturaPartnerUsageListResponse
     * @throws KalturaVarConsoleErrors::MAX_SUB_PUBLISHERS_EXCEEDED
     */
    public function getPartnerUsageAction (KalturaPartnerFilter $partnerFilter = null, KalturaReportInputFilter $usageFilter = null, KalturaFilterPager $pager = null)
    {
        if (is_null($partnerFilter))
        {
            $partnerFilter = new KalturaPartnerFilter();
        }
        
        if (is_null($usageFilter))
        {
            $usageFilter = new KalturaReportInputFilter();
            $usageFilter->fromDate = time() - 60*60*24*30; // last 30 days
			$usageFilter->toDate = time();
        }
        else
        {
            //The first time the filter is sent, it it sent with 0 as fromDate
            if (!$usageFilter->fromDate)
                $usageFilter->fromDate = time() - 60*60*24*30;
            if (!$usageFilter->interval)
                $usageFilter->interval = KalturaReportInterval::MONTHS;
        }
        
        if (is_null($pager))
        {
            $pager = new KalturaFilterPager();
        }
        
        //Create a propel filter for the partner
        $partnerFilterDb = new partnerFilter();
		$partnerFilter->toObject($partnerFilterDb);
		
		//add filter to criteria
		$c = PartnerPeer::getDefaultCriteria();
		$partnerFilterDb->attachToCriteria($c);
		
		$partnersCount = PartnerPeer::doCount($c);
		if ($partnersCount > self::MAX_SUB_PUBLISHERS)
		{
		    throw new KalturaAPIException(KalturaVarConsoleErrors::MAX_SUB_PUBLISHERS_EXCEEDED);
		}
		
		$partners = PartnerPeer::doSelect($c);
		$partnerIds = array();
		foreach($partners as &$partner)
			$partnerIds[] = $partner->getId();
		
		// add pager to criteria
		$pager->attachToCriteria($c);
		$c->addAscendingOrderByColumn(PartnerPeer::ID);
		
		// select partners
		
		$items = array();
		
		$inputFilter = new reportsInputFilter (); 
		$inputFilter->from_date = ( $usageFilter->fromDate );
		$inputFilter->to_date = ( $usageFilter->toDate );
		$inputFilter->from_day = date ( "Ymd" , $usageFilter->fromDate );
		$inputFilter->to_day = date ( "Ymd" , $usageFilter->toDate );	
		$inputFilter->timeZoneOffset = $usageFilter->timeZoneOffset;
		$inputFilter->interval = $usageFilter->interval;
		
		if ( ! count($partnerIds ) )
		{
		    $total = new KalturaVarPartnerUsageTotalItem();
			// no partners fit the filter - don't fetch data	
			$totalCount = 0;
			// the items are set to an empty KalturaSystemPartnerUsageArray
		}
		else
		{
		    $totalCount = 0;
		    $orderBy = ($inputFilter->interval == reportInterval::MONTHS ? "+month_id" : "+date_id") . ",+partner_id";
		    list ( $reportHeader , $reportData , $totalCount ) = kKavaReportsMgr::getTable(  
    				null , 
    				myReportsMgr::REPORT_TYPE_VAR_USAGE , 
    				$inputFilter ,
    				$pager->pageSize , $pager->pageIndex, // pageIndex is 0 because we are using specific ids 
    				$orderBy  , // order by  
    				implode(",", $partnerIds));
    				
		    foreach ( $reportData as $line )
			{
    			$item = new KalturaVarPartnerUsageItem();
				$item->fromString( $reportHeader , $line );
    			if ($item)
    			{
    			    $items[] = $item;
    			}
			}
			
			list ( $reportHeader , $reportData) = kKavaReportsMgr::getTotal( 
    				null , 
    				myReportsMgr::REPORT_TYPE_PARTNER_USAGE , 
    				$inputFilter ,
    				implode(",", $partnerIds));
		
    		$total = new KalturaVarPartnerUsageTotalItem();
    		$total->fromString($reportHeader, $reportData);

			list ( $peakStoragereportHeader , $peakStoragereportData) = kKavaReportsMgr::getTotal(
					null ,
					myReportsMgr::REPORT_TYPE_PEAK_STORAGE ,
					$inputFilter ,
					implode(",", $partnerIds));

    		if($peakStoragereportData[0])
    			$total->peakStorage = ceil(@$peakStoragereportData[0]);
    		}

		$response = new KalturaPartnerUsageListResponse();
		
		//Sort according to dateId and partnerId
		uasort($items, array($this, 'sortByDate'));
		
        $response->total = $total; 
		$response->totalCount = $totalCount;
		$response->objects = $items;
		return $response;
    }
    
    /**
     * Sorting function - returns array sorted first by dateId and secondly by partnerId
     * @param KalturaVarPartnerUsageItem $item1
     * @param KalturaVarPartnerUsageItem $item2
     */
    private function sortByDate (KalturaVarPartnerUsageItem $item1, KalturaVarPartnerUsageItem $item2)
    {
        $dateItem1 = strlen($item1->dateId) == 6 ? DateTime::createFromFormat( "Ym" , $item1->dateId)->getTimestamp() : DateTime::createFromFormat( "Ymd" , $item1->dateId)->getTimestamp();
        $dateItem2 = strlen($item2->dateId) == 6 ? DateTime::createFromFormat( "Ym" , $item2->dateId)->getTimestamp() : DateTime::createFromFormat( "Ymd" , $item2->dateId)->getTimestamp();
        
        if ($dateItem1 == $dateItem2)
        {
            if ($item1->partnerId > $item2->partnerId)
            {
                return 1;
            }
            else
            {
                return -1;
            }
        }
        else if ($dateItem1 > $dateItem2)
        {
            return 1;
        }
        else
        {
            return -1;
        }
    }
    
	/**
	 * Function to change a sub-publisher's status
	 * @action updateStatus
	 * @param int $id
	 * @param KalturaPartnerStatus $status
	 * @throws KalturaErrors::UNKNOWN_PARTNER_ID
	 */
	public function updateStatusAction($id, $status)
	{
        $c = PartnerPeer::getDefaultCriteria();
        $c->addAnd(PartnerPeer::ID, $id);
        $dbPartner = PartnerPeer::doSelectOne($c);		
		if (!$dbPartner)
			throw new KalturaAPIException(KalturaErrors::UNKNOWN_PARTNER_ID, $id);
			
		$dbPartner->setStatus($status);
		$dbPartner->save();
		PartnerPeer::removePartnerFromCache($id);
	}
	
	
}