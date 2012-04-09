<?php 
class KalturaServicesMap
{
	/**
	 * @var array <KalturaServiceActionItem>
	 */
	private static $services = array();
	
	private static $extraServices = array();
	
	public static function addService($serviceId, $class)
	{
		$serviceId = strtolower($serviceId);
		if(class_exists($class))
			self::$extraServices[$serviceId] = $class;
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
		foreach($serviceClasses as $serviceId => $serviceClass)
		{
			$serviceReflectionClass = KalturaServiceReflector::constructFromClassName($serviceClass);
			$serviceMapEntry = new KalturaServiceActionItem();
			$serviceMapEntry->serviceId = $serviceId;
			$serviceMapEntry->serviceInfo = $serviceReflectionClass->getServiceInfo();
            $actionMap = array();
            $nativeActions = $serviceReflectionClass->getActions();
            foreach ($nativeActions as $actionId => $actionName)	
            {
                $actionMap[strtolower($actionId)] = array ("serviceClass" => $serviceClass, "actionMethodName" => $actionName, "serviceId" => $serviceId, "actionName" => $actionId);
            }	
            
            //Loop over all service classes and find the alias actions for the $serviceClass
            foreach ($serviceClasses as $extServiceId => $extServiceClass)
            {
                $aliasServiceClass = KalturaServiceReflector::constructFromClassName($extServiceClass);
                $aliasActions = $aliasServiceClass->getAliasActions($serviceId);
                foreach ($aliasActions as $aliasAction => $actionName)
                {
                    if (isset($actionMap[$aliasAction]))
                    {
                        throw new Exception("Cannot use the same action alias from 2 service classes! Action alias [$aliasAction], classes [".$actionMap[$aliasAction]."], [$extServiceClass]");
                    }
                    $actionMap[strtolower($aliasAction)] = array ("serviceClass" => $extServiceClass, "actionMethodName" => $actionName, "serviceId" => $extServiceId, "actionName" => $aliasAction);
                }
                
            }
            
            if (count($actionMap))
            {
                $serviceMapEntry->actionMap = $actionMap;
                $serviceMap[strtolower($serviceId)] = $serviceMapEntry;
            }
		}
		
//		$cachedFile = '';
//		$cachedFile .= ('<?php' . PHP_EOL);
//		$cachedFile .= ('self::$services = ' . var_export($serviceMap, true) . ';' . PHP_EOL);
		if (!is_dir(dirname($cacheFilePath))) {
			mkdir(dirname($cacheFilePath), 0777);
		}
		file_put_contents($cacheFilePath, serialize($serviceMap));
	}
}