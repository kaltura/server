<?php
/**
 * Will hold the current context of the API call / current running batch.
 * The information is static per call and can be used from anywhere in the code. 
 */
class kCurrentContext
{
	/**
	 * @var string
	 */
	public static $ks;
	
	/**
	 * @var ks
	 */
	public static $ks_object;
	
	/**
	 * @var string
	 */
	public static $ks_hash;
	
	/**
	 * This value is populated only in case of impersonation using the partnerId parameter in the request.
	 * It's used by the batch and the admin console only.
	 * 
	 * @var int
	 */
	public static $partner_id;

	/**
	 * @var int
	 */
	public static $ks_partner_id;

	/**
	 * @var int
	 */
	public static $master_partner_id;
	
	/**
	 * @var string
	 */
	public static $uid;
	
	
	/**
	 * @var string
	 */
	public static $ks_uid;
	
	/**
	 * @var int
	 */
	public static $ks_kuser_id = null;
	
	/**
	 * @var kuser
	 */
	public static $ks_kuser;

	/**
	 * @var string
	 */
	public static $ps_vesion;
	
	/**
	 * @var string
	 */
	public static $call_id;
	
	/**
	 * @var string
	 */
	public static $service;
	
	/**
	 * @var string
	 */
	public static $action;
	
	/**
	 * @var string
	 */
	public static $host;
	
	/**
	 * @var string
	 */
	public static $client_version;
	
	/**
	 * @var string
	 */
	public static $client_lang;
	
	/**
	 * @var string
	 */
	public static $user_ip;
	
	/**
	 * @var bool
	 */
	public static $is_admin_session;
	
	/**
	 * @var bool
	 */
	public static $ksPartnerUserInitialized = false;
	
	/**
	 * @var int
	 */
	public static $multiRequest_index = 1;
	
	/**
	 * @var callable
	 */	
	public static $serializeCallback;

	/**
	 * @var int
	 */
	public static $HTMLPurifierBehaviour = null;

	/**
	 * @var bool
	 */
	public static $HTMLPurifierBaseListOnlyUsage = null;

	public static $isInMultiRequest = false;
	
	/**
	 * @var executionScope
	 */
	public static $executionScope = null;
	
	/**
	 * @var int
	 */
	public static $virtual_event_id = null;

	public static function getEntryPoint()
	{
		if(self::$service && self::$action)
			return self::$service . '::' . self::$action;
			
		if(isset($_SERVER['SCRIPT_NAME']))
			return $_SERVER['SCRIPT_NAME'];
			
		if(isset($_SERVER['PHP_SELF']))
			return $_SERVER['PHP_SELF'];
			
		if(isset($_SERVER['SCRIPT_FILENAME']))
			return $_SERVER['SCRIPT_FILENAME'];
			
		return '';
	}
	
	public static function isApiV3Context()
	{		
		if (kCurrentContext::$ps_vesion == 'ps3') {
			return true;
		}
		
		return false;
	}
	
	public static function initPartnerByEntryId($entryId)
	{		
		$entry = entryPeer::retrieveByPKNoFilter($entryId);
		if(!$entry)
			return null;
			
		kCurrentContext::$ks = null;
		kCurrentContext::$ks_object = null;
		kCurrentContext::$ks_hash = null;
		kCurrentContext::$ks_partner_id = $entry->getPartnerId();
		kCurrentContext::$ks_uid = null;
		kCurrentContext::$master_partner_id = null;
		kCurrentContext::$partner_id = $entry->getPartnerId();
		kCurrentContext::$uid = null;
		kCurrentContext::$is_admin_session = false;
		
		return $entry;
	}
	
	public static function initPartnerByAssetId($assetId)
	{		
		KalturaCriterion::disableTags(array(KalturaCriterion::TAG_ENTITLEMENT_ENTRY, KalturaCriterion::TAG_WIDGET_SESSION));
		$asset = assetPeer::retrieveByIdNoFilter($assetId);
		KalturaCriterion::restoreTags(array(KalturaCriterion::TAG_ENTITLEMENT_ENTRY, KalturaCriterion::TAG_WIDGET_SESSION));
		
		if(!$asset)
			return null;
			
		kCurrentContext::$ks = null;
		kCurrentContext::$ks_object = null;
		kCurrentContext::$ks_hash = null;
		kCurrentContext::$ks_partner_id = $asset->getPartnerId();
		kCurrentContext::$ks_uid = null;
		kCurrentContext::$master_partner_id = null;
		kCurrentContext::$partner_id = $asset->getPartnerId();
		kCurrentContext::$uid = null;
		kCurrentContext::$is_admin_session = false;
		
		return $asset;
	}
	
