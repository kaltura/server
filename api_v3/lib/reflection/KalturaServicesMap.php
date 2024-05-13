<?php 
/**
 * @package api
 * @subpackage v3
 */
class KalturaServicesMap
{
	/**
	 * @var array <KalturaServiceActionItem>
	 */
	private static $services = array();
	
	private static $extraServices = array();

	private static $serviceClassToIdAndName = array();
	
	const SERVICES_MAP_MODIFICATION_TIME = "serviceMapModificationTime";
	
	public static function addService($serviceId, $class)
	{
		$serviceId = strtolower($serviceId);
		if(class_exists($class))
		{
			self::$extraServices[$serviceId] = $class;
			if(isset($class->serviceId,$class->serviceInfo->serviceName))
				self::$serviceClassToIdAndName[$serviceId] = array($class->serviceId,$class->serviceInfo->serviceName);
		}
	}
	
	static function getMap()
	{
		if(!count(self::$services))
		{
			$cacheFilePathArray = array(kConf::get("cache_root_path"), 'api_v3', 'KalturaServicesMap.cache');
			$cacheFilePath = implode(DIRECTORY_SEPARATOR, $cacheFilePathArray);
			if (!file_exists($cacheFilePath))
			{
				$servicesPathArray = array(KALTURA_API_PATH, 'services',);
				$servicesPath = implode(DIRECTORY_SEPARATOR, $servicesPathArray);
				self::cacheMap($servicesPath, $cacheFilePath);
				if (!file_exists($cacheFilePath))
					throw new Exception('Failed to save services cached map to ['.$cacheFilePath.']');
			}
			
			self::$services = unserialize(file_get_contents($cacheFilePath));
			self::populateServiceClassToId(self::$services);
		}
		return self::$services + self::$extraServices;
	}

	static function getService($serviceId)
	{
		$services = self::getMap();
		if(isset($services[$serviceId]))
			return $services[$serviceId];
			
		return null;
	}
	
	static function getServiceIdsFromName($serviceName)
	{
		$serviceIds = array();
		$allServices = self::getMap();
		foreach ($allServices as $currentServiceId => $currentService)
		{
			$currentServiceName = end(explode('_', $currentServiceId));
			if (strtolower($currentServiceName) === strtolower($serviceName)) {
				$serviceIds[] = $currentServiceId;
			}
		}
		return $serviceIds;
	}
	
	static function filterEmptyServices($service)
	{
		return count($service->actionMap) != 0;
	}
	
	//TODO create a function for the subsequent loops
	static function cacheMap($servicePath, $cacheFilePath)
	{
		if (!is_dir($servicePath))
			throw new Exception('Invalid directory ['.$servicePath.']');
			
		$servicePath = realpath($servicePath);
		$serviceMap = array();
		$classMap = KAutoloader::getClassMap();
		$checkedClasses = array();
		
		//Retrieve all service classes from the classMap.
		$serviceClasses = array();
		foreach ($classMap as $class => $classFilePath)
		{
		    $classFilePath = realpath($classFilePath);
			if (strpos($classFilePath, $servicePath) === 0) // make sure the class is in the request service path
			{
				$reflectionClass = new ReflectionClass($class);
				
				
				if ($reflectionClass->isSubclassOf('KalturaBaseService'))
				{
				    $serviceDoccomment = new KalturaDocCommentParser($reflectionClass->getDocComment());
				    $serviceClasses[$serviceDoccomment->serviceName] = $class;
				}
			}
		}
		
		//Retrieve all plugin service classes.
		$pluginInstances = KalturaPluginManager::getPluginInstances('IKalturaServices');
		foreach($pluginInstances as $pluginName => $pluginInstance)
		{
			$pluginServices = $pluginInstance->getServicesMap();
			foreach($pluginServices as $serviceName => $serviceClass)
			{
			    $serviceName = strtolower($serviceName);
				$serviceId = "{$pluginName}_{$serviceName}";
			    $serviceClasses[$serviceId] = $serviceClass;
			}
		}
		
		//Add core & plugin services to the services map
		$aliasActions = array();
		foreach($serviceClasses as $serviceId => $serviceClass)
		{
			$serviceReflectionClass = KalturaServiceReflector::constructFromClassName($serviceClass);
			$serviceMapEntry = new KalturaServiceActionItem();
			$serviceMapEntry->serviceId = $serviceId;
			$serviceMapEntry->serviceClass = $serviceClass;
			$serviceMapEntry->serviceInfo = $serviceReflectionClass->getServiceInfo();

            $actionMap = array();
            $nativeActions = $serviceReflectionClass->getActions();
            foreach ($nativeActions as $actionId => $actionName)	
            {
                $actionMap[strtolower($actionId)] = array ("serviceClass" => $serviceClass, "actionMethodName" => $actionName, "serviceId" => $serviceId, "actionName" => $actionId);
            }	

            $serviceMapEntry->actionMap = $actionMap;
            $serviceMap[strtolower($serviceId)] = $serviceMapEntry;
            
            foreach ($serviceReflectionClass->getAliasActions() as $alias => $methodName)
            	$aliasActions[$alias] = "$serviceId.$methodName";
		}
		
		// add aliases
		foreach ($aliasActions as $aliasAction => $sourceAction)
		{
			list($aliasService, $aliasAction) = explode('.', $aliasAction);
			list($sourceService, $sourceAction) = explode('.', $sourceAction);
			$aliasService = strtolower($aliasService);
			$sourceService = strtolower($sourceService);
			
			$extServiceClass = $serviceClasses[$sourceService];
			
			if(!isset($serviceMap[$aliasService]))
				throw new Exception("Alias service [$aliasService] not found");
			
			$serviceMap[$aliasService]->actionMap[strtolower($aliasAction)] = 
				array ("serviceClass" => $extServiceClass, "actionMethodName" => $sourceAction, "serviceId" => $sourceService, "actionName" => $aliasAction);
		}
		
		// filter out services that have no actions
		$serviceMap = array_filter($serviceMap, array('KalturaServicesMap', 'filterEmptyServices'));

		if (!is_dir(dirname($cacheFilePath))) {
			mkdir(dirname($cacheFilePath));
			chmod(dirname($cacheFilePath), 0755);
		}
		kFile::safeFilePutContents($cacheFilePath, serialize($serviceMap), 0644);
	}
	
