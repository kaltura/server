<?php
/**
 * partner service allows you to change/manage your partner personal details and settings as well
 *
 * @service partner
 * @package api
 * @subpackage services
 */
class PartnerService extends KalturaBaseService 
{
	const PARENT_PARTNER_ID = 'parentPartnerId';
	const IS_ADMIN_OR_VAR_CONSOLE = 'isAdminOrVarConsole';
	protected function partnerRequired($actionName)
	{
		if ($actionName === 'register') {
			return false;
		}
		return parent::partnerRequired($actionName);
	}
	
	
	/**
	 * Create a new Partner object
	 *
	 * @action registrationValidation
	 * @param KalturaPartner $partner
	 * @param string $cmsPassword
	 * @param int $templatePartnerId
	 * @param bool $silent
	 * @return bool
	 * @ksOptional
	 *
	 * @throws APIErrors::PARTNER_REGISTRATION_ERROR
	 */
	public function registrationValidationAction(KalturaPartner $partner, $cmsPassword = "" ,$templatePartnerId = null, $silent = false)
	{
		KalturaResponseCacher::disableCache();
		
		$dbPartner = $partner->toPartner();
		
		$c = new Criteria();
		$c->addAnd(UserLoginDataPeer::LOGIN_EMAIL, $partner->adminEmail, Criteria::EQUAL);
		$existingUser = UserLoginDataPeer::doSelectOne($c);
		/* @var $exisitingUser UserLoginData */
		
		try
		{
			$cmsPassword = ($cmsPassword == "") ? null : $cmsPassword;
			
			$parentPartnerInfo = $this->getParentPartnerInfo($templatePartnerId);
			$parentPartnerId = $parentPartnerInfo[self::PARENT_PARTNER_ID];
			$isAdminOrVarConsole = $parentPartnerInfo[self::IS_ADMIN_OR_VAR_CONSOLE];
			
			$partner_registration = new myPartnerRegistration ($parentPartnerId);
			
			$ignorePassword = $this->getIgnorePassword($existingUser, $isAdminOrVarConsole, $partner->adminEmail,
			                                           $parentPartnerId);
			$partner_registration->validateNewPartner($dbPartner->getName(), $dbPartner->getAdminName(), $dbPartner->getAdminEmail(),
				$dbPartner->getCommercialUse(), "yes", $dbPartner->getDescription(), $dbPartner->getUrl1(),
				$cmsPassword, $dbPartner, $ignorePassword, $templatePartnerId );
			return true;
		}
		catch ( SignupException $se )
		{
			throw new KalturaAPIException( APIErrors::PARTNER_REGISTRATION_ERROR, $se->getMessage());
		}
		catch ( Exception $ex )
		{
			KalturaLog::CRIT($ex);
			// this assumes the partner name is unique - TODO - remove key from DB !
			throw new KalturaAPIException( APIErrors::PARTNER_REGISTRATION_ERROR, 'Unknown error');
		}
	}
	
	protected function getIgnorePassword($existingUser, $isAdminOrVarConsole, $adminEmail, $parentPartnerId)
	{
		$ignorePassword = false;
		if ($existingUser && $isAdminOrVarConsole)
		{
			kuserPeer ::setUseCriteriaFilter(false);
			$kuserOfLoginData = kuserPeer ::getKuserByEmail($adminEmail, $existingUser -> getConfigPartnerId());
			kuserPeer ::setUseCriteriaFilter(true);
			if ($kuserOfLoginData &&
				(!$parentPartnerId || ($parentPartnerId == $existingUser -> getConfigPartnerId())))
			{
				$ignorePassword = true;
			}
		}
		return $ignorePassword;
	}
	
