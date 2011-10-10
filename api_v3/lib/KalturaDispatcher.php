<?php
/**
 * @package api
 */
class KalturaDispatcher 
{
	private static $instance = null;
	
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
	
	public function dispatch($service, $action, $params = array()) 
	{
		$start = microtime( true );
	
		// prevent impersonate to partner zero
		$p = isset($params["p"]) && $params["p"] ? $params["p"] : null;
		if (!$p) 
			$p = isset($params["partnerId"]) && $params["partnerId"] ? $params["partnerId"] : null;
			
		$GLOBALS["partnerId"] = $p; // set for logger
		
		$userId = "";
		$ksStr = isset($params["ks"]) ? $params["ks"] : null ;
		 
		if (!$service)
			throw new KalturaAPIException(KalturaErrors::SERVICE_NOT_SPECIFIED);
		
        try 
        {
            // load the service reflector
		    $reflector = new KalturaServiceReflector($service);
        }
        catch(Exception $ex)
        {
			throw new KalturaAPIException(KalturaErrors::SERVICE_DOES_NOT_EXISTS, $service);
        }
		
		// check if action exists
		if (!$action)
			throw new KalturaAPIException(KalturaErrors::ACTION_NOT_SPECIFIED, $service);
		
		if (!$reflector->isActionExists($action))
			throw new KalturaAPIException(KalturaErrors::ACTION_DOES_NOT_EXISTS, $action, $service);
		
		$actionParams = $reflector->getActionParams($action);

		// services.ct - check if partner is allowed to access service ...

		// validate it's ok to access this service
		$deserializer = new KalturaRequestDeserializer($params);
		$arguments = $deserializer->buildActionArguments($actionParams);
		KalturaLog::debug("Dispatching service [".$service."], action [".$action."] with params " . print_r($arguments, true));

		$serviceInstance = $reflector->getServiceInstance();
		
		kCurrentContext::$host = (isset($_SERVER["HOSTNAME"]) ? $_SERVER["HOSTNAME"] : null);
		kCurrentContext::$user_ip = requestUtils::getRemoteAddress();
		kCurrentContext::$ps_vesion = "ps3";
		kCurrentContext::$service = $reflector->getServiceName();
		kCurrentContext::$action =  $action;
		kCurrentContext::$client_lang =  isset($params['clientTag']) ? $params['clientTag'] : null;
		
		kCurrentContext::initKsPartnerUser($ksStr, $p, $userId);
		kPermissionManager::init(kConf::get('enable_cache'));
		
		$actionInfo = $reflector->getActionInfo($action);
		if($actionInfo->validateUserObjectClass && $actionInfo->validateUserIdParamName && isset($actionParams[$actionInfo->validateUserIdParamName]))
		{
//			// TODO maybe if missing should throw something, maybe a bone?
//			if(!isset($actionParams[$actionInfo->validateUserIdParamName]))
//				throw new KalturaAPIException(KalturaErrors::MISSING_MANDATORY_PARAMETER, $actionInfo->validateUserIdParamName);
			KalturaLog::debug("validateUserIdParamName: ".$actionInfo->validateUserIdParamName);
			$objectId = $params[$actionInfo->validateUserIdParamName];
			$this->validateUser($actionInfo->validateUserObjectClass, $objectId, $actionInfo->validateUserPrivilege);
		}
		
		// initialize the service before invoking the action on it
		$serviceInstance->initService($reflector->getServiceId(), $reflector->getServiceName(), $action);
		
		$invokeStart = microtime(true);
		KalturaLog::debug("Invoke start");
		
		$res =  $reflector->invoke($action, $arguments);
		
		KalturaLog::debug("Invoke took - " . (microtime(true) - $invokeStart) . " seconds");
		KalturaLog::debug("Disptach took - " . (microtime(true) - $start) . " seconds");		
				
		kMemoryManager::clearMemory();
		
		return $res;
	}

	/**
	 * @param string $objectClass
	 * @param string $objectId
	 * @param string $privilege optional
	 * @throws KalturaAPIException
	 */
	private function validateUser($objectClass, $objectId, $privilege = null)
	{
		// don't allow operations without ks
		if (!kCurrentContext::$ks_object)
			throw new KalturaAPIException(KalturaErrors::INVALID_KS, "", ks::INVALID_TYPE, ks::getErrorStr(ks::INVALID_TYPE));

		// if admin always allowed
		if (kCurrentContext::$is_admin_session)
			return;

		KalturaLog::debug("user validation running");
		KalturaLog::debug($objectClass);
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
			return;
	
		if($privilege)
		{
			// check if all ids are privileged
			if (kCurrentContext::$ks_object->verifyPrivileges($privilege, ks::PRIVILEGE_WILDCARD))
				return;
				
			// check if object id is privileged
			if (kCurrentContext::$ks_object->verifyPrivileges($privilege, $dbObject->getId()))
			{
				return;
			}
			
		}
				
		if ($dbObject->getPuserId() != kCurrentContext::$uid)
			throw new KalturaAPIException(KalturaErrors::INVALID_KS, "", ks::INVALID_TYPE, ks::getErrorStr(ks::INVALID_TYPE));
	}
}