	public static function getServiceMapModificationTime ()
	{
	    $cacheFilePathArray = array(kConf::get("cache_root_path"), 'api_v3', 'KalturaServicesMap.cache');
		$cacheFilePath = implode(DIRECTORY_SEPARATOR, $cacheFilePathArray);
	    return filemtime($cacheFilePath);
	}
	
    /**
     * Function tpo retrieve a specific KalturaServiceActionItem from the cache by a service ID and action ID.
     * If the item was not found, it is retrieved from the services map and cached.
     * @param string $serviceId
     * @param string $actionId
     * @throws KalturaAPIException
     * @return KalturaServiceActionItem
     */
    public static function retrieveServiceActionItem($serviceId, $actionId)
	{
        if (kApcWrapper::functionExists('fetch'))
        {
            $apcFetchSuccess = null;
            $serviceItemFromCache = kApcWrapper::apcFetch($serviceId, $apcFetchSuccess);
            $serviceClassToIdFileCachePath = self::getServiceClassToIdCacheFilePath();
            if ($apcFetchSuccess && $serviceItemFromCache[KalturaServicesMap::SERVICES_MAP_MODIFICATION_TIME] == self::getServiceMapModificationTime()
				 && file_exists($serviceClassToIdFileCachePath))
            {
            	self::populateServiceClassToId();
            	return $serviceItemFromCache["serviceActionItem"];
            }
        }
		
		// load the service reflector
		$serviceMap = self::getMap();
		
		if(!isset($serviceMap[$serviceId]))
		{
			KalturaLog::crit("Service [$serviceId] does not exist!");
			throw new KalturaAPIException(KalturaErrors::SERVICE_DOES_NOT_EXISTS, $serviceId);
		}
		
		// check if action exists
		if(!$actionId)
		{
			KalturaLog::crit("Action not specified!");
			throw new KalturaAPIException(KalturaErrors::ACTION_NOT_SPECIFIED, $serviceId);
		}
		$reflector = $serviceMap[$serviceId];
		
		if(kApcWrapper::functionExists('store'))
		{
			$servicesMapLastModTime = self::getServiceMapModificationTime();
			$success = kApcWrapper::apcStore($serviceId, array("serviceActionItem" => $serviceMap[$serviceId], KalturaServicesMap::SERVICES_MAP_MODIFICATION_TIME => $servicesMapLastModTime));
		}
		
		return $reflector;
	}

	/**
	 * @param string $clazz the class name
	 * @return array
	 */
	public static function getServiceIdAndServiceNameByClass($clazz)
	{
		if (isset(self::$serviceClassToIdAndName[$clazz]))
			return self::$serviceClassToIdAndName[$clazz];
		return array();
	}

	/**
	 * @param $services
	 */
	private static function populateServiceClassToId($services = null)
	{
		// check if $serviceClassToIdAndName was already loaded
		if(self::$serviceClassToIdAndName)
			return;
		
		$serviceClassToIdFileCachePath = self::getServiceClassToIdCacheFilePath();
		if(file_exists($serviceClassToIdFileCachePath))
		{
			self::$serviceClassToIdAndName = unserialize(kFile::getFileContent($serviceClassToIdFileCachePath));
			return;
		}
		
		if(!$services)
			throw new Exception('Failed to load service class to id map');
		
		/** @var KalturaServiceActionItem $service */
		foreach($services as $service)
		{
			self::$serviceClassToIdAndName[$service->serviceClass] = array($service->serviceId,$service->serviceInfo->serviceName);
		}
		
		if (!is_dir(dirname($serviceClassToIdFileCachePath)))
		{
			mkdir(dirname($serviceClassToIdFileCachePath));
			chmod(dirname($serviceClassToIdFileCachePath), 0755);
		}
		kFile::safeFilePutContents($serviceClassToIdFileCachePath, serialize(self::$serviceClassToIdAndName), 0644);
	}
	
	private static function getServiceClassToIdCacheFilePath()
	{
		return implode(DIRECTORY_SEPARATOR, array(kConf::get("cache_root_path"), 'api_v3', 'KalturaServiceClassToId.cache'));;
	}
}