	/**
	 * Create a new Partner object
	 * 
	 * @action register
	 * @param KalturaPartner $partner
	 * @param string $cmsPassword
	 * @param int $templatePartnerId
	 * @param bool $silent
	 * @return KalturaPartner
	 * @ksOptional
	 *
	 * @throws APIErrors::PARTNER_REGISTRATION_ERROR
	 */
	public function registerAction( KalturaPartner $partner , $cmsPassword = "" , $templatePartnerId = null, $silent = false)
	{
		KalturaResponseCacher::disableCache();

		$blockedCountriesList = kConf::getArrayValue( "blockedRegistration", partner::GLOBAL_ACCESS_LIMITATIONS, kConfMapNames::RUNTIME_CONFIG, "");
		if($partner->isSelfServe && !myPartnerUtils::isRequestFromAllowedCountry($blockedCountriesList, null) )
		{
			throw new KalturaAPIException( APIErrors::PARTNER_REGISTRATION_ERROR, "Action is temporarily blocked from this country");
		}

		try
		{
			$cmsPassword = ($cmsPassword == "") ? null : $cmsPassword;
			
			$c = new Criteria();
			$c->addAnd(UserLoginDataPeer::LOGIN_EMAIL, $partner->adminEmail, Criteria::EQUAL);
			$existingUser = UserLoginDataPeer::doSelectOne($c);
			/* @var $exisitingUser UserLoginData */
			$dbPartner = $partner->toPartner();
			$parentPartnerInfo = $this->getParentPartnerInfo($templatePartnerId);
			$parentPartnerId = $parentPartnerInfo[self::PARENT_PARTNER_ID];
			$isAdminOrVarConsole = $parentPartnerInfo[self::IS_ADMIN_OR_VAR_CONSOLE];
			$partner_registration = new myPartnerRegistration ( $parentPartnerId );
			$ignorePassword = $this->getIgnorePassword($existingUser, $isAdminOrVarConsole, $partner->adminEmail,$parentPartnerId);
			list($pid, $subpid, $pass, $hashKey) = $partner_registration->initNewPartner( $dbPartner->getName(),
				$dbPartner->getAdminName(), $dbPartner->getAdminEmail(), $dbPartner->getCommercialUse(), "yes",
				$dbPartner->getDescription(), $dbPartner->getUrl1(), $cmsPassword, $dbPartner, $ignorePassword,
				$templatePartnerId );

			$dbPartner = PartnerPeer::retrieveByPK( $pid );

			// send a confirmation email as well as the result of the service
			$partner_registration->sendRegistrationInformationForPartner( $dbPartner , false, $existingUser, $silent );

		}
		catch ( SignupException $se )
		{
			throw new KalturaAPIException( APIErrors::PARTNER_REGISTRATION_ERROR, $se->getMessage());
		}
		catch (KalturaAPIException $ex)
		{
			KalturaLog::CRIT($ex);
			$exceptionMessage = (str_replace('KalturaPartner::', '', $ex->getMessage()));
			throw new KalturaAPIException( APIErrors::PARTNER_REGISTRATION_ERROR, $exceptionMessage);
		}
		catch ( Exception $ex )
		{
			KalturaLog::CRIT($ex);
			// this assumes the partner name is unique - TODO - remove key from DB !
			throw new KalturaAPIException( APIErrors::PARTNER_REGISTRATION_ERROR, 'Unknown error');
		}
		
		$partner = new KalturaPartner(); // start from blank
		$partner->fromPartner( $dbPartner );
		$partner->secret = $dbPartner->getSecret();
		$partner->adminSecret = $dbPartner->getAdminSecret();
		$partner->cmsPassword = $pass;
		
		return $partner;
	}

