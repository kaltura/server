<?php

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
	
	public function clearMemory() 
	{
		accessControlPeer::clearInstancePool();
		BatchJobPeer::clearInstancePool();
		BulkUploadResultPeer::clearInstancePool();
		categoryPeer::clearInstancePool();
		EmailIngestionProfilePeer::clearInstancePool();
		entryPeer::clearInstancePool();
		FileSyncPeer::clearInstancePool();
		flavorAssetPeer::clearInstancePool();
		flavorParamsConversionProfilePeer::clearInstancePool();
		flavorParamsOutputPeer::clearInstancePool();
		flavorParamsPeer::clearInstancePool();
		kshowPeer::clearInstancePool();
		mediaInfoPeer::clearInstancePool();
		moderationFlagPeer::clearInstancePool();
		moderationPeer::clearInstancePool();
		notificationPeer::clearInstancePool();
		roughcutEntryPeer::clearInstancePool();
		SchedulerConfigPeer::clearInstancePool();
		SchedulerPeer::clearInstancePool();
		SchedulerStatusPeer::clearInstancePool();
		SchedulerWorkerPeer::clearInstancePool();
		StorageProfilePeer::clearInstancePool();
		syndicationFeedPeer::clearInstancePool();
		TrackEntryPeer::clearInstancePool();
		uiConfPeer::clearInstancePool();
		UploadTokenPeer::clearInstancePool();
		
		// TODO clear default filters
		// TODO call all memory cleaner plugins
		
		if(function_exists('gc_collect_cycles')) // php 5.3 and above
			gc_collect_cycles();
	}
	
	public function dispatch($service, $action, $params = array()) 
	{
		KalturaLog::debug("Dispatching service [".$service."], action [".$action."] with params " . print_r($params, true));
		
		$start = microtime( true );
	
		$p = isset ($params["p"]) ? $params["p"] : null;
		if (!$p) 
			$p = isset($params["partnerId"]) ? $params["partnerId"] : null;
			
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

		$serviceInstance = $reflector->getServiceInstance();
		
		kCurrentContext::$host = (isset($_SERVER["HOSTNAME"]) ? $_SERVER["HOSTNAME"] : null);
		kCurrentContext::$user_ip = requestUtils::getRemoteAddress();
		kCurrentContext::$ps_vesion = "ps3";
		kCurrentContext::$service = $reflector->getServiceName();
		kCurrentContext::$action =  $action;
		kCurrentContext::$client_lang =  isset($params['clientTag']) ? $params['clientTag'] : null;
		
		kCurrentContext::initKsPartnerUser($ksStr, $p, $userId);
		kPermissionManager::init(kConf::get('enable_cache'));
				
		// initialize the service before invoking the action on it
		$serviceInstance->initService ($reflector->getServiceName(), $action);
		
		$invokeStart = microtime(true);
		KalturaLog::debug("Invoke start");
		
		$res =  $reflector->invoke($action, $arguments);
		
		KalturaLog::debug("Invoke took - " . (microtime(true) - $invokeStart) . " seconds");
		KalturaLog::debug("Disptach took - " . (microtime(true) - $start) . " seconds");
				
		$this->clearMemory();
		
		return $res;
	}
}

