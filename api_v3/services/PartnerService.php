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
	const KALTURAS_PARTNER_EMAIL_CHANGE = 52;
	// use initService to add a peer to the partner filter
	/**
	 * @ignore
	 */
	public function initService ($partner_id , $puser_id , $ks_str , $service_name , $action )
	{
		parent::initService ($partner_id , $puser_id , $ks_str , $service_name , $action );
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
		
		try
		{
			if ( $cmsPassword == "" ) {
				$cmsPassword = null;
			}
			
			
			$parentPartnerId = null;
			if ( $this->getKs() && $this->getKs()->isAdmin() )
			{
				$parentPartnerId = $this->getKs()->partner_id ;
				
				// only if this partner is a var/grou, allow setting it as parent for the new created partner
				$parentPartner = PartnerPeer::retrieveByPK( $parentPartnerId );
				if ( ! ($parentPartner->getPartnerGroupType() == Partner::PARTNER_GROUP_TYPE_VAR ||
						$parentPartner->getPartnerGroupType() == Partner::PARTNER_GROUP_TYPE_GROUP ) )
				{
					throw new KalturaAPIException( KalturaErrors::NON_GROUP_PARTNER_ATTEMPTING_TO_ASSIGN_CHILD , $parentPartnerId );
				}
			}
			
			$partner_registration = new myPartnerRegistration ( $parentPartnerId );
			
			list($pid, $subpid, $pass, $hashKey) = $partner_registration->initNewPartner( $dbPartner->getName() , $dbPartner->getAdminName() , $dbPartner->getAdminEmail() ,
				$dbPartner->getCommercialUse() , "yes" , $dbPartner->getDescription() , $dbPartner->getUrl1() , $cmsPassword , $dbPartner );

			$dbPartner = PartnerPeer::retrieveByPK( $pid );

			// send a confirmation email as well as the result of the service
			$partner_registration->sendRegistrationInformationForPartner( $dbPartner , $subpid , $pass, false, $hashKey );

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
		// TODO: decide - why we need 2 different services for update ? ? ?

		$dbPartner = PartnerPeer::retrieveByPK( $this->getPartnerId() );
		
		if ( ! $dbPartner )
			throw new KalturaAPIException ( APIErrors::UNKNOWN_PARTNER_ID , $this->getPartnerId() );
		
		$partnerUpdate = $partner->toPartner();
		
		if ( isset($partner->adminEmail) && $partner->adminEmail && $partnerUpdate->getAdminEmail() != $dbPartner->getAdminEmail() )
		{
			myPartnerUtils::emailChangedEmail($this->getPartnerId(), $dbPartner->getAdminEmail(), $partnerUpdate->getAdminEmail(), $dbPartner->getName() , partnerservice::KALTURAS_PARTNER_EMAIL_CHANGE );
		}

		// TODO - what is the $allowEmpty policy  ?
		baseObjectUtils::autoFillObjectFromObject ( $partnerUpdate , $dbPartner , $allowEmpty );
		
		$dbPartner->save();
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
		
		$loginData = UserLoginDataPeer::getByEmail($adminEmail);
		if (!$loginData) {
			throw new KalturaAPIException ( APIErrors::ADMIN_KUSER_NOT_FOUND, "The data you entered is invalid" );
		}
		if (!$loginData->isPasswordValid($cmsPassword)) {
			throw new KalturaAPIException ( APIErrors::ADMIN_KUSER_NOT_FOUND, "The data you entered is invalid" );
		}
		
		$c = new Criteria();
		$c->add ( kuserPeer::EMAIL , $adminEmail );
		$c->add ( kuserPeer::PARTNER_ID , $partnerId );
		$c->add ( kuserPeer::LOGIN_DATA_ID , $loginData->getId() );
		$c->add ( kuserPeer::IS_ADMIN, true);
		$c->setLimit ( 20 ); // just to limit the number of partners returned
		$adminKuser = kuserPeer::doSelectOne( $c );
		
		// be sure to return the same error if there are no admins in the list and when there are none matched -
		// so no hint about existing admin will leak 
		if ( count ( $adminKuser ) < 1 )
			throw new KalturaAPIException ( APIErrors::ADMIN_KUSER_NOT_FOUND, "The data you entered is invalid" );

		KalturaLog::log( "Admin Kuser found, going to validate password", KalturaLog::INFO );
		
		$dbPartner = PartnerPeer::retrieveByPK( $partnerId );
		$partner = new KalturaPartner;
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
		
		$report_date = dateUtils::todayOffset(-3);
		
		list ( $totalStorage , $totalUsage , $totalTraffic ) = myPartnerUtils::collectPartnerUsageFromDWH($dbPartner, $partnerPackage, $report_date);
		
		$partnerUsage->hostingGB = round($totalStorage/1024 , 2); // from MB to GB
		$totalUsageGB = round($totalUsage/1024/1024 , 2); // from KB to GB
		$partnerUsage->Percent = round( ($totalUsageGB / $partnerPackage['cycle_bw'])*100, 2);
		$partnerUsage->packageBW = $partnerPackage['cycle_bw'];
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

}