	protected function getParentPartnerInfo($templatePartnerId = null)
	{
		$parentPartnerId = null;
		$isAdminOrVarConsole = false;
		if ($this -> getKs() && $this -> getKs() -> isAdmin())
		{
			$parentPartnerId = $this -> getKs() -> partner_id;
			if (in_array($parentPartnerId ,array(Partner::ADMIN_CONSOLE_PARTNER_ID, Partner::SELF_SERVE_PARTNER_ID)))
			{
				$parentPartnerId = null;
				$isAdminOrVarConsole = true;
			}
			else
			{
				// only if this partner is a var/group, allow setting it as parent for the new created partner
				$parentPartner = PartnerPeer ::retrieveByPK($parentPartnerId);
				if (!($parentPartner -> getPartnerGroupType() == PartnerGroupType::VAR_GROUP ||
					$parentPartner -> getPartnerGroupType() == PartnerGroupType::GROUP))
				{
					throw new KalturaAPIException(KalturaErrors::NON_GROUP_PARTNER_ATTEMPTING_TO_ASSIGN_CHILD,
					                              $parentPartnerId);
				}
				$isAdminOrVarConsole = true;
				if ($templatePartnerId)
				{
					$templatePartner = PartnerPeer ::retrieveByPK($templatePartnerId);
					if (!$templatePartner || $templatePartner -> getPartnerParentId() != $parentPartnerId)
					{
						throw new KalturaAPIException(KalturaErrors::NON_GROUP_PARTNER_ATTEMPTING_TO_ASSIGN_CHILD,
						                              $parentPartnerId);
					}
				}
			}
		}
		return Array('parentPartnerId' => $parentPartnerId, 'isAdminOrVarConsole' => $isAdminOrVarConsole);
	}

	/**
	 * Update details and settings of an existing partner
	 * 
	 * @action update
	 * @param KalturaPartner $partner
	 * @param bool $allowEmpty
	 * @return KalturaPartner
	 *
	 * @throws APIErrors::UNKNOWN_PARTNER_ID
	 */	
	public function updateAction( KalturaPartner $partner, $allowEmpty = false)
	{
		$vars_arr=get_object_vars($partner);
		foreach ($vars_arr as $key => $val){
		    if (is_string($partner->$key)){
                        $partner->$key=strip_tags($partner->$key);
                    }    
                }   
		$dbPartner = PartnerPeer::retrieveByPK( $this->getPartnerId() );
		
		if ( ! $dbPartner )
			throw new KalturaAPIException ( APIErrors::UNKNOWN_PARTNER_ID , $this->getPartnerId() );
		
		try {
			$dbPartner = $partner->toUpdatableObject($dbPartner);
			$dbPartner->save();
		}
		catch(kUserException $e) {
			if ($e->getCode() === kUserException::USER_NOT_FOUND) {
				throw new KalturaAPIException(KalturaErrors::USER_NOT_FOUND);
			}
			throw $e;
		}
		catch(kPermissionException $e) {
			if ($e->getCode() === kPermissionException::ACCOUNT_OWNER_NEEDS_PARTNER_ADMIN_ROLE) {
				throw new KalturaAPIException(KalturaErrors::ACCOUNT_OWNER_NEEDS_PARTNER_ADMIN_ROLE);
			}
			throw $e;			
		}		
		
		$partner = new KalturaPartner();
		$partner->fromPartner( $dbPartner );
		
		return $partner;
	}
	
	
	/**
	 * Retrieve partner object by Id
	 * 
	 * @action get
	 * @param bigint $id
	 * @return KalturaPartner
	 *
	 * @throws APIErrors::INVALID_PARTNER_ID
	 */
	public function getAction ($id = null)
	{
	    if (is_null($id))
	    {
	        $id = $this->getPartnerId();
	    }
	    
	    $c = PartnerPeer::getDefaultCriteria();
	    
		$c->addAnd(PartnerPeer::ID ,$id);
		
		$dbPartner = PartnerPeer::doSelectOne($c);
		if (is_null($dbPartner))
		{
		    throw new KalturaAPIException(KalturaErrors::INVALID_PARTNER_ID, $id);
		}

		if($this->getPartnerId() != $id)
		{
			myPartnerUtils::addPartnerToCriteria('kuser', $id, true);
		}

		$partner = new KalturaPartner();
		$partner->fromObject($dbPartner, $this->getResponseProfile());
		
		return $partner;
	}

	/**
	 * Retrieve partner secret and admin secret
	 * 
	 * @action getSecrets
	 * @param int $partnerId
	 * @param string $adminEmail
	 * @param string $cmsPassword
	 * @param string $otp
	 * @return KalturaPartner
	 * @ksIgnored
	 *
	 * @throws APIErrors::ADMIN_KUSER_NOT_FOUND
	 */
	public function getSecretsAction( $partnerId , $adminEmail , $cmsPassword, $otp = null )
	{
		KalturaResponseCacher::disableCache();

		$adminKuser = null;
		try {
			$adminKuser = UserLoginDataPeer::userLoginByEmail($adminEmail, $cmsPassword, $partnerId, $otp);
		}
		catch (kUserException $e) {
			throw new KalturaAPIException ( APIErrors::USER_DATA_ERROR, "The data you entered is invalid" );
		}
		
		if (!$adminKuser || !$adminKuser->getIsAdmin()) {
			throw new KalturaAPIException ( APIErrors::USER_DATA_ERROR, "The data you entered is invalid" );
		}
		
		KalturaLog::log( "Admin Kuser found, going to validate password", KalturaLog::INFO );
		
		// user logged in - need to re-init kPermissionManager in order to determine current user's permissions
		$ks = null;
		kSessionUtils::createKSessionNoValidations ( $partnerId ,  $adminKuser->getPuserId() , $ks , 86400 , $adminKuser->getIsAdmin() , "" , '*' );
		kCurrentContext::initKsPartnerUser($ks);
		kPermissionManager::init();		
		
		$dbPartner = PartnerPeer::retrieveByPK( $partnerId );
		$partner = new KalturaPartner();
		$partner->fromPartner( $dbPartner );
		$partner->cmsPassword = $cmsPassword;
		
		return $partner;
	}
	
	/**
	 * Retrieve all info attributed to the partner
	 * This action expects no parameters. It returns information for the current KS partnerId.
	 * 
	 * @action getInfo
	 * @return KalturaPartner
	 * @deprecated
	 * @throws APIErrors::UNKNOWN_PARTNER_ID
	 */		
	public function getInfoAction( )
	{
		return $this->getAction();
	}
	
	/**
	 * Get usage statistics for a partner
	 * Calculation is done according to partner's package
	 *
	 * Additional data returned is a graph points of streaming usage in a time frame
	 * The resolution can be "days" or "months"
	 *
	 * @link http://docs.kaltura.org/api/partner/usage
	 * @action getUsage
	 * @param int $year
	 * @param int $month
	 * @param KalturaReportInterval $resolution
	 * @return KalturaPartnerUsage
	 * 
	 * @throws APIErrors::UNKNOWN_PARTNER_ID
	 * @deprecated use getStatistics instead
	 */
	public function getUsageAction($year = '', $month = 1, $resolution = "days")
	{
		$dbPartner = PartnerPeer::retrieveByPK($this->getPartnerId());
		
		if(!$dbPartner)
			throw new KalturaAPIException(APIErrors::UNKNOWN_PARTNER_ID, $this->getPartnerId());
		
		$packages = new PartnerPackages();
		$partnerUsage = new KalturaPartnerUsage();
		$partnerPackage = $packages->getPackageDetails($dbPartner->getPartnerPackage());
		
		$report_date = date("Y-m-d", time());
		
		list($totalStorage, $totalUsage, $totalTraffic) = myPartnerUtils::collectPartnerUsageFromDWH($dbPartner, $partnerPackage, $report_date);
		
		$partnerUsage->hostingGB = round($totalStorage / 1024, 2); // from MB to GB
		$totalUsageGB = round($totalUsage / 1024 / 1024, 2); // from KB to GB
		if($partnerPackage)
		{
			$partnerUsage->Percent = round(($totalUsageGB / $partnerPackage['cycle_bw']) * 100, 2);
			$partnerUsage->packageBW = $partnerPackage['cycle_bw'];
		}
		$partnerUsage->usageGB = $totalUsageGB;
		$partnerUsage->reachedLimitDate = $dbPartner->getUsageLimitWarning();
		
		if($year != '')
		{
			$startDate = gmmktime(0, 0, 0, $month, 1, $year);
			$endDate = gmmktime(0, 0, 0, $month, date('t', $startDate), $year);
			
			if($resolution == reportInterval::MONTHS)
			{
				$startDate = gmmktime(0, 0, 0, 1, 1, $year);
				$endDate = gmmktime(0, 0, 0, 12, 31, $year);
				
				if(intval(date('Y')) == $year)
					$endDate = time();
			}
			
			$usageGraph = myPartnerUtils::getPartnerUsageGraph($startDate, $endDate, $dbPartner, $resolution);
			// currently we provide only one line, output as a string.
			// in the future this could be extended to something like KalturaGraphLines object
			$partnerUsage->usageGraph = $usageGraph;
		}
		
		return $partnerUsage;
	}
	