	public static function initKsPartnerUser($ksString, $requestedPartnerId = null, $requestedPuserId = null)
	{		
		if (!$ksString)
		{
			kCurrentContext::$ks = null;
			kCurrentContext::$ks_object = null;
			kCurrentContext::$ks_hash = null;
			kCurrentContext::$ks_partner_id = null;
			kCurrentContext::$ks_uid = null;
			kCurrentContext::$master_partner_id = null;
			kCurrentContext::$partner_id = $requestedPartnerId;
			kCurrentContext::$uid = $requestedPuserId;
			kCurrentContext::$is_admin_session = false;
		}
		else
		{
			try { $ksObj = kSessionUtils::crackKs ( $ksString ); }
			catch(Exception $ex)
			{
				if (strpos($ex->getMessage(), "INVALID_STR") !== null)
					throw new kCoreException($ex->getMessage(), kCoreException::INVALID_KS, $ksString);
				else 
					throw $ex;
			}
		
			kCurrentContext::$ks = $ksString;
			kCurrentContext::$ks_object = $ksObj;
			kCurrentContext::$ks_hash = $ksObj->getHash();
			kCurrentContext::$ks_partner_id = $ksObj->partner_id;
			kCurrentContext::$ks_uid = $ksObj->user;
			kCurrentContext::$master_partner_id = $ksObj->master_partner_id ? $ksObj->master_partner_id : kCurrentContext::$ks_partner_id;
			kCurrentContext::$is_admin_session = $ksObj->isAdmin();
			kCurrentContext::$virtual_event_id = $ksObj->getPrivilegeValue(ks::PRIVILEGE_VIRTUAL_EVENT_ID);
			
			if($requestedPartnerId == PartnerPeer::GLOBAL_PARTNER && self::$ks_partner_id > PartnerPeer::GLOBAL_PARTNER)
				$requestedPartnerId = null;
			
			kCurrentContext::$partner_id = $requestedPartnerId;
			kCurrentContext::$uid = $requestedPuserId;
		}

		// set partner ID for logger
		if (kCurrentContext::$partner_id) {
			$GLOBALS["partnerId"] = kCurrentContext::$partner_id;
		}
		else if (kCurrentContext::$ks_partner_id) {
			$GLOBALS["partnerId"] = kCurrentContext::$ks_partner_id;
		}
		
		self::$ksPartnerUserInitialized = true;
	}
	
	public static function getCurrentKsKuser($activeOnly = true)
	{
		if(!kCurrentContext::$ks_kuser || kCurrentContext::$ks_kuser->getPuserId() != kCurrentContext::$ks_uid || kCurrentContext::$ks_kuser->getPartnerId() != kCurrentContext::$ks_partner_id)
		{
			kCurrentContext::$ks_kuser = kuserPeer::getKuserByPartnerAndUid(kCurrentContext::$ks_partner_id, kCurrentContext::$ks_uid, true);
		}
		
		if(kCurrentContext::$ks_kuser &&
		   $activeOnly && 
		   kCurrentContext::$ks_kuser->getStatus() != KuserStatus::ACTIVE)
		   	return null;
			
		return kCurrentContext::$ks_kuser;
	}

	public static function getCurrentSessionType()
	{
		if(!self::$ks_object)
			return kSessionBase::SESSION_TYPE_NONE;
			
		if(self::$ks_object->isAdmin())
			return kSessionBase::SESSION_TYPE_ADMIN;
			
		if(self::$ks_object->isWidgetSession())
			return kSessionBase::SESSION_TYPE_WIDGET;
			
		return kSessionBase::SESSION_TYPE_USER;
	}

	public static function getCurrentPartnerId()
	{
		if(isset(self::$partner_id))
			return self::$partner_id;
			
		return self::$ks_partner_id;
	}

	public static function getCurrentKsKuserId()
	{
		$ksKuser = kCurrentContext::getCurrentKsKuser(false);
		if($ksKuser)
			kCurrentContext::$ks_kuser_id = $ksKuser->getId();
		else 
			kCurrentContext::$ks_kuser_id = 0;
			
		return kCurrentContext::$ks_kuser_id;
	}
}
