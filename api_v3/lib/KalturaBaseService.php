<?php
/**
 * @abstract
 * @package api
 * @subpackage services
 */
abstract class KalturaBaseService 
{
	/**
	 * @var ks
	 */
	private $ks = null;
	
	/**
	 * @var Partner
	 */
	private $partner = null;
	
	/**
	 * @var kuser
	 */
	private $kuser = null;

	/**
	 * @var KalturaPartner
	 */
	private $operating_partner = null;
	 
	
	protected $private_partner_data = null; /// will be used internally and from the actual services for setting the
	
	protected $impersonatedPartnerId = null;
	
	protected $serviceId = null;
	
	protected $serviceName = null;
	
	protected $actionName = null;
	
	protected $partnerGroup = null;
	
	public function __construct()
	{
		DbManager::setConfig(kConf::getDB());
		DbManager::initialize();
		//TODO: initialize $this->serviceName here instead of in initService method
	}	

	
	public function __destruct( )
	{
		DbManager::shutdown();
	}
	
	
	/**
	 * Should return true or false for allowing/disallowing kaltura network filter for the given action.
	 * Can be extended to partner specific checks etc...
	 * @return true if "kaltura network" is enabled for the given action or false otherwise
	 * @param string $actionName action name
	 */
	protected function kalturaNetworkAllowed($actionName)
	{
		return false;
	}
	
	/**
	 * Should return 'false' if no partner is required for that action, to make it usable with no KS or partner_id variable.
	 * Return 'true' otherwise (most actions).
	 * @param string $actionName
	 */
	protected function partnerRequired($actionName)
	{
		return true;
	}
	
	/**
	 * Should return 'true' if global partner (partner 0) should be added to the partner group filter for the given action, or 'false' otherwise.
	 * Enter description here ...
	 * @param string $actionName action name
	 */
	protected function globalPartnerAllowed($actionName)
	{
		return false;
	} 
		
	public function initService($serviceId, $serviceName, $actionName)
	{	
		// init service and action name
		$this->serviceId = $serviceId;
		$this->serviceName = $serviceName;
		$this->actionName  = $actionName;
		
		// impersonated partner = partner parameter from the request
		$this->impersonatedPartnerId = kCurrentContext::$partner_id;
		
		$this->ks = kCurrentContext::$ks_object ? kCurrentContext::$ks_object : null;
		
		// operating partner = partner from the request or the ks partner
		$partnerId = kCurrentContext::$partner_id;
		if (is_null($partnerId) || !strlen($partnerId)) {
			$partnerId = kCurrentContext::$ks_partner_id;
		}
		$this->partner = PartnerPeer::retrieveByPK( $partnerId );
		if (!$this->partner) { $this->partner = null; }		

		// check if current aciton is allowed and if private partner data access is allowed
		$allowPrivatePartnerData = false;
		$actionPermitted = $this->isPermitted($allowPrivatePartnerData);
		
		// action not permitted at all, not even kaltura network
		if (!$actionPermitted)
		{			
			$e = new KalturaAPIException ( APIErrors::SERVICE_FORBIDDEN, $this->serviceId.'->'.$this->actionName); //TODO: should sometimes thorow MISSING_KS instead
			header("X-Kaltura:error-".$e->getCode());
			header("X-Kaltura-App: exiting on error ".$e->getCode()." - ".$e->getMessage());
			throw $e;		
		}
		
		// init partner filter parameters
		$this->private_partner_data = $allowPrivatePartnerData;
		$this->partnerGroup = kPermissionManager::getPartnerGroup($this->serviceId, $this->actionName);
		if ($this->globalPartnerAllowed($this->actionName)) {
			$this->partnerGroup = PartnerPeer::GLOBAL_PARTNER.','.trim($this->partnerGroup,',');
		}
		
		// apply partner filters according to current context and permissions
		myPartnerUtils::resetAllFilters();
		myPartnerUtils::applyPartnerFilters($partnerId ,$this->private_partner_data ,$this->partnerGroup ,$this->kalturaNetworkAllowed($this->actionName));
	}
	
/* >--------------------- Security and config settings ----------------------- */

