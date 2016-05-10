<?php

abstract class ClientGeneratorFromPhp
{
	protected $_files = array();
	protected $_services = array();
	protected $_types = array();
	protected $_sourcePath = "";
	protected $_classMap = null;
	protected $_typesClassMap = null;
	
	protected $package = 'Kaltura';
	protected $subpackage = 'Client';
	
	public function setPackage($package)
	{
		$this->package = $package;
	}

	public function setSubpackage($subpackage)
	{
		$this->subpackage = $subpackage;
	}

	/**
	 * @return the $_services
	 */
	public function getServices() {
		return $this->_services;
	}

	/**
	 * @return the $_types
	 */
	public function getTypes() {
		return $this->_types;
	}

	public function __construct($sourcePath = null)
	{
		$this->_config = kConf::getMap('generator');
		$this->_sourcePath = realpath($sourcePath);
		
		if (($sourcePath !== null) && !(is_dir($sourcePath)))
			throw new Exception("Source path was not found [$sourcePath]");
		
		if (is_dir($sourcePath))
			$this->addSourceFiles($this->_sourcePath);

		$typesClassMapPath = $this->getTypesClassMapPath();
		if(file_exists($typesClassMapPath))
			$this->_typesClassMap = unserialize(file_get_contents($typesClassMapPath));
	}
	
	public function getOutputFiles()
	{
		return $this->_files;
	}
	
	protected function addFile($fileName, $fileContents)
	{
		 $this->_files[$fileName] = $fileContents;
	}
	
	protected function addSourceFiles($directory)
	{
		// add if file
		if (is_file($directory))
		{
			$file = str_replace($this->_sourcePath.DIRECTORY_SEPARATOR, "", $directory);
			$this->addFile($file, file_get_contents($directory));
			return;
		}
		
		// loop through the folder
		$dir = dir($directory);
		while (false !== $entry = $dir->read())
		{
			// skip pointers & hidden files
			if ($this->beginsWith($entry, "."))
			{
				continue;
			}
			 
			$this->addSourceFiles(realpath("$directory/$entry"));
		}
		 
		// clean up
		$dir->close();
	}
	
	/**
	 * Main generation method, can be overload to support a different flow
	 *
	 */
	public function generate()
	{
		$this->load();
		
		$this->writeHeader();

		$this->writeBeforeTypes();
		// types
		foreach($this->_types as $typeReflector)
		{
			$this->writeType($typeReflector);
		}
		$this->writeAfterTypes();
		
		$this->writeBeforeServices();
		// services
		foreach($this->_services as $serviceId => $serviceActionItem)
		{
			/* @var $serviceActionItem KalturaServiceActionItem */
			$this->writeBeforeService($serviceActionItem);
			$serviceName = $serviceActionItem->serviceInfo->serviceName;
			$serviceId = $serviceActionItem->serviceId;
			$actions = $serviceActionItem->actionMap;
			foreach($actions as $action => $actionReflector)
			{
				$actionInfo = $actionReflector->getActionInfo();
				
				if($actionInfo->serverOnly)
					continue;
					
				if (strpos($actionInfo->clientgenerator, "ignore") !== false)
					continue;
					
				$outputTypeReflector = $actionReflector->getActionOutputType();
				$actionParams = $actionReflector->getActionParams();
				$this->writeServiceAction($serviceId, $serviceName, $action, $actionParams, $outputTypeReflector);
			}
			$this->writeAfterService($serviceActionItem);
		}
		$this->writeAfterServices();
		
		$this->writeFooter();
	}
	
	protected function initClassMap()
	{
		if ($this->_classMap !== null)
			return;
		
		$this->_classMap = KAutoloader::getClassMap();
	}
	
	public function load()
	{
		$this->loadServicesInfo();
		
		
		$this->addType(KalturaTypeReflectorCacher::get('KalturaClientConfiguration'));
		$this->addType(KalturaTypeReflectorCacher::get('KalturaRequestConfiguration'));
		
		// load the filter order by string enums
		foreach($this->_types as $typeReflector)
		{
			$include = null;
			if(isset($typeReflector->include))
				$include = $typeReflector->include;
			
			if (strpos($typeReflector->getType(), "Filter", strlen($typeReflector->getType()) - 6))
			{
				$filterOrderByStringEnumTypeName = str_replace("Filter", "OrderBy", $typeReflector->getType());
				if (class_exists($filterOrderByStringEnumTypeName))
					$this->addType(KalturaTypeReflectorCacher::get($filterOrderByStringEnumTypeName), $include);
			}
		}

		foreach($this->_config as $sectionName => $section)
		{
			if(isset($section['additional']))
			{
				$additionals = explode(',', str_replace(' ', '', $section['additional']));
				foreach($additionals as $additional)
				{
					$this->addType(KalturaTypeReflectorCacher::get($additional), array($sectionName));
				}
			}
		}

		foreach($this->_config as $sectionName => $section)
		{
			if(isset($section['ignore']))
			{
				$ignores = explode(',', str_replace(' ', '', $section['ignore']));
				foreach($ignores as $ignore)
				{
					if(isset($this->_types[$ignore]))
					{
						if(isset($this->_types[$ignore]->exclude))
						{
							$this->_types[$ignore]->exclude[] = $sectionName;
						}
						else
						{
							$this->_types[$ignore]->exclude = array($sectionName);
						}
					}
				}
			}
		}
		
		uasort($this->_types, array($this, 'compareTypes'));
		
		$sortedTypes = array();
		$this->fixTypeDependencies($this->_types, $sortedTypes);
		$this->_types = $sortedTypes;
	}
	
