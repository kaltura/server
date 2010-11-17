<?php 
class KalturaServicesMap
{
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
			$cacheFilePathArray = array(KALTURA_API_PATH, 'cache', 'KalturaServicesMap.cache');
			$cacheFilePath = implode(DIRECTORY_SEPARATOR, $cacheFilePathArray);
			if (!file_exists($cacheFilePath))
			{
				$servicesPathArray = array(KALTURA_API_PATH, 'services',);
				$servicesPath = implode(DIRECTORY_SEPARATOR, $servicesPathArray);
				self::cacheMap($servicesPath, $cacheFilePath);
				if (!file_exists($cacheFilePath))
					throw new Exception('Failed to save services cached map to ['.$cacheFilePath.']');
			}
			
			require_once($cacheFilePath);
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
	
	static function cacheMap($servicePath, $cacheFilePath)
	{
		if (!is_dir($servicePath))
			throw new Exception('Invalid directory ['.$servicePath.']');
			
		$servicePath = realpath($servicePath);
		$serviceMap = array();
		$classMap = KAutoloader::getClassMap();
		$checkedClasses = array();
		foreach($classMap as $class => $classFilePath)
		{
			$classFilePath = realpath($classFilePath);
			if (strpos($classFilePath, $servicePath) === 0) // make sure the class is in the request service path
			{
				$reflectionClass = new ReflectionClass($class);
				if ($reflectionClass->isSubclassOf('KalturaBaseService'))
				{
					$docComment = $reflectionClass->getDocComment();
					$parser = new KalturaDocCommentParser($docComment);
					$serviceId = strtolower($parser->serviceName);
					$serviceMap[$serviceId] = $class;
				}
			}
			$checkedClasses[] = $class;
		}
		
		$pluginServices = array();
		$pluginInstances = KalturaPluginManager::getPluginInstances('IKalturaServices');
		foreach($pluginInstances as $pluginName => $pluginInstance)
		{
			$pluginServices = $pluginInstance->getServicesMap();
			foreach($pluginServices as $serviceName => $serviceClass)
			{
				$serviceName = strtolower($serviceName);
				$serviceId = "{$pluginName}_{$serviceName}";
				$pluginServices[$serviceId] = $serviceClass;
				$serviceMap[$serviceId] = $serviceClass;
			}
		}
		
		$cachedFile = '';
		$cachedFile .= ('<?php' . PHP_EOL);
		$cachedFile .= ('self::$services = ' . var_export($serviceMap, true) . ';' . PHP_EOL);
		file_put_contents($cacheFilePath, $cachedFile);
	}
}