	/**
	 * Check if current action is permitted for current context (ks/partner/user)
	 * @param bool $allowPrivatePartnerData true if access to private partner data is allowed, false otherwise (kaltura network)
	 * @throws APIErrors::MISSING_KS
	 */
	protected function isPermitted(&$allowPrivatePartnerData)
	{		
		// if no partner defined but required -> error MISSING_KS
		if (!$this->partner && $this->partnerRequired($this->actionName))
		{
			throw new KalturaAPIException(APIErrors::MISSING_KS);
		}
		
		// check if actions is permitted for current context
		$isActionPermitted = kPermissionManager::isActionPermitted($this->serviceId, $this->actionName);
		
		// if action permitted - no problem to access action and the private partner data
		if ($isActionPermitted) {
			$allowPrivatePartnerData = true; // allow private partner data
			return true; // action permitted with access to partner private data
		}
		
		// action not permitted for current user - check if kaltura network is allowed
		if (!kCurrentContext::$ks && $this->kalturaNetworkAllowed($this->actionName))
		{
			// if the service action support kaltura network - continue without private data
			$allowPrivatePartnerData = false; // DO NOT allow private partner data
			return true; // action permitted (without private partner data)
		}
		
		// action not permitted, not even without private partner data access
		return false;
	}
		
		
	// can be used from derived classes to set additionl filter that don't automatically happen in applyPartnerFilters  
	protected function applyPartnerFilterForClass ( $peer, $partnerGroup = null)//, $partner_id= null )
	{
		if ( $this->getPartner() )
			$partner_id = $this->getPartner()->getId();
		else
			$partner_id = Partner::PARTNER_THAT_DOWS_NOT_EXIST;
			
		if(is_null($partnerGroup))
			$partnerGroup = $this->partnerGroup();
			
		myPartnerUtils::addPartnerToCriteria ( $peer , $partner_id , $this->private_partner_data , $partnerGroup , $this->kalturaNetworkAllowed($this->actionName)  );
	}	
	
	
	protected function applyPartnerFilterForClassNoKalturaNetwork ( $peer )
	{
		if ( $this->getPartner() )
			$partner_id = $this->getPartner()->getId();
		else
			$partner_id = -1; 
		myPartnerUtils::addPartnerToCriteria ( $peer , $partner_id , $this->private_partner_data , $this->partnerGroup() , null );
	}
/* <--------------------- Security and config settings ----------------------- */	
	
	/**
	 * @return A comma seperated string of partner ids to which current context is allowed to access
	 */
	protected function partnerGroup() 		
	{ 		
		return $this->partnerGroup;
	}
	
	/**
	 * 
	 * @return ks
	 */
	public function getKs()
	{
		return $this->ks;
	}

	public function getPartnerId()
	{
		if ($this->partner) 
			return $this->partner->getId();
			
		return null;
	}
	
	/**
	 * @return Partner
	 */
	public function getPartner()
	{
		return $this->partner; 
	}
	
	/**
	 * Returns Kuser (New kuser will be created if it doesn't exists) 
	 *
	 * @return kuser
	 */
	public function getKuser()
	{
		if (!$this->kuser)
		{
			// if no ks, puser id will be null
			if ($this->ks)
				$puserId = $this->ks->user;
			else
				$puserId = null;
				
			$kuser = kuserPeer::createKuserForPartner($this->getPartnerId(), $puserId);
			
			if ($kuser->getStatus() !== KalturaUserStatus::ACTIVE)
				throw new KalturaAPIException(KalturaErrors::INVALID_USER_ID);
			
			$this->kuser = $kuser;
		}
		
		return $this->kuser;
	}
	
	protected function getKsUniqueString()
	{
		if ($this->ks)
		{
			return $this->ks->getUniqueString();
		}
		else
		{
			return substr ( md5( rand ( 10000,99999 ) . microtime(true) ) , 1 , 7 );
			//throw new Exception ( "Cannot find unique string" );
		}

	}
	
	/**
	 * @param EntryDistribution $entryDistribution
	 * @param int $fileSubType
	 * @param string $fileName
	 * @param bool $forceProxy
	 * @throws KalturaAPIException
	 */
	protected function serveFile(EntryDistribution $entryDistribution, $fileSubType, $fileName, $forceProxy = false)
	{
		$syncKey = $entryDistribution->getSyncKey($fileSubType);
		if(!kFileSyncUtils::fileSync_exists($syncKey))
			throw new KalturaAPIException(KalturaErrors::FILE_DOESNT_EXIST);

		list($fileSync, $local) = kFileSyncUtils::getReadyFileSyncForKey($syncKey, true, false);
		
		header("Content-Disposition: attachment; filename=\"$fileName\"");
		
		if($local)
		{
			$filePath = $fileSync->getFullPath();
			$mimeType = kFile::mimeType($filePath);
			kFile::dumpFile($filePath, $mimeType);
		}
		else
		{
			$remoteUrl = kDataCenterMgr::getRedirectExternalUrl($fileSync);
			KalturaLog::info("Redirecting to [$remoteUrl]");
			if($forceProxy)
			{
				kFile::dumpUrl($remoteUrl);
			}
			else
			{
				// or redirect if no proxy
				header("Location: $remoteUrl");
			}
		}	
	}
}