	/**
	 * Get usage statistics for a partner
	 * Calculation is done according to partner's package
	 *
	 * @action getStatistics
	 * @return KalturaPartnerStatistics
	 * 
	 * @throws APIErrors::UNKNOWN_PARTNER_ID
	 */
	public function getStatisticsAction()
	{
		$dbPartner = PartnerPeer::retrieveByPK($this->getPartnerId());
		
		if(!$dbPartner)
			throw new KalturaAPIException(APIErrors::UNKNOWN_PARTNER_ID, $this->getPartnerId());
		
		$packages = new PartnerPackages();
		$partnerUsage = new KalturaPartnerStatistics();
		$partnerPackage = $packages->getPackageDetails($dbPartner->getPartnerPackage());
		
		$report_date = date("Y-m-d", time());
		
		list($totalStorage, $totalUsage, $totalTraffic) = myPartnerUtils::collectPartnerStatisticsFromDWH($dbPartner, $partnerPackage, $report_date);
		
		$partnerUsage->hosting = round($totalStorage / 1024, 2); // from MB to GB
		$totalUsageGB = round($totalUsage / 1024 / 1024, 2); // from KB to GB
		if($partnerPackage)
		{
			$partnerUsage->usagePercent = round(($totalUsageGB / $partnerPackage['cycle_bw']) * 100, 2);
			$partnerUsage->packageBandwidthAndStorage = $partnerPackage['cycle_bw'];
		}
		if($totalTraffic)
		{
			$partnerUsage->bandwidth = round($totalTraffic / 1024 / 1024, 2); // from KB to GB
		}
		$partnerUsage->usage = $totalUsageGB;
		$partnerUsage->reachedLimitDate = $dbPartner->getUsageLimitWarning();
		
		return $partnerUsage;
	}
	
	/**
	 * Retrieve a list of partner objects which the current user is allowed to access.
	 * 
	 * @action listPartnersForUser
	 * @param KalturaPartnerFilter $partnerFilter
	 * @param KalturaFilterPager $pager
	 * @return KalturaPartnerListResponse
	 * @throws KalturaErrors::INVALID_USER_ID
	 * 
	 */
	public function listPartnersForUserAction(KalturaPartnerFilter $partnerFilter = null, KalturaFilterPager $pager = null)
	{
		$partnerId = kCurrentContext::getCurrentPartnerId();
		$c = new Criteria();
		$currentUser = kuserPeer::getKuserByPartnerAndUid($partnerId, kCurrentContext::$ks_uid, true);

		if(!$currentUser)
		{
		    throw new KalturaAPIException(KalturaErrors::INVALID_USER_ID);
		}
		
		if (!$pager)
		{
		    $pager = new KalturaFilterPager();
		}
		
		$dbFilter = null;
		if ($partnerFilter)
		{
		    $dbFilter = new partnerFilter();
		    $partnerFilter->toObject($dbFilter);
		}	
			
		$allowedIds = $currentUser->getAllowedPartnerIds($dbFilter);
		$pager->attachToCriteria($c);
		$partners = myPartnerUtils::getPartnersArray($allowedIds, $c);	
		$kalturaPartners = KalturaPartnerArray::fromPartnerArray($partners);
		$response = new KalturaPartnerListResponse();
		$response->objects = $kalturaPartners;
		$response->totalCount = count($partners);	
		
		return $response;
	}

