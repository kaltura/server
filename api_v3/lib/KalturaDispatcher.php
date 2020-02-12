<?php
/**
 * @package api
 * @subpackage v3
 */
class KalturaDispatcher 
{
	private static $instance = null;
	private $arguments = null;

	const OWNER_ONLY_OPTION = "ownerOnly";
	/**
	 * Return a KalturaDispatcher instance
	 *
	 * @return KalturaDispatcher
	 */
	public static function getInstance()
	{
		if (!self::$instance)
		{
			self::$instance = new self();
		}
		    
		return self::$instance;
	}
	
	public function getArguments()
	{
		return $this->arguments;
	}
	
	public function dispatch($service, $action, $params = array()) 
	{
		$start = microtime( true );
		
		$userId = "";
		$ksStr = isset($params["ks"]) ? $params["ks"] : null ;
		
		//If trying to impersonate to partner zero validate that a ksString is provided,
		//In case an invalid partner tries to do the impersonation it will be prevented when building the current context.
		$p = isset($params["p"]) && ($params["p"] || $ksStr) ? $params["p"] : null;
		if (!$p)
			$p = isset($params["partnerId"]) && ($params["partnerId"] || $ksStr) ? $params["partnerId"] : null;
		
		$GLOBALS["partnerId"] = $p; // set for logger
		 
		if (!$service)
			throw new KalturaAPIException(KalturaErrors::SERVICE_NOT_SPECIFIED);
		
        //strtolower on service - map is indexed according to lower-case service IDs
        $service = strtolower($service);
        
        $serviceActionItem = KalturaServicesMap::retrieveServiceActionItem($service, $action);
        $action = strtolower($action);
        if (!isset($serviceActionItem->actionMap[$action]))
        {
            KalturaLog::crit("Action does not exist!");
		    throw new KalturaAPIException(KalturaErrors::ACTION_DOES_NOT_EXISTS, $action, $service);
        }
        
        try
        {
	        $actionReflector = new KalturaActionReflector($service, $action, $serviceActionItem->actionMap[$action]);
        }
        catch (Exception $e)
        {
             throw new Exception("Could not create action reflector for service [$service], action [$action]. Received error: ". $e->getMessage());
        }
        
		$actionParams = $actionReflector->getActionParams();
		$actionInfo = $actionReflector->getActionInfo();
		// services.ct - check if partner is allowed to access service ...
		kCurrentContext::$host = (isset($_SERVER["HOSTNAME"]) ? $_SERVER["HOSTNAME"] : gethostname());
		kCurrentContext::$user_ip = requestUtils::getRemoteAddress();
		kCurrentContext::$ps_vesion = "ps3";
		kCurrentContext::$service = $serviceActionItem->serviceInfo->serviceName;
		kCurrentContext::$action =  $action;
		kCurrentContext::$client_lang =  isset($params['clientTag']) ? $params['clientTag'] : null;
		kCurrentContext::initKsPartnerUser($ksStr, $p, $userId);

		if (!$this->rateLimit($service, $action, $params))
		{
			throw new KalturaAPIException(KalturaErrors::ACTION_BLOCKED, $action, $service);
		}
		
		// validate it's ok to access this service
		$deserializer = new KalturaRequestDeserializer($params);
		$this->arguments = $deserializer->buildActionArguments($actionParams);
		KalturaLog::debug("Dispatching service [".$service."], action [".$action."], reqIndex [".
			kCurrentContext::$multiRequest_index."] with params " . print_r($this->arguments, true));

		$responseProfile = $deserializer->getResponseProfile();
		if($responseProfile)
		{
			KalturaLog::debug("Response profile: " . print_r($responseProfile, true));
		}
		
		PermissionPeer::preFetchPermissions(array(PermissionName::FEATURE_END_USER_REPORTS, PermissionName::FEATURE_ENTITLEMENT));

		kPermissionManager::init(kConf::get('enable_cache'));
		kEntitlementUtils::initEntitlementEnforcement();
		
	    $disableTags = $actionInfo->disableTags;
		if($disableTags && is_array($disableTags) && count($disableTags))
		{
			foreach ($disableTags as $disableTag)
			{
			    KalturaCriterion::disableTag($disableTag);
			}
		}
		
		if($actionInfo->validateUserObjectClass && $actionInfo->validateUserIdParamName && isset($actionParams[$actionInfo->validateUserIdParamName]))
		{
//			// TODO maybe if missing should throw something, maybe a bone?
//			if(!isset($actionParams[$actionInfo->validateUserIdParamName]))
//				throw new KalturaAPIException(KalturaErrors::MISSING_MANDATORY_PARAMETER, $actionInfo->validateUserIdParamName);
			KalturaLog::debug("validateUserIdParamName: ".$actionInfo->validateUserIdParamName);
			$objectId = $params[$actionInfo->validateUserIdParamName];
			$this->validateUser($actionInfo->validateUserObjectClass, $objectId, $actionInfo->validateUserPrivilege, $actionInfo->validateOptions);
		}
		
		// initialize the service before invoking the action on it
		// action reflector will init the service to maintain the pluginable action transparency
		$actionReflector->initService($responseProfile);
		
		$invokeStart = microtime(true);
		KalturaLog::debug("Invoke start");
		try
		{
			$res =  $actionReflector->invoke( $this->arguments );
		}
		catch (KalturaAPIException $e)
		{
			if ($actionInfo->returnType != 'file')
			{
				 throw ($e);
			}
					
			KalturaResponseCacher::adjustApiCacheForException($e);
			$res = new kRendererDieError ($e->getCode(), $e->getMessage());
		 }
		
		kEventsManager::flushEvents();
		
		KalturaLog::debug("Invoke took - " . (microtime(true) - $invokeStart) . " seconds");
		KalturaLog::debug("Dispatch took - " . (microtime(true) - $start) . " seconds, memory: ".memory_get_peak_usage(true));		
				
		
		return $res;
	}
	