	protected function getServiceTags($service)
	{
		$tags = array();

		foreach($this->_config as $sectionName => $section)
		{
			if(isset($section['include']))
			{
				$includes = explode(',', str_replace(' ', '', $section['include']));
				if(in_array("$service.*", $includes))
				{
					$tags[] = $sectionName;
					continue;
				}
				
				foreach($includes as $include)
				{
					if(strpos($include, "$service.") === 0)
					{
						$tags[] = $sectionName;
						break;
					}
				}
				continue;
			}

			$add = true;
			if(isset($section['exclude']))
			{
				$excludes = explode(',', str_replace(' ', '', $section['exclude']));
				if(in_array("$service.*", $excludes))
					continue;
				
				foreach($excludes as $exclude)
				{
					if(strpos($exclude, "$service.") === 0)
					{
						$add = false;
						break;
					}
				}
			}
			
			if($add)
				$tags[] = $sectionName;
		}
		
		return $tags;
	}
	
	protected function getActionTags($service, $action)
	{
		$tags = array();
		
		foreach($this->_config as $sectionName => $section)
		{
			if(isset($section['include']))
			{
				$includes = explode(',', str_replace(' ', '', $section['include']));
				if(in_array("$service.*", $includes) || in_array("$service.$action", $includes))
					$tags[] = $sectionName;
			}
			elseif(isset($section['exclude']))
			{
				$excludes = explode(',', str_replace(' ', '', $section['exclude']));
				if(!in_array("$service.*", $excludes) && !in_array("$service.$action", $excludes))
					$tags[] = $sectionName;
			}
			else 
			{
				$tags[] = $sectionName;
			}
		}
		
		return $tags;
	}
	
	/**
	 * This function arranges the types in bottom up order
	 * @param array $input
	 * @param array $output
	 */
	private function fixTypeDependencies(array &$input, array &$output, &$added = array())
	{
		foreach ($input as $typeName => $typeReflector)
		{
			if (array_key_exists($typeName, $output))
				continue;		// already added

			if(isset($added[$typeName]))
				continue;
			$added[$typeName] = true;

			if (!$typeReflector->isEnum() && !$typeReflector->isStringEnum())
			{
				$dependencies = $this->getTypeDependencies($typeReflector);
				if(isset($typeReflector->include) && count($typeReflector->include))
				{
					foreach($dependencies as &$dependency)
						if(isset($dependency->include))
							$dependency->include = array_unique(array_merge($dependency->include, $typeReflector->include));
				}
				else
				{
					foreach($dependencies as &$dependency)
						if(isset($dependency->include))
							unset($dependency->include);
				}
				$this->fixTypeDependencies($dependencies, $output, $added);
			}
			
			$output[$typeName] = $typeReflector;
		}
	}
	
	/**
	 * @param KalturaTypeReflector $a
	 * @param KalturaTypeReflector $b
	 */
	protected function compareTypes(KalturaTypeReflector $a, KalturaTypeReflector $b)
	{
		// enums at the begining
		if($a->isEnum() && !$b->isEnum())
			return -1;
			
		if($b->isEnum() && !$a->isEnum())
			return 1;

		if($a->isStringEnum() && !$b->isStringEnum())
			return -1;
			
		if($b->isStringEnum() && !$a->isStringEnum())
			return 1;
			
		if($a->getInheritanceLevel() != $b->getInheritanceLevel())
			return ($a->getInheritanceLevel() < $b->getInheritanceLevel() ? -1 : 1);
			
		return strcmp($a->getType(), $b->getType());
	}
	
	/**
	 * Called to write the header
	 *
	 */
	protected abstract function writeHeader();
	
	/**
	 * Called to write the footer
	 *
	 */
	protected abstract function writeFooter();
	
	protected abstract function writeBeforeServices();
	
	protected abstract function writeBeforeService(KalturaServiceActionItem $serviceReflector);
	
