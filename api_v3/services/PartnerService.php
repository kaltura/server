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
	
	protected function partnerRequired($actionName)
	{
		if ($actionName === 'register') {
			return false;
		}
		return parent::partnerRequired($actionName);
	}

	/**
	 * Register to Kaltura's partner program
	 * 
	 * @action register
	 * @param KalturaPartner $partner
	 * @param string $cmsPassword
	 * @return KalturaPartner
	 *
	 * @throws APIErrors::PARTNER_REGISTRATION_ERROR
	 */
	function registerAction( KalturaPartner $partner , $cmsPassword = "" )
	{
		KalturaResponseCacher::disableCache();
		
		$dbPartner = $partner->toPartner();
		$partner->validatePropertyNotNull("name");
		$partner->validatePropertyNotNull("adminName");
		$partner->validatePropertyNotNull("adminEmail");
		$partner->validatePropertyNotNull("description");
		$partner->validatePropertyMaxLength("country", 2, true);
		$partner->validatePropertyMaxLength("state", 2, true);
		
		$c = new Criteria();
		$c->addAnd(UserLoginDataPeer::LOGIN_EMAIL, $partner->adminEmail, Criteria::EQUAL);
		$c->setLimit(1);
		$existingUser = UserLoginDataPeer::doCount($c) > 0;
				
		try
		{
			if ( $cmsPassword == "" ) {
				$cmsPassword = null;
			}
			
			
			$parentPartnerId = null;
			if ( $this->getKs() && $this->getKs()->isAdmin() )
			{
				$parentPartnerId = $this->getKs()->partner_id;
				if ($parentPartnerId == Partner::ADMIN_CONSOLE_PARTNER_ID) {
		                    $parentPartnerId = null;
				}
                		else {
				
					// only if this partner is a var/grou, allow setting it as parent for the new created partner
					$parentPartner = PartnerPeer::retrieveByPK( $parentPartnerId );
					if ( ! ($parentPartner->getPartnerGroupType() == PartnerGroupType::VAR_GROUP ||
							$parentPartner->getPartnerGroupType() == PartnerGroupType::GROUP ) )
					{
						throw new KalturaAPIException( KalturaErrors::NON_GROUP_PARTNER_ATTEMPTING_TO_ASSIGN_CHILD , $parentPartnerId );
					}
				}
			}
			
			$partner_registration = new myPartnerRegistration ( $parentPartnerId );
			
			$ignorePassword = false;
			if ($existingUser && $this->getKs()->partner_id == Partner::ADMIN_CONSOLE_PARTNER_ID &&
				 kuserPeer::getKuserByEmail($partner->adminEmail, Partner::ADMIN_CONSOLE_PARTNER_ID) != null) {
				$ignorePassword = true;
			}
			
			list($pid, $subpid, $pass, $hashKey) = $partner_registration->initNewPartner( $dbPartner->getName() , $dbPartner->getAdminName() , $dbPartner->getAdminEmail() ,
				$dbPartner->getCommercialUse() , "yes" , $dbPartner->getDescription() , $dbPartner->getUrl1() , $cmsPassword , $dbPartner, $ignorePassword );

			$dbPartner = PartnerPeer::retrieveByPK( $pid );

			// send a confirmation email as well as the result of the service
			$partner_registration->sendRegistrationInformationForPartner( $dbPartner , false, $existingUser );

		}
		catch ( SignupException $se )
		{
			KalturaLog::INFO($se);
			throw new KalturaAPIException( APIErrors::PARTNER_REGISTRATION_ERROR , 'SE '.$se->getMessage() );
		}
		catch ( Exception $ex )
		{
			KalturaLog::CRIT($ex);
			// this assumes the partner name is unique - TODO - remove key from DB !
			throw new KalturaAPIException( APIErrors::PARTNER_REGISTRATION_ERROR , $ex->getMessage() );
		}		
		
		$partner = new KalturaPartner(); // start from blank
		$partner->fromPartner( $dbPartner );
		$partner->secret = $dbPartner->getSecret();
		$partner->adminSecret = $dbPartner->getAdminSecret();
		$partner->cmsPassword = $pass;
		
		return $partner;
	}


	/**
	 * Update details and settings of you existing partner
	 * 
	 * @action update
	 * @param KalturaPartner $partner
	 * @param bool $allowEmpty
	 * @return KalturaPartner
	 *
	 * @throws APIErrors::UNKNOWN_PARTNER_ID
	 */	
	function updateAction( KalturaPartner $partner, $allowEmpty = false)
	{
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
	 * Retrieve partner secret and admin secret
	 * 
	 * @action getSecrets
	 * @param int $partnerId
	 * @param string $adminEmail
	 * @param string $cmsPassword
	 * @return KalturaPartner
	 *
	 * @throws APIErrors::ADMIN_KUSER_NOT_FOUND
	 */
	function getSecretsAction( $partnerId , $adminEmail , $cmsPassword )
	{
		KalturaResponseCacher::disableCache();

		$adminKuser = null;
		try {
			$adminKuser = UserLoginDataPeer::userLoginByEmail($adminEmail, $cmsPassword, $partnerId);
		}
		catch (kUserException $e) {
			throw new KalturaAPIException ( APIErrors::ADMIN_KUSER_NOT_FOUND, "The data you entered is invalid" );
		}
		
		if (!$adminKuser || !$adminKuser->getIsAdmin()) {
			throw new KalturaAPIException ( APIErrors::ADMIN_KUSER_NOT_FOUND, "The data you entered is invalid" );
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
	 * Retrieve all info about partner
	 * This service gets no parameters, and is using the KS to know which partnerId info should be returned
	 * 
	 * @action getInfo
	 * @return KalturaPartner
	 *
	 * @throws APIErrors::UNKNOWN_PARTNER_ID
	 */		
	function getInfoAction( )
	{
		$partnerId = $this->getPartnerId();
		$dbPartner = PartnerPeer::retrieveByPK( $partnerId );
		
		if ( ! $dbPartner )
			throw new KalturaAPIException ( APIErrors::UNKNOWN_PARTNER_ID , $partnerId );
			
		$partner = new KalturaPartner();
		$partner->fromPartner( $dbPartner );
		
		return $partner;
	}
	
	/**
	 * Get usage statistics for a partner
	 * Calculation is done according to partner's package
	 *
	 * Additional data returned is a graph points of streaming usage in a timeframe
	 * The resolution can be "days" or "months"
	 *
	 * @link http://docs.kaltura.org/api/partner/usage
	 * @action getUsage
	 * @param int $year
	 * @param int $month
	 * @param string $resolution accepted values are "days" or "months"
	 * @return KalturaPartnerUsage
	 * 
	 * @throws APIErrors::UNKNOWN_PARTNER_ID
	 */
	function getUsageAction( $year = '' , $month = 1 , $resolution = "days" )
	{

		$dbPartner = PartnerPeer::retrieveByPK( $this->getPartnerId() );
		
		if ( ! $dbPartner )
			throw new KalturaAPIException ( APIErrors::UNKNOWN_PARTNER_ID , $this->getPartnerId() );


		$packages = new PartnerPackages();
		$partnerUsage = new KalturaPartnerUsage;
		$partnerPackage = $packages->getPackageDetails($dbPartner->getPartnerPackage());
		
		$report_date = date ( "Y-m-d" , time());
		
		list ( $totalStorage , $totalUsage , $totalTraffic ) = myPartnerUtils::collectPartnerUsageFromDWH($dbPartner, $partnerPackage, $report_date);
		
		$partnerUsage->hostingGB = round($totalStorage/1024 , 2); // from MB to GB
		$totalUsageGB = round($totalUsage/1024/1024 , 2); // from KB to GB
		if($partnerPackage)
		{
			$partnerUsage->Percent = round( ($totalUsageGB / $partnerPackage['cycle_bw'])*100, 2);
			$partnerUsage->packageBW = $partnerPackage['cycle_bw'];
		}
		$partnerUsage->usageGB = $totalUsageGB;
		$partnerUsage->reachedLimitDate = $dbPartner->getUsageLimitWarning();
		
		if($year != '' && is_int($year))
		{
			$graph_lines = myPartnerUtils::getPartnerUsageGraph($year, $month, $dbPartner, $resolution);
			// currently we provide only one line, output as a string.
			// in the future this could be extended to something like KalturaGraphLines object
			$partnerUsage->usageGraph = $graph_lines['line'];
		}
		
		return $partnerUsage;
	}
	
	/**
	 * Retrieve partner secret and admin secret
	 * 
	 * @action listPartnersForUser
	 * @return KalturaPartner
	 * 
	 */
	function listPartnersForUserAction()
	{	
		$partnerId = kCurrentContext::$master_partner_id;
		
		if (isset(kCurrentContext::$partner_id))
			$partnerId = kCurrentContext::$partner_id;
		
		$partners = array();
		$currentUser = kuserPeer::getKuserByPartnerAndUid($partnerId, kCurrentContext::$ks_uid, true);
		if($currentUser)
			$partners = myPartnerUtils::getPartnersArray($currentUser->getAllowedPartnerIds());	
		
		$kalturaPartners = KalturaPartnerArray::fromPartnerArray($partners );
		$response = new KalturaPartnerListResponse();
		$response->objects = $kalturaPartners;
		$response->totalCount = count($partners);	
		
		return $response;
	}

	/**
	 * List partners by filter with paging support
	 * Current implementation will only list the sub partners of the partner initiating the api call (using the current KS)
	 * 
	 * @action list
	 * @param KalturaPartnerFilter $filter
	 * @param KalturaFilterPager $pager
	 * @return KalturaPartnerListResponse
	 * 
	 * @throws KalturaErrors::NON_GROUP_PARTNER
	 */
	function listAction(KalturaPartnerFilter $filter = null, KalturaFilterPager $pager = null)
	{
		/**
		 * this action is only partially implemented to support listing sub partners of a VAR partner
		 * see action description for current implementation details
		 */
		
		// to be on the safe side
		if (is_null($this->getKs()) || is_null($this->getPartner()) || !$this->getPartnerId())
			throw new KalturaAPIException(APIErrors::MISSING_KS);
			
		$c = new Criteria();
		$subCriterion1 = $c->getNewCriterion(PartnerPeer::PARTNER_PARENT_ID, $this->getPartnerId());
		$subCriterion2 = $c->getNewCriterion(PartnerPeer::ID, $this->getPartnerId());
		$subCriterion1->addOr($subCriterion2);
		$c->add($subCriterion1);
		$dbPartners = PartnerPeer::doSelect($c);
		$partnersArray = KalturaPartnerArray::fromPartnerArray($dbPartners);
		
		$response = new KalturaPartnerListResponse();
		$response->objects = $partnersArray;
		$response->totalCount = count($partnersArray);
		return $response;
	}

}