	/**
	 * List partners by filter with paging support
	 * Current implementation will only list the sub partners of the partner initiating the API call (using the current KS).
	 * This action is only partially implemented to support listing sub partners of a VAR partner.
	 * @action list
	 * @param KalturaPartnerFilter $filter
	 * @param KalturaFilterPager $pager
	 * @return KalturaPartnerListResponse
	 */
	public function listAction(KalturaPartnerFilter $filter = null, KalturaFilterPager $pager = null)
	{
	    if (is_null($filter))
	    {
	        $filter = new KalturaPartnerFilter();
	    }
	    
	    if (is_null($pager))
	    {
	        $pager = new KalturaFilterPager();   
	    }
	    
	    $partnerFilter = new partnerFilter();
	    $filter->toObject($partnerFilter);
	    
	    $c = PartnerPeer::getDefaultCriteria();
		
	    $partnerFilter->attachToCriteria($c);
		$response = new KalturaPartnerListResponse();
		$response->totalCount = PartnerPeer::doCount($c);
		
	    $pager->attachToCriteria($c);
	    $dbPartners = PartnerPeer::doSelect($c);
	    
		$partnersArray = KalturaPartnerArray::fromPartnerArray($dbPartners);
		
		$response->objects = $partnersArray;
		return $response;
	}
	
	/**
	 * List partner's current processes' statuses
	 * 
	 * @action listFeatureStatus
	 * @throws APIErrors::UNKNOWN_PARTNER_ID
	 * @return KalturaFeatureStatusListResponse
	 */
	public function listFeatureStatusAction()
	{
		if (is_null($this->getKs()) || is_null($this->getPartner()) || !$this->getPartnerId())
			throw new KalturaAPIException(APIErrors::MISSING_KS);
			
		$dbPartner = $this->getPartner();
		if ( ! $dbPartner )
			throw new KalturaAPIException ( APIErrors::UNKNOWN_PARTNER_ID , $this->getPartnerId() );
		
		$dbFeaturesStatus = $dbPartner->getFeaturesStatus();
		
		$featuresStatus = KalturaFeatureStatusArray::fromDbArray($dbFeaturesStatus, $this->getResponseProfile());
		
		$response = new KalturaFeatureStatusListResponse();
		$response->objects = $featuresStatus;
		$response->totalCount = count($featuresStatus);
		
		return $response;
	}
	
	/**
	 * Count partner's existing sub-publishers (count includes the partner itself).
	 * 
	 * @action count
	 * @param KalturaPartnerFilter $filter
	 * @return int
	 */
	public function countAction (KalturaPartnerFilter $filter = null)
	{
	    if (!$filter)
		$filter = new KalturaPartnerFilter();
		
	    $dbFilter = new partnerFilter();
	    $filter->toObject($dbFilter);
	    
	    $c = PartnerPeer::getDefaultCriteria();
	    $dbFilter->attachToCriteria($c);
	    
	    return PartnerPeer::doCount($c);
	}

	/**
	 * Returns partner public info by Id
	 *
	 * @action getPublicInfo
	 * @param bigint $id
	 * @return KalturaPartnerPublicInfo
	 *
	 * @throws APIErrors::INVALID_PARTNER_ID
	 */
	public function getPublicInfoAction ($id = null)
	{
		if (!$id)
		{
			throw new KalturaAPIException(KalturaErrors::INVALID_PARTNER_ID, $id);
		}

		$dbPartner = PartnerPeer::retrieveByPK($id);
		if (is_null($dbPartner))
		{
			throw new KalturaAPIException(KalturaErrors::INVALID_PARTNER_ID, $id);
		}

		$response = new KalturaPartnerPublicInfo();
		$response->fromObject($dbPartner, $this->getResponseProfile());

		return $response;
	}
	
}