	/**
	 * @param string $objectClass
	 * @param string $objectId
	 * @param string $privilege optional
	 * @param string $options optional
	 * @throws KalturaErrors::INVALID_KS
	 */
	protected function validateUser($objectClass, $objectId, $privilege = null, $options = null)
	{
		// don't allow operations without ks
		if (!kCurrentContext::$ks_object)
			throw new KalturaAPIException(KalturaErrors::INVALID_KS, "", ks::INVALID_TYPE, ks::getErrorStr(ks::INVALID_TYPE));

		// if admin always allowed
		if (kCurrentContext::$is_admin_session)
			return;

		
		$objectGetters = null;
		if(strstr($objectClass, '::'))
		{
			$objectGetters = explode('::', $objectClass);
			$objectClass = array_shift($objectGetters);
		}
			
		$objectClassPeer = "{$objectClass}Peer";
		if(!class_exists($objectClassPeer))
			return;
			
		$dbObject = $objectClassPeer::retrieveByPK($objectId);
		
		if($objectGetters)
		{
			foreach($objectGetters as $objectGetter)
			{
				$getterMethod = "get{$objectGetter}";
				
				$reflector = new ReflectionObject($dbObject); 
				
				if (! $reflector->hasMethod($getterMethod))
				{
					KalturaLog::err("Method ".$getterMethod. " does not exist for class ".$reflector->getName());
					return;
				}
				
				$dbObject = $dbObject->$getterMethod();
			}
		}
		
		if(!($dbObject instanceof IOwnable))
		{
			return;
		}
	
		if($privilege)
		{
			// check if all ids are privileged
			if (kCurrentContext::$ks_object->verifyPrivileges($privilege, ks::PRIVILEGE_WILDCARD))
			{
				return;
			}
				
			// check if object id is privileged
			if (kCurrentContext::$ks_object->verifyPrivileges($privilege, $dbObject->getId()))
			{
				return;
			}
			
		}
		
		if ((strtolower($dbObject->getPuserId()) != strtolower(kCurrentContext::$ks_uid)) && (kCurrentContext::$ks_partner_id != Partner::MEDIA_SERVER_PARTNER_ID))
		{
			$optionsArray = array();
			if ( $options ) {
				$optionsArray = explode(",", $options);
			}
			if (!$dbObject->isEntitledKuserEdit(kCurrentContext::getCurrentKsKuserId())
				||
				(in_array(self::OWNER_ONLY_OPTION,$optionsArray)) && !$dbObject->isOwnerActionsAllowed(kCurrentContext::getCurrentKsKuserId()))
					throw new KalturaAPIException(KalturaErrors::INVALID_KS, "", ks::INVALID_TYPE, ks::getErrorStr(ks::INVALID_TYPE));
		}
	}
	
