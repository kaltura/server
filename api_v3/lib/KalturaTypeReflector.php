<?php

/**
 * This class is used to reflect specific Kaltura objects, arrays & enums
 * This will be the place to boost performance by caching the reflection results to memcache or the filesystem 
 *
 */
class KalturaTypeReflector
{
	static private $properyReservedWords = array(
		'objectType',
	);
	
	static private $_classMap = array();
	static private $_classInheritMap = array();
	static private $_classInheritMapLocation = "";
	
	/**
	 * @var string
	 */
	private $_type;
	
	/**
	 * @var KalturaObject
	 */
	private $_instance;
	
	/**
	 * @var array<KalturaPropertyInfo>
	 */
	private $_properties;
	
	/**
	 * @var array<KalturaPropertyInfo>
	 */
	private $_currentProperties;
	
	/**
	 * @var array<KalturaPropertyInfo>
	 */
	private $_constants;
	
	/**
	 * @var bool
	 */
	private $_isEnum;
	
	/**
	 * @var bool
	 */
	private $_isStringEnum;
	
	/**
	 * @var bool
	 */
	private $_isDynamicEnum;
	
	/**
	 * @var bool
	 */
	private $_isArray;
	
	/**
	 * @var string
	 */
	private $_description;
	
	/**
	 * @var bool
	 */
	private $_deprecated = false;
	
	/**
	 * @var bool
	 */
	private $_abstract = false;
	
	/**
	 * Contructs new type reflector instance
	 *
	 * @param string $type
	 * @return KalturaTypeReflector
	 */
	public function KalturaTypeReflector($type)
	{
//		KalturaLog::debug("Reflecting type [$type]");
		
		if (!class_exists($type))
			throw new KalturaReflectionException("Type \"".$type."\" not found");
			
		$this->_type = $type;
		
	    $reflectClass = new ReflectionClass($this->_type);
	    $comments = $reflectClass->getDocComment();
	    if($comments)
	    {
	    	$commentsParser = new KalturaDocCommentParser($comments);
	    	$this->_deprecated = $commentsParser->deprecated;
	    	$this->_abstract = $commentsParser->abstract;
	    }
	    
	    if(!$reflectClass->isAbstract())
	    {
	    	$constructor = $reflectClass->getConstructor();
	    	if(!$constructor || $constructor->isPublic())
	    	{
//				KalturaLog::debug("Instanciating type [$type]");
				$this->_instance = new $type;
	    	}
	    }
	}
	
	/**
	 * Returns the type of the reflected class
	 *
	 * @return string
	 */
	public function getType()
	{
		return $this->_type;
	}
	
	/**
	 * Return property by name 
	 * @param string $name
	 * @return KalturaPropertyInfo
	 */
	public function getProperty($name)
	{
		if ($this->_properties === null)
			$this->getProperties();
			
		if(!isset($this->_properties[$name]))
			return null;
			
		return $this->_properties[$name];
	}
	
	/**
	 * Return the type properties 
	 *
	 * @return array
	 */
	public function getProperties()
	{
		if ($this->_properties === null)
		{
			$this->_properties = array();
			$this->_currentProperties = array();
			
			if (!$this->isEnum() && !$this->isArray())
			{
				$reflectClass = new ReflectionClass($this->_type);
				$classesHierarchy = array();
				$classesHierarchy[] = $reflectClass;
				$parentClass = $reflectClass;
				
				// lets get the class hierarchy so we could order the properties in the right order
				while($parentClass = $parentClass->getParentClass())
				{
					$classesHierarchy[] = $parentClass;
				}
				
				// reverse the hierarchy, top class properties should be first 
				$classesHierarchy = array_reverse($classesHierarchy);
				foreach($classesHierarchy as $currentReflectClass)
				{
					$properties = $currentReflectClass->getProperties(ReflectionProperty::IS_PUBLIC);
					foreach($properties as $property)
					{
						if ($property->getDeclaringClass() == $currentReflectClass) // only properties defined in the current class, ignore the inherited
						{
							$name = $property->name;
							if(in_array($name, self::$properyReservedWords))
								throw new Exception("Property name [$name] is a reserved word in type [$currentReflectClass]");
								
							$docComment = $property->getDocComment();
							$parsedDocComment = new KalturaDocCommentParser( $docComment );
							if ($parsedDocComment->varType)
							{
								$prop = new KalturaPropertyInfo($parsedDocComment->varType, $name);
								
								if ($parsedDocComment->readOnly)
									$prop->setReadOnly(true);
								
								if ($parsedDocComment->insertOnly)
									$prop->setInsertOnly(true);
								
								if ($parsedDocComment->writeOnly)
									$prop->setWriteOnly(true);
								
								if ($parsedDocComment->dynamicType)
									$prop->setDynamicType($parsedDocComment->dynamicType);
									
								$this->_properties[$name] = $prop;
								
								if ($property->getDeclaringClass() == $reflectClass) // store current class properties
								{
								     $this->_currentProperties[] = $prop;   
								}
							}
							
							if ($parsedDocComment->description)
								$prop->setDescription($parsedDocComment->description);
								
							if ($parsedDocComment->filter)
								$prop->setFilters($parsedDocComment->filter);
						}
					}
				}
				
				$reflectClass = null;
			}
		}
		
		return $this->_properties;
	}
	