	/**
	 * Called while looping the actions inside a service to write the service action description
	 *
	 * @param string $serviceName
	 * @param string $action
	 * @param array $actionParams
	 * @param KalturaTypeReflector $outputTypeReflector
	 */
	protected abstract function writeServiceAction($serviceId, $serviceName, $action, $actionParams, $outputTypeReflector);
	
	protected abstract function writeAfterService(KalturaServiceActionItem $serviceReflector);
	
	protected abstract function writeAfterServices();
	
	protected abstract function writeBeforeTypes();
	
	/**
	 * Called to write the description of a type
	 *
	 * @param array $typeDescription
	 */
	protected abstract function writeType(KalturaTypeReflector $type);
	
	protected abstract function writeAfterTypes();
	

	/**
	 * Scans the file system and loads the description of the services to an array
	 *
	 */
	protected function loadServicesInfo()
	{
		$this->initClassMap();
		
		$alwaysIncludeList = array(
			'KalturaApiExceptionArg',
			'KalturaClientConfiguration',
			'KalturaRequestConfiguration',
		);
		
		foreach($alwaysIncludeList as $class)
		{
			$classTypeReflector = KalturaTypeReflectorCacher::get($class);
			if($classTypeReflector)
				$this->loadTypesRecursive($classTypeReflector);
		}
		
		$serviceMap = KalturaServicesMap::getMap();
		foreach($serviceMap as $serviceId => $serviceActionItem)
		{
		    /* @var $serviceActionItem KalturaServiceActionItem */
			
			$serviceActionItem->include = $this->getServiceTags($serviceId);
  			$serviceActionItemToAdd = KalturaServiceActionItem::cloneItem($serviceActionItem);
		    foreach ($serviceActionItem->actionMap as $actionId => $actionCallback)
		    {
		        list ( $serviceClassName, $actionMethodName) = array_values($actionCallback);
		        
		        //Check if the service path for the current action is excluded
		        $servicePath = $this->_classMap[$serviceClassName];
      			
      			$actionReflector = new KalturaActionReflector($serviceId, $actionId, $actionCallback);
      			if (!$this->shouldUseServiceAction($actionReflector))
      			{
      			    continue;
      			}
      			$actionReflector->include = $this->getActionTags($serviceId, $actionId);
      			
      			$serviceActionItemToAdd->actionMap[$actionId] = $actionReflector;
      			
      			$actionParams = $actionReflector->getActionParams();
      			
      			foreach ($actionParams as $actionParam)
      			{
      			    if ($actionParam->isComplexType())
					{
						$typeReflector = KalturaTypeReflectorCacher::get($actionParam->getType());
						if(!$typeReflector)
							throw new Exception("Couldn't load type reflector for service [$serviceId] action [$actionId] param type[" . $actionParam->getType() . "]");
						
						$this->loadTypesRecursive($typeReflector, array(), $actionReflector->include);
					}
      			}
      			
		        $outputInfo = $actionReflector->getActionOutputType();
				if ($outputInfo && $outputInfo->isComplexType())
				{
					$typeReflector = $outputInfo->getTypeReflector();
					if(!$typeReflector)
						throw new Exception("Couldn't load type reflector for service [$serviceId] action [$actionId] output type[" . $outputInfo->getType() . "]");
						
					$this->loadTypesRecursive($typeReflector, array(), $actionReflector->include);
				}
		    }
		    
		    if ( count($serviceActionItem->actionMap) )
		    {
		        $this->_services[$serviceId] = $serviceActionItemToAdd;
		    }
		}
	}
	
	private function getTypeDependencies(KalturaTypeReflector $typeReflector)
	{
		$result = array();
		
	    $parentTypeReflector = $typeReflector->getParentTypeReflector();
	    if ($parentTypeReflector)
	    {
            $result[$parentTypeReflector->getType()] = $parentTypeReflector;
	    }
	    
		$properties = $typeReflector->getProperties();
		foreach($properties as $property)
		{
			$subTypeReflector = $property->getTypeReflector();
			if ($subTypeReflector)
				$result[$subTypeReflector->getType()] = $subTypeReflector;
		}
		
		if ($typeReflector->isArray() && !$typeReflector->isAbstract())
		{
			$arrayTypeReflector = KalturaTypeReflectorCacher::get($typeReflector->getArrayType());
			if($arrayTypeReflector)
				$result[$arrayTypeReflector->getType()] = $arrayTypeReflector;
		}

		return $result;
	}

	private function loadTypesRecursive(KalturaTypeReflector $typeReflector, $loaded = array(), $include = null)
	{
		if (isset($this->_types[$typeReflector->getType()]))
		{
			$this->addType($typeReflector, $include);
			return;
		}
		
		if(isset($loaded[$typeReflector->getType()]))
			return;
		
		$loaded[$typeReflector->getType()] = true;
			
		$this->initClassMap();
			
		foreach ($this->getTypeDependencies($typeReflector) as $subTypeReflector)
		{
			$this->loadTypesRecursive($subTypeReflector, $loaded, $include);
		}
		
		if ($typeReflector->getType() != 'KalturaObject')
			$this->loadChildTypes($typeReflector, $include);
	}
	