	protected function getApiParamValue($params, $key)
	{
		if (isset($params[$key]))
		{
			return $params[$key];
		}
		
		$explodedKey = explode(':', $key);
		foreach ($explodedKey as $curKey)
		{
			if (!is_array($params) || !isset($params[$curKey]))
			{
				return null;
			}
			
			$params = $params[$curKey];
		}
		
		return $params;
	}
	
	protected function getApiParamValueWildcard($params, $key)
	{
		$result = '';
		foreach ($params as $curKey => $value)
		{
			if (is_array($value))
			{
				// recurse into the nested param
				$result .= $this->getApiParamValueWildcard($value, $key);
				continue;
			}
			
			if ($curKey == $key || 
				substr($curKey, -strlen($key) - 1) == ':' . $key)
			{
				$result .= $value;
			}
		}
		return $result;
	}
	
	protected function getRateLimitRule($params)
	{
		foreach (kConf::getMap('api_rate_limit') as $rateLimitRule)
		{
			$matches = true;
			foreach ($rateLimitRule as $key => $value)
			{
				if ($key[0] == '_')
				{
					if ($key == '_partnerId')
					{
						$partnerId = kCurrentContext::$ks_partner_id ? kCurrentContext::$ks_partner_id : kCurrentContext::$partner_id;
						if (!in_array($partnerId, explode(",", $value)))
						{
							$matches = false;
							break;
						}
					}
					
					continue;
				}
					
				if ($this->getApiParamValue($params, $key) != $value)
				{
					$matches = false;
					break;
				}
			}
		
			if ($matches)
			{
				return $rateLimitRule;
			}
		}
		
		return null;
	}
	
	protected function rateLimit($service, $action, $params)
	{
		if (!kConf::hasMap('api_rate_limit') ||
			kCurrentContext::$ks_partner_id == Partner::BATCH_PARTNER_ID)
		{
			return true;
		}
		
		$rule = $this->getRateLimitRule($params);
		if (!$rule)
		{
			return true;
		}
		
		if (isset($rule['_key']))
		{
			$keyOptions = explode(',', $rule['_key']);
			$key = null;
			foreach ($keyOptions as $keyOption)
			{
				$value = $this->getApiParamValueWildcard($params, $keyOption);
				if ($value)
				{
					$key = $value;
					break;
				}
			}
			
			if (!$key)
			{
				return true;
			}
		}
		else
		{
			$key = '';
		}
		
		$cache = kCacheManager::getSingleLayerCache(kCacheManager::CACHE_TYPE_LOCK_KEYS);
		if (!$cache)
		{
			return true;
		}
		
		$partnerId = kCurrentContext::$ks_partner_id;
		$keySeed = "$service-$action-$partnerId-$key";
		$key = 'apiRateLimit-'.md5($keySeed);
		
		$cacheExpiry = isset($rule['_expiry']) ? $rule['_expiry'] : 10;
		$cache->add($key, 0, $cacheExpiry);
		$counter = $cache->increment($key);
		if ($counter <= $rule['_limit'])
		{
			return true;
		}
		
		KalturaLog::log("Rate limit exceeded - key=$key keySeed=$keySeed counter=$counter");
		
		if (isset($rule['_logOnly']) && $rule['_logOnly'])
		{
			return true;
		}
		
		return false;
	}
}
