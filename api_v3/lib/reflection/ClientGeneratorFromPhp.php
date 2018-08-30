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
		$this->loadTypes();
		
		$this->loadServicesInfo();
		
		// load the filter order by string enums
		foreach($this->_types as $typeReflector)
		{
			if (strpos($typeReflector->getType(), "Filter", strlen($typeReflector->getType()) - 6))
			{
				$filterOrderByStringEnumTypeName = str_replace("Filter", "OrderBy", $typeReflector->getType());
				if (class_exists($filterOrderByStringEnumTypeName))
					$this->addType(KalturaTypeReflectorCacher::get($filterOrderByStringEnumTypeName));
			}
		}

		uasort($this->_types, array($this, 'compareTypes'));
		
		$sortedTypes = array();
		$this->fixTypeDependencies($this->_types, $sortedTypes);
		$this->_types = $sortedTypes;
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
		
		$serviceMap = KalturaServicesMap::getMap();
		foreach($serviceMap as $serviceId => $serviceActionItem)
		{
		    /* @var $serviceActionItem KalturaServiceActionItem */
			
  			$serviceActionItemToAdd = KalturaServiceActionItem::cloneItem($serviceActionItem);
		    foreach ($serviceActionItem->actionMap as $actionId => $actionCallback)
		    {
      			$actionReflector = new KalturaActionReflector($serviceId, $actionId, $actionCallback);
      			if ($this->shouldUseServiceAction($actionReflector))      			
      				$serviceActionItemToAdd->actionMap[$actionId] = $actionReflector;
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

	protected function getTypesClassMapPath()
	{
		$class = get_class($this);
		$dir = kConf::get("cache_root_path") . "/generator";
		if (!is_dir($dir))
			mkdir($dir, 0777, true);
			
		return "$dir/$class.typeClassMap.cache";
	}
	
	private function loadTypes()
	{
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
				if ($reflector->isSubclassOf('KalturaObject') || $reflector->isSubclassOf('KalturaEnum') || $reflector->isSubclassOf('KalturaStringEnum'))
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
					
					$this->addType($classTypeReflector);
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
	
	
	protected function addType(KalturaTypeReflector $objectReflector)
	{
		$type = $objectReflector->getType();
	
		if (isset($this->_types[$type]))
		{
			return;
		}
		
		if($objectReflector->isServerOnly())
		{
			KalturaLog::info("Type is server only [$type]");
			return;
		}
			
		$this->_types[$type] = $objectReflector;
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