	/**
	 * Return a type reflector for the parent class (null if none) 
	 *
	 * @return KalturaTypeReflector
	 */
	public function getParentTypeReflector()
	{
	    $reflectClass = new ReflectionClass($this->_type);
	    $parentClass = $reflectClass->getParentClass();
	    if (!$parentClass)
	    	throw new Exception("API object [$this->_type] must have parent type");
	    	
	    $parentClassName = $parentClass->getName();
	    if (!in_array($parentClassName, array("KalturaObject", "KalturaEnum", "KalturaStringEnum", "KalturaTypedArray"))) // from the api point of view, those objects are ignored
            return KalturaTypeReflectorCacher::get($parentClass->getName());
	    else
	        return null;
	}
	
	/**
	 * Return a array of all sub classes names 
	 *
	 * @return array
	 */
	public function getSubTypesNames()
	{
		return self::getSubClasses($this->_type);
	}
	
	/**
	 * Return only the properties defined in the current class
	 *
	 * @return array
	 */
	public function getCurrentProperties()
	{
		if ($this->_currentProperties === null)
		{
		    $this->getProperties();
		}
		
		return $this->_currentProperties;
	}
	
	/**
	 * Returns the enum constants
	 *
	 * @return array
	 */
	public function getConstants()
	{
		if ($this->_constants === null)
		{
			$this->_constants = array();
			
			if ($this->isEnum() || $this->isStringEnum())
			{
				$reflectClass = new ReflectionClass($this->_type);
				$constantsDescription = array();
				if ($reflectClass->hasMethod("getDescription"))
					$constantsDescription = $reflectClass->getMethod("getDescription")->invoke($this->_instance);
				$contants = $reflectClass->getConstants();
				foreach($contants as $enum => $value)
				{
					if ($this->isEnum())
						$prop = new KalturaPropertyInfo("int", $enum);
					else
						$prop = new KalturaPropertyInfo("string", $enum);
						
					if (array_key_exists($value, $constantsDescription))
						$prop->setDescription($constantsDescription[$value]);
					$prop->setDefaultValue($value);
					$this->_constants[] = $prop;
				}
			}
			
			if($this->isDynamicEnum())
			{
				$type = $this->getType();
				// TODO remove call_user_func after moving to php 5.3
				$baseEnumName = call_user_func("$type::getEnumClass");
//				$baseEnumName = $type::getEnumClass();
				$pluginInstances = KalturaPluginManager::getPluginInstances('IKalturaEnumerator');
				foreach($pluginInstances as $pluginInstance)
				{
					$pluginName = $pluginInstance->getPluginName();
					$enums = $pluginInstance->getEnums($baseEnumName);
					foreach($enums as $enum)
					{
						// TODO remove call_user_func after moving to php 5.3
						$enumConstans = call_user_func("$enum::getAdditionalValues");
//						$enumConstans = $enum::getAdditionalValues();
						foreach($enumConstans as $name => $value)
						{
							$prop = new KalturaPropertyInfo("string", $name);
							$prop->setDefaultValue($pluginName . IKalturaEnumerator::PLUGIN_VALUE_DELIMITER . $value);
							$this->_constants[] = $prop;
						}
					}
				}
			}
		}
		
		return $this->_constants;
	}
	
	/**
	 * Returns true when the type is (for what we know) an enum
	 *
	 * @return boolean
	 */
	public function isEnum()
	{
		if ($this->_isEnum === null)
		{
			if ($this->_instance instanceof KalturaEnum)
				$this->_isEnum = true;
			else
				$this->_isEnum = false;
		}
			
		return $this->_isEnum; 
	}
	
	/**
	 * Returns true when the type is depracated
	 *
	 * @return boolean
	 */
	public function isDeprecated()
	{
		return $this->_deprecated; 
	}
	
	/**
	 * Returns true when the type is abstract
	 *
	 * @return boolean
	 */
	public function isAbstract()
	{
		return $this->_abstract; 
	}
	
	/**
	 * Returns true when the type is a string enum
	 *
	 * @return boolean
	 */
	public function isStringEnum()
	{
		if ($this->_isStringEnum === null)
		{
			if ($this->_instance instanceof KalturaStringEnum)
				$this->_isStringEnum = true;
			else
				$this->_isStringEnum = false;
		}
			
		return $this->_isStringEnum; 
	}
	
	/**
	 * Returns true when the type is a dynamic enum
	 *
	 * @return boolean
	 */
	public function isDynamicEnum()
	{
		if ($this->_isDynamicEnum === null)
		{
			if ($this->_instance instanceof KalturaDynamicEnum)
				$this->_isDynamicEnum = true;
			else
				$this->_isDynamicEnum = false;
		}
			
		return $this->_isDynamicEnum; 
	}
	
	
	/**
	 * Returns true when the type is (for what we know) an array
	 *
	 * @return boolean
	 */
	public function isArray()
	{
		if ($this->_isArray === null)
		{
			if ($this->_instance instanceof KalturaTypedArray)
				$this->_isArray = true;
			else
				$this->_isArray = false;
		}
			
		return $this->_isArray;
	}
	
