<?php
/**
 * System partner service
 *
 * @service systemPartner
 * @package plugins.systemPartner
 * @subpackage api.services
 */
class SystemPartnerService extends KalturaBaseService
{
	public function initService($serviceId, $serviceName, $actionName)
	{
		parent::initService($serviceId, $serviceName, $actionName);
		
		// since plugin might be using KS impersonation, we need to validate the requesting
		// partnerId from the KS and not with the $_POST one
		if(!SystemPartnerPlugin::isAllowedPartner(kCurrentContext::$master_partner_id))
			throw new KalturaAPIException(SystemPartnerErrors::FEATURE_FORBIDDEN, SystemPartnerPlugin::PLUGIN_NAME);
	}

	
	/**
	 * Retrieve all info about partner
	 * This service gets partner id as parameter and accessable to the admin console partner only
	 * 
	 * @action get
	 * @param int $pId
	 * @return KalturaPartner
	 *
	 * @throws APIErrors::UNKNOWN_PARTNER_ID
	 */		
	function getAction($pId)
	{		
		$dbPartner = PartnerPeer::retrieveByPK( $pId );
		
		if ( ! $dbPartner )
			throw new KalturaAPIException ( APIErrors::UNKNOWN_PARTNER_ID , $pId );
			
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
			$usageFilter->timezoneOffset = 0;
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
			$inputFilter->from_day = date ( "Ymd" , $usageFilter->fromDate );
			$inputFilter->to_day = date ( "Ymd" , $usageFilter->toDate );
		
			$inputFilter->timeZoneOffset = $usageFilter->timezoneOffset;
	
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
	    myDbHelper::$use_alternative_con = myDbHelper::DB_HELPER_CONN_PROPEL2;
		
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
	 * @param int $id
	 * @param KalturaPartnerStatus $status
	 * @param string $reason
	 */
	public function updateStatusAction($id, $status, $reason)
	{
		$dbPartner = PartnerPeer::retrieveByPK($id);
		if (!$dbPartner)
			throw new KalturaAPIException(KalturaErrors::UNKNOWN_PARTNER_ID, $id);
			
		$dbPartner->setStatus($status);
		$dbPartner->setStatusChangeReason( $reason );
		$dbPartner->save();
		PartnerPeer::removePartnerFromCache($id);
	}
	
	/**
	 * @action getAdminSession
	 * @param int $pId
	 * @param string $userId
	 * @return string
	 */
	public function getAdminSessionAction($pId, $userId = null)
	{
		$dbPartner = PartnerPeer::retrieveByPK($pId);
		if (!$dbPartner)
			throw new KalturaAPIException(KalturaErrors::UNKNOWN_PARTNER_ID, $pId);
		
		if (!$userId) {
			$userId = $dbPartner->getAdminUserId();
		}
		
		$kuser = kuserPeer::getKuserByPartnerAndUid($pId, $userId);
		if (!$kuser) {
			throw new KalturaAPIException(KalturaErrors::INVALID_USER_ID, $userId);
		}
		if (!$kuser->getIsAdmin()) {
			throw new KalturaAPIException(KalturaErrors::USER_NOT_ADMIN, $userId);
		}
			
		$ks = "";
		kSessionUtils::createKSessionNoValidations($dbPartner->getId(), $userId, $ks, 86400, 2, "", '*,' . ks::PRIVILEGE_DISABLE_ENTITLEMENT);
		return $ks;
	}
	
	/**
	 * @action updateConfiguration
	 * @param int $pId
	 * @param KalturaSystemPartnerConfiguration $configuration
	 */
	public function updateConfigurationAction($pId, KalturaSystemPartnerConfiguration $configuration)
	{
		$dbPartner = PartnerPeer::retrieveByPK($pId);
		if (!$dbPartner)
			throw new KalturaAPIException(KalturaErrors::UNKNOWN_PARTNER_ID, $pId);
		$configuration->toUpdatableObject($dbPartner);
		$dbPartner->save();
		PartnerPeer::removePartnerFromCache($pId);
	}
	
	/**
	 * @action getConfiguration
	 * @param int $pId
	 * @return KalturaSystemPartnerConfiguration
	 */
	public function getConfigurationAction($pId)
	{
		$dbPartner = PartnerPeer::retrieveByPK($pId);
		if (!$dbPartner)
			throw new KalturaAPIException(KalturaErrors::UNKNOWN_PARTNER_ID, $pId);
			
		$configuration = new KalturaSystemPartnerConfiguration();
		$configuration->fromObject($dbPartner, $this->getResponseProfile());
		return $configuration;
	}
	
	/**
	 * @action getPackages
	 * @return KalturaSystemPartnerPackageArray
	 */
	public function getPackagesAction()
	{
		$partnerPackages = new PartnerPackages();
		$packages = $partnerPackages->listPackages();
		$partnerPackages = new KalturaSystemPartnerPackageArray();
		$partnerPackages->fromArray($packages);
		return $partnerPackages;
	}
	
	/**
	 * @action getPackagesClassOfService
	 * @return KalturaSystemPartnerPackageArray
	 */
	public function getPackagesClassOfServiceAction()
	{
		$partnerPackages = new PartnerPackages();
		$packages = $partnerPackages->listPackagesClassOfService();
		$partnerPackages = new KalturaSystemPartnerPackageArray();
		$partnerPackages->fromArray($packages);
		return $partnerPackages;
	}
	
	/**
	 * @action getPackagesVertical
	 * @return KalturaSystemPartnerPackageArray
	 */
	public function getPackagesVerticalAction()
	{
		$partnerPackages = new PartnerPackages();
		$packages = $partnerPackages->listPackagesVertical();
		$partnerPackages = new KalturaSystemPartnerPackageArray();
		$partnerPackages->fromArray($packages);
		return $partnerPackages;
	}
	
	/**
	 * @action getPlayerEmbedCodeTypes
	 * @return KalturaPlayerEmbedCodeTypesArray
	 */
	public function getPlayerEmbedCodeTypesAction()
	{
		$map = kConf::getMap('players');
		return KalturaPlayerEmbedCodeTypesArray::fromDbArray($map['embed_code_types'], $this->getResponseProfile());
	}
	
	/**
	 * @action getPlayerDeliveryTypes
	 * @return KalturaPlayerDeliveryTypesArray
	 */
	public function getPlayerDeliveryTypesAction()
	{
		$map = kConf::getMap('players');
		return KalturaPlayerDeliveryTypesArray::fromDbArray($map['delivery_types'], $this->getResponseProfile());
	}

	/**
	 * 
	 * @action resetUserPassword
	 * @param string $userId
	 * @param int $pId
	 * @param string $newPassword
	 * @throws KalturaAPIException
	 */
	public function resetUserPasswordAction($userId, $pId, $newPassword)
	{
		if ($pId == Partner::ADMIN_CONSOLE_PARTNER_ID || $pId == Partner::BATCH_PARTNER_ID)
		{
			throw new KalturaAPIException(KalturaErrors::CANNOT_RESET_PASSWORD_FOR_SYSTEM_PARTNER);
		}				
		//get loginData using userId and PartnerId 
		$kuser = kuserPeer::getKuserByPartnerAndUid ($pId, $userId);
		if (!$kuser){
			throw new KalturaAPIException(KalturaErrors::USER_NOT_FOUND);
		}
		$userLoginDataId = $kuser->getLoginDataId();
		$userLoginData = UserLoginDataPeer::retrieveByPK($userLoginDataId);
		
		// check if login data exists
		if (!$userLoginData) {
			throw new KalturaAPIException(KalturaErrors::LOGIN_DATA_NOT_FOUND);
		}
		try {
			UserLoginDataPeer::checkPasswordValidation($newPassword, $userLoginData);
		}
		catch (kUserException $e) {
			$code = $e->getCode();
			if ($code == kUserException::PASSWORD_STRUCTURE_INVALID) {
				$passwordRules = $userLoginData->getInvalidPasswordStructureMessage();
				$passwordRules = str_replace( "\\n", "<br>", $passwordRules );
				$passwordRules = "<br>" . $passwordRules; // Add a newline prefix
				throw new KalturaAPIException(KalturaErrors::PASSWORD_STRUCTURE_INVALID, $passwordRules);
			}
			else if ($code == kUserException::PASSWORD_ALREADY_USED) {
				throw new KalturaAPIException(KalturaErrors::PASSWORD_ALREADY_USED);
			}			
			throw new KalturaAPIException(KalturaErrors::INTERNAL_SERVERL_ERROR);						
		}
		// update password if requested
		if ($newPassword) {
			$password = $userLoginData->resetPassword($newPassword);
		}		
		$userLoginData->save();
	}
	
	
	/**
	 * @action listUserLoginData
	 * @param KalturaUserLoginDataFilter $filter
	 * @param KalturaFilterPager $pager
	 * @return KalturaUserLoginDataListResponse
	 */
	public function listUserLoginDataAction(KalturaUserLoginDataFilter $filter = null, KalturaFilterPager $pager = null)
	{
		if (is_null($filter))
			$filter = new KalturaUserLoginDataFilter();
			
		if (is_null($pager))
			$pager = new KalturaFilterPager();
			
		return $filter->getListResponse($pager, $this->getResponseProfile());
	}
	
	
}