	protected function getTypesClassMapPath()
	{
		$class = get_class($this);
		$dir = kConf::get("cache_root_path") . "/generator";
		if (!is_dir($dir))
			mkdir($dir, 0777, true);
			
		return "$dir/$class.typeClassMap.cache";
	}
	
	private function loadChildTypes(KalturaTypeReflector $typeReflector, $include)
	{
		if (isset($this->_types[$typeReflector->getType()]))
		{
			$this->addType($typeReflector, $include);
			return;
		}
	
		$this->addType($typeReflector, $include);
		
		$cacheTypesClassMap = false;
		if(!$this->_typesClassMap)
		{
			$this->initClassMap();
			$this->_typesClassMap = $this->_classMap;
			$cacheTypesClassMap = true;
		}
		foreach($this->_typesClassMap as $class => $path)
		{
			if (strpos($class, 'Kaltura') === 0 && strpos($class, '_') === false && strpos($path, 'api') !== false) // make sure the class is api object
			{
				$reflector = new ReflectionClass($class);
				if ($reflector->isSubclassOf('KalturaObject') && $reflector->isSubclassOf($typeReflector->getType()))
				{
					$classTypeReflector = KalturaTypeReflectorCacher::get($class);
					if(!$classTypeReflector)
						throw new Exception("Type [$class] reflector not found");
						
					$pluginName = $classTypeReflector->getPlugin();
					if ($pluginName && !KalturaPluginManager::getPluginInstance($pluginName))
					{
						unset($this->_typesClassMap[$class]);
						continue;
					}
					
					if($classTypeReflector)
						$this->loadTypesRecursive($classTypeReflector, $include);
				}
			}
			else
			{
				unset($this->_typesClassMap[$class]);
			}
		}
		
		if($cacheTypesClassMap)
		{
			file_put_contents($this->getTypesClassMapPath(), serialize($this->_typesClassMap));
		}
	}
	
	protected function shouldUseServiceAction (KalturaActionReflector $actionReflector)
	{
	    $serviceId = $actionReflector->getServiceId();
	    
	    if ($actionReflector->getActionClassInfo()->serverOnly)
	    {
	        KalturaLog::info("Service [".$serviceId."] is server only");
	        return false;
	    }
	    
	    $actionId = $actionReflector->getActionId();
	    if (strpos($actionReflector->getActionInfo()->clientgenerator, "ignore") !== false)
	    {
	        KalturaLog::info("Action [$actionId] in service [$serviceId] ignored by generator");
	        return false;
	    }
	    
	    if (isset($this->_serviceActions[$serviceId]) && isset($this->_serviceActions[$serviceId][$actionId]))
	    {
	        KalturaLog::err("Service [$serviceId] action [$actionId] already exists!");
	        return false;
	    }
        
	    return true;
	}
	
	
	protected function addType(KalturaTypeReflector $objectReflector, $include = null)
	{
		$type = $objectReflector->getType();
	
		if (isset($this->_types[$type]))
		{
			if($include)
			{
				if(isset($this->_types[$type]->include))
				{
					$this->_types[$type]->include = array_unique(array_merge($this->_types[$type]->include, $include));
				}
				else
				{
					$this->_types[$type]->include = $include;
				}	
			}
			return;
		}
		
		if($objectReflector->isServerOnly())
		{
			KalturaLog::info("Type is server only [$type]");
			return;
		}
			
		$objectReflector->include = $include;
		$this->_types[$type] = $objectReflector;
	}
	
	public function setAdditionalList($list)
	{
		if ($list === null)
			return;
	
		$includeList = array();
		if(is_array($list))
		{
			$includeList = $list;
		}
		else
		{
			$tempList = explode(",", str_replace(" ", "", $list));
			foreach($tempList as $item)
				if(class_exists($item))
					$includeList[] = $item;
		}
		
		foreach($includeList as $class)
		{
			$classTypeReflector = KalturaTypeReflectorCacher::get($class);
			if($classTypeReflector)
				$this->loadTypesRecursive($classTypeReflector);
		}
	}
	
	/**
	 * Check if a string ends with another string
	 *
	 * @param string $str
	 * @param string $end
	 * @return boolean
	 */
	protected function endsWith($str, $end)
	{
		return (substr($str, strlen($str) - strlen($end)) === $end);
	}
	
	/**
	 * Check if a string begins with another string
	 *
	 * @param string $str
	 * @param string $end
	 * @return boolean
	 */
	protected function beginsWith($str, $end)
	{
		return (substr($str, 0, strlen($end)) === $end);
	}
	
	public function done($outputPath)
	{
	}
}