	/**
	 * When reflecting an array, returns the type of the array as string
	 *
	 * @return string
	 */
	public function getArrayType()
	{
		if ($this->isArray())
		{
			return $this->_instance->getType(); 
		}
		return null;
	}
	
	public function setDescription($desc)
	{
		$this->_description = $desc;
	}
	
	public function getDescription()
	{
		return $this->_description;
	}	
	
	/**
	 * Checks whether the enum value is valid for the reflected enum 
	 *
	 * @param mixed $value
	 * @return boolean
	 */
	public function checkEnumValue($value)
	{
		if (!$this->isEnum())
			return false;
			
		$this->getConstants();
		
		foreach($this->_constants as $constValue)
		{
			if ((int)$constValue->getDefaultValue() === (int)$value)
			{
				return true;
			}
		}
		return false;
	}
	
	/**
	 * Checks whether the string enum value is valid for the reflected string enum 
	 *
	 * @param mixed $value
	 * @return boolean
	 */
	public function checkStringEnumValue($value)
	{
		if (!$this->isStringEnum())
			return false;
			
		$this->getConstants();
		
		foreach($this->_constants as $constValue)
		{
			if ((string)$constValue->getDefaultValue() === (string)$value)
			{
				return true;
			}
		}
		
		if($this->isDynamicEnum())
		{
			$split = explode(IKalturaEnumerator::PLUGIN_VALUE_DELIMITER, $value, 2);
			if(count($split) == 1)
				return false;
				
			list($pluginName, $valueName) = $split;
			
			$type = $this->getType();
			
			// TODO remove call_user_func after moving to php 5.3
			$baseEnumName = call_user_func("$type::getEnumClass");
	//		$baseEnumName = $type::getEnumClass();
	 
			$pluginInstance = KalturaPluginManager::getPluginInstance($pluginName);
			$enums = $pluginInstance->getEnums($baseEnumName);
			
			foreach($enums as $enum)
			{		
				// TODO remove call_user_func after moving to php 5.3
				$enumConstans = call_user_func("$enum::getAdditionalValues");
	//			$enumConstans = $enum::getAdditionalValues();
				if(in_array($valueName, $enumConstans))
					return true;
			}
		}
		
		return false;
	}
	
	/**
	 * @param string $class
	 * @return boolean
	 */
	public function isParentOf($class)
	{
	    if (!class_exists($class))
	        return false;
	        
	    $possibleReflectionClass = new ReflectionClass($class);
        return $possibleReflectionClass->isSubclassOf(new ReflectionClass($this->_type));
	}
	
	public function isFilterable()
	{
		$reflectionClass = new ReflectionClass($this->_type);
		return $reflectionClass->implementsInterface("IFilterable");
	}
	
	public function getInstance()
	{
		return $this->_instance;
	}
	
	public function __sleep()
	{
		if ($this->_properties === null)
			$this->getProperties();
			
		if ($this->_constants === null)
			$this->getConstants();
			
		return array("_type", "_instance", "_properties", "_currentProperties", "_constants", "_isEnum", "_isStringEnum", "_isDynamicEnum", "_isArray", "_description");
	}


	/**
	 * Set the class inherit map cache file path
	 * 
	 * @param string $path
	 */
	static function setClassInheritMapPath($path)
	{
		self::$_classInheritMapLocation = $path;
	}
	
	/**
	 * @return bool
	 */
	static function hasClassInheritMapCache()
	{
		return file_exists(self::$_classInheritMapLocation);
	}
	
	/**
	 * Set the class map array
	 * 
	 * @param array $map
	 */
	static function setClassMap(array $map)
	{
		self::$_classMap = $map;
	}
	
	protected static function loadSubClassesMap()
	{
		self::$_classInheritMap = array();
	
		if (!file_exists(self::$_classInheritMapLocation))
		{
			foreach(self::$_classMap as $class)
			{
				if(!class_exists($class))
					continue;
					
				$parentClass = get_parent_class($class);
				while($parentClass)
				{
					if(!isset(self::$_classInheritMap[$parentClass]))
						self::$_classInheritMap[$parentClass] = array();
						
					self::$_classInheritMap[$parentClass][] = $class;
					
					$parentClass = get_parent_class($parentClass);
				}
			}
			
			file_put_contents(self::$_classInheritMapLocation, serialize(self::$_classInheritMap));
		}
		else 
		{
			self::$_classInheritMap = unserialize(file_get_contents(self::$_classInheritMapLocation));
		}
	}
	
	public static function getSubClasses($class)
	{
		if(!count(self::$_classInheritMap))
			self::loadSubClassesMap();
			
		if(isset(self::$_classInheritMap[$class]))
			return self::$_classInheritMap[$class];
			
		return array();
	}
}