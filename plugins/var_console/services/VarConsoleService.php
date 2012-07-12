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
    public function initService($serviceId, $serviceName, $actionName)
    {
        parent::initService($serviceId, $serviceName, $actionName);
		
		if(!VarConsolePlugin::isAllowedPartner($this->getPartnerId()))
		{
		    throw new KalturaAPIException(KalturaErrors::SERVICE_FORBIDDEN, $this->serviceName.'->'.$this->actionName);
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
		$totalCount = PartnerPeer::doCount($c);
		
		// add pager to criteria
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
		    
			foreach ($partnerIds as $partnerId)
			{
    			list ( $reportHeader , $reportData , $totalCountNoNeeded ) = myReportsMgr::getTable( 
    				null , 
    				myReportsMgr::REPORT_TYPE_PARTNER_USAGE , 
    				$inputFilter ,
    				$pager->pageSize , 0 , // pageIndex is 0 because we are using specific ids 
    				null  , // order by  
    				"$partnerId");
    				
    			foreach ( $reportData as $line )
    			{
        			$item = new KalturaVarPartnerUsageItem();
        			$item->fromPartner(PartnerPeer::retrieveByPK($partnerId));
    				$item->fromString( $reportHeader , $line );
        			$items[] = $item;
    			}
			    
			}
			
		}
		$response = new KalturaPartnerUsageListResponse();
		
		list ( $reportHeader , $reportData , $totalCountNoNeeded ) = myReportsMgr::getTotal( 
    				null , 
    				myReportsMgr::REPORT_TYPE_PARTNER_USAGE , 
    				$inputFilter ,
    				$pager->pageSize , 0 , // pageIndex is 0 because we are using specific ids 
    				null  , // order by  
    				implode(",", $partnerIds));
		
		$total = new KalturaVarPartnerUsageItem();
		$total->fromString($reportHeader, $reportData);
		$response->total = $total; 
		$response->totalCount = $totalCount;
		$response->objects = $items;
		return $response;
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