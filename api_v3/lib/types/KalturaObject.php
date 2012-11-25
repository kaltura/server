<?php 
/**
 * @package api
 * @subpackage objects
 */
abstract class KalturaObject 
{
	static protected $fromObjectMap = array();
	
	protected function getReadOnly ()
	{
		
	}
	
	// TODO - get the set of properties from the annotations
	protected function getPropertiresForField ( $field )
	{
		
	}
	
	/**
	 * Function tests whether a property on the object is null.
	 * This can occur in case the property is actually null or if it is instance of type KalturaNullField
	 * @param string $propertyName
	 * @return bool
	 */
	protected function isNull ($propertyName)
	{
	    if (!property_exists(get_class($this), $propertyName) || is_null($this->$propertyName) || $this->$propertyName instanceof KalturaNullField)
	    {
	        return true;
	    }
	    return false;
	}
	
	protected function getMapBetweenObjects ( )
	{
		return array();
	}
	
	private function getDeclaringClassName($propertyName)
	{
		$reflection = new ReflectionProperty(get_class($this), $propertyName);
		$declaringClass = $reflection->getDeclaringClass();
		$className = $declaringClass->getName();
		return $className;
	}
		

	public function getFromObjectMap ()
	{
		$className = get_class($this);
		$cacheKey = kCurrentContext::$ks_hash . '_' . $className;
		if (isset(self::$fromObjectMap[$cacheKey]))
			return self::$fromObjectMap[$cacheKey];

		$reflector = KalturaTypeReflectorCacher::get($className);
		if(!$reflector)
			return array();
			
		$properties = $reflector->getProperties();
		
		if ($reflector->requiresReadPermission() && !kPermissionManager::getReadPermitted($className, kApiParameterPermissionItem::ALL_VALUES_IDENTIFIER)) {
			return array(); // current user has no permission for accessing this object class
		}
		
		$result = array(); 
		foreach ( $this->getMapBetweenObjects() as $this_prop => $object_prop )
		{
			if ( is_numeric( $this_prop) ) 
			    $this_prop = $object_prop;
			    
			if(!isset($properties[$this_prop]) || $properties[$this_prop]->isWriteOnly())
				continue;
				
			// ignore property if it requires a read permission which the current user does not have
			if ($properties[$this_prop]->requiresReadPermission() && !kPermissionManager::getReadPermitted($this->getDeclaringClassName($this_prop), $this_prop))
			{
			    KalturaLog::debug("Missing read permission for property $this_prop");
				continue;
			}
				
            $getter_name = "get{$object_prop}";
            
            $getter_params = array();
            if (in_array($this_prop, array("createdAt", "updatedAt", "deletedAt")))
				$getter_params = array(null);            
            
			$arrayClass = null;
			if ($properties[$this_prop]->isArray())
			{
				$class = $properties[$this_prop]->getType();
				if(method_exists($class, 'fromDbArray'))
					$arrayClass = $class;
			}
			
			$enumMap = null;
			if ($properties[$this_prop]->isDynamicEnum())
            {
            	$propertyType = $properties[$this_prop]->getType();
            	$enumClass = call_user_func(array($propertyType, 'getEnumClass'));
            	if ($enumClass)
            		$enumMap = kPluginableEnumsManager::getCoreMap($enumClass);
            }
            
            $result[] = array($this_prop, $getter_name, $getter_params, $arrayClass, $enumMap);
		}

		self::$fromObjectMap[$cacheKey] = $result;
		return $result;
	}
	
	public function fromObject ( $source_object  )
	{
		$map = $this->getFromObjectMap();
		foreach ($map as $curProp)
		{
			list($this_prop, $getter_name, $getter_params, $arrayClass, $enumMap) = $curProp;

			$getter_callback = array($source_object, $getter_name);
			
            if (!is_callable($getter_callback))
            {
            	KalturaLog::alert("getter for property [$this_prop] was not found on object class [" . get_class($source_object) . "]");
            	continue;
            }
            
            $value = call_user_func_array($getter_callback, $getter_params);
                
            if($arrayClass && is_array($value))
            {
             	$value = call_user_func(array($arrayClass, 'fromDbArray'), $value);
            }
            elseif($enumMap && isset($enumMap[$value]))
            {
              	$value = $enumMap[$value];
            }
               	
            $this->$this_prop = $value;
		}
	}
	
	public function fromArray ( $source_array )
	{
		foreach ( $this->getMapBetweenObjects() as $this_prop => $object_prop )
		{
			if ( is_numeric( $this_prop ) ) $this_prop = $object_prop;
			$this->$this_prop = isset($source_array[$object_prop]) ? $source_array[$object_prop] : null;
		}
	}
	
	public function toObject($object_to_fill = null, $props_to_skip = array())
	{
		$this->validateForUsage($object_to_fill, $props_to_skip); // will check that not useable properties are not set 
		$class = get_class($this);
		
		// enables extension with default empty object
		if(is_null($object_to_fill))
		{
			KalturaLog::err("No object supplied for type [$class]");
			return null;
		}
			
		$typeReflector = KalturaTypeReflectorCacher::get($class);
		
		foreach ( $this->getMapBetweenObjects() as $this_prop => $object_prop )
		{
		 	if ( is_numeric( $this_prop) ) 
		 		$this_prop = $object_prop;
			
			$value = $this->$this_prop;
			if (is_null($value)) 
				continue;
				
			if (in_array($this_prop, $props_to_skip)) 
				continue;
				
			$propertyInfo = $typeReflector->getProperty($this_prop);
			if (!$propertyInfo)
			{
	            KalturaLog::alert("property [$this_prop] was not found on object class [$class]");
	            continue;
			}
			
			if ($value instanceof KalturaNullField)
			{
				$value = null;
			}
			elseif ($value instanceof KalturaTypedArray)
			{
				$value = $value->toObjectsArray();
			}
			elseif ($propertyInfo->isComplexType() && $value instanceof KalturaObject)
			{
				$value = $value->toObject();
			}
			elseif ($propertyInfo->isDynamicEnum())
			{
				$propertyType = $propertyInfo->getType();
				$enumType = call_user_func(array($propertyType, 'getEnumClass'));
				$value = kPluginableEnumsManager::apiToCore($enumType, $value);
			}
			elseif (is_string($value) && ! kXml::isXMLValidContent($value) )
			{
				throw new KalturaAPIException ( KalturaErrors::INVALID_PARAMETER_CHAR, $this_prop );
			}
			
			$setter_callback = array ( $object_to_fill ,"set{$object_prop}");
			if (is_callable($setter_callback))
		 	    call_user_func_array( $setter_callback , array ($value ) );
	 	    else 
            	KalturaLog::alert("setter for property [$object_prop] was not found on object class [" . get_class($object_to_fill) . "] defined as property [$this_prop] on api class [$class]");
		}
		return $object_to_fill;		
	}
	
	public function toUpdatableObject ( $object_to_fill , $props_to_skip = array() )
	{
		$this->validateForUpdate($object_to_fill, $props_to_skip); // will check that not updatable properties are not set 
		
		return $this->toObject($object_to_fill, $props_to_skip);
	}
	
	public function toInsertableObject ( $object_to_fill = null , $props_to_skip = array() )
	{
		$this->validateForInsert($props_to_skip); // will check that not insertable properties are not set 
		
		return $this->toObject($object_to_fill, $props_to_skip);
	}
	
	public function validatePropertyNotNull($propertiesNames, $xor = false)
	{
        if (!is_array($propertiesNames))
        {
            $propertyName = $propertiesNames;
    		if ($this->isNull($propertyName))
    		{
    			throw new KalturaAPIException(KalturaErrors::PROPERTY_VALIDATION_CANNOT_BE_NULL, $this->getFormattedPropertyNameWithClassName($propertyName));
    		}
        }
        else 
        {
            $isValidated = false;
            foreach ($propertiesNames as $propertyName)
            {
                if (!$this->isNull($propertyName))
                {
                    if (!$isValidated)
                    {
                        $isValidated = true;
                        if (!$xor)
                        {
                            return;
                        }
                    }
                    else
                    {
                        if ($xor)
                            throw new KalturaAPIException(KalturaErrors::PROPERTY_VALIDATION_ALL_MUST_BE_NULL_BUT_ONE, implode("/", $propertiesNames)); 
                    }
                }
            }
            if (!$isValidated)
                throw new KalturaAPIException(KalturaErrors::PROPERTY_VALIDATION_CANNOT_BE_NULL, implode("/", $propertiesNames));
        }
	}
	
	public function validatePropertyMinLength($propertyName, $minLength, $allowNull = false, $validateEachWord = false)
	{
		if(!$allowNull)
			$this->validatePropertyNotNull($propertyName);
		elseif(is_null($this->$propertyName))
			return;
		
		if ($this->$propertyName instanceof KalturaNullField) 
			return;
		
		if (strlen($this->$propertyName) < $minLength)
			throw new KalturaAPIException(KalturaErrors::PROPERTY_VALIDATION_MIN_LENGTH, $this->getFormattedPropertyNameWithClassName($propertyName), $minLength);
	
	    if ($validateEachWord)
	    {
	        $separateWords = explode(" ", $this->$propertyName);
	        foreach ($separateWords as $word)
	        {
	            if (strlen($word) < $minLength)
	            {
	                throw new KalturaAPIException(KalturaErrors::PROPERTY_VALIDATION_MIN_LENGTH, $this->getFormattedPropertyNameWithClassName($propertyName), $minLength);
	            }
	        }
	    }
	}
	
	
	public function validatePropertyNumeric($propertyName, $allowNull = false)
	{
		if($allowNull && is_null($this->$propertyName))
			return;
			
		$this->validatePropertyNotNull($propertyName);
		
		if ($this->$propertyName instanceof KalturaNullField)
			return;
		
		if (!is_numeric($this->$propertyName))
			throw new KalturaAPIException(KalturaErrors::PROPERTY_VALIDATION_NUMERIC_VALUE, $this->getFormattedPropertyNameWithClassName($propertyName));
	}
	
	public function validatePropertyMinValue($propertyName, $minValue, $allowNull = false)
	{
		if($allowNull && is_null($this->$propertyName))
			return;
			
		$this->validatePropertyNumeric($propertyName, $allowNull);
		
		if ($this->$propertyName instanceof KalturaNullField)
			return;
		
		if ($this->$propertyName < $minValue)
			throw new KalturaAPIException(KalturaErrors::PROPERTY_VALIDATION_MIN_VALUE, $this->getFormattedPropertyNameWithClassName($propertyName), $minValue);
	}
	
	public function validatePropertyMaxValue($propertyName, $maxValue, $allowNull = false)
	{
		if($allowNull && is_null($this->$propertyName))
			return;
			
		$this->validatePropertyNumeric($propertyName, $allowNull);
		
		if ($this->$propertyName instanceof KalturaNullField)
			return;
		
		if ($this->$propertyName > $maxValue)
			throw new KalturaAPIException(KalturaErrors::PROPERTY_VALIDATION_MAX_VALUE, $this->getFormattedPropertyNameWithClassName($propertyName), $maxValue);
	}
	
	public function validatePropertyMinMaxValue($propertyName, $minValue, $maxValue, $allowNull = false)
	{
		$this->validatePropertyMinValue($propertyName, $minValue, $allowNull);
		$this->validatePropertyMaxValue($propertyName, $maxValue, $allowNull);
	}
	
	public function validatePropertyMaxLength($propertyName, $maxLength, $allowNull = false)
	{
		if(!$allowNull) $this->validatePropertyNotNull($propertyName);
                
		if ($this->$propertyName instanceof KalturaNullField)
			return;
		                                          
		if (strlen($this->$propertyName) > $maxLength)
			throw new KalturaAPIException(KalturaErrors::PROPERTY_VALIDATION_MAX_LENGTH, $this->getFormattedPropertyNameWithClassName($propertyName), $maxLength);
	}
	
	public function validatePropertyMinMaxLength($propertyName, $minLength, $maxLength, $allowNull = false)
	{
		$this->validatePropertyMinLength($propertyName, $minLength, $allowNull);
		$this->validatePropertyMaxLength($propertyName, $maxLength, $allowNull);
	}
	
	public function getFormattedPropertyNameWithClassName($propertyName)
	{
		return get_class($this) . "::" . $propertyName;
	}
	
	public function validateForInsert($propertiesToSkip = array())
	{
		$reflector = KalturaTypeReflectorCacher::get(get_class($this));
		$properties = $reflector->getProperties();
		
		if ($reflector->requiresInsertPermission()&& !kPermissionManager::getInsertPermitted(get_class($this), kApiParameterPermissionItem::ALL_VALUES_IDENTIFIER)) {
			throw new KalturaAPIException(KalturaErrors::PROPERTY_VALIDATION_NO_INSERT_PERMISSION, get_class($this));
		}
		
		foreach($properties as $property)
		{
			$propertyName = $property->getName();
			
			if (in_array($propertyName, $propertiesToSkip)) 
				continue;
			
			if ($this->$propertyName !== null)
			{
				if ($property->isReadOnly())
				{
					throw new KalturaAPIException(KalturaErrors::PROPERTY_VALIDATION_NOT_UPDATABLE, $this->getFormattedPropertyNameWithClassName($propertyName));
				}
				// property requires insert permissions, verify that the current user has it
				if ($property->requiresInsertPermission())
				{
					if (!kPermissionManager::getInsertPermitted($this->getDeclaringClassName($propertyName), $propertyName)) {
						//throw new KalturaAPIException(KalturaErrors::PROPERTY_VALIDATION_NO_INSERT_PERMISSION, $this->getFormattedPropertyNameWithClassName($propertyName));
						//TODO: not throwing exception to not break clients that sends -1 as null for integer values (etc...)
						$e = new KalturaAPIException(KalturaErrors::PROPERTY_VALIDATION_NO_INSERT_PERMISSION, $this->getFormattedPropertyNameWithClassName($propertyName));
						KalturaLog::err($e->getMessage());
						$this->$propertyName = null;
						header($this->getDeclaringClassName($propertyName).'-'.$propertyName.' error: '.$e->getMessage());
					}
				}
			}
		}
	}
	
	public function validateForUpdate($sourceObject, $propertiesToSkip = array())
	{
		$updatableProperties = array();
		$reflector = KalturaTypeReflectorCacher::get(get_class($this));
		$properties = $reflector->getProperties();
		
		if ($reflector->requiresUpdatePermission()&& !kPermissionManager::getUpdatePermitted(get_class($this), kApiParameterPermissionItem::ALL_VALUES_IDENTIFIER)) {
			throw new KalturaAPIException(KalturaErrors::PROPERTY_VALIDATION_NO_UPDATE_PERMISSION, get_class($this));
		}
		
		foreach($properties as $property)
		{
			$propertyName = $property->getName();
			
			if (in_array($propertyName, $propertiesToSkip)) 
				continue;
			
			if ($this->$propertyName !== null)
			{
				// check if property value is being changed - if not, just continue to the next
				$objectPropertyName = $this->getObjectPropertyName($propertyName);
				$getter_callback = array ( $sourceObject ,"get{$objectPropertyName}"  );
				if (is_callable($getter_callback))
            	{
                	$value = call_user_func($getter_callback);
                	if ($value === $this->$propertyName ||
                		// since propel instansiates database boolean values as integer
                		// a casting shoud be done for values arriving as bool from the api  
                		(is_bool($this->$propertyName) && $value === (int)$this->$propertyName)) {
                		continue;
                	}
            	}
				
				if ($property->isReadOnly() || $property->isInsertOnly())
				{
					throw new KalturaAPIException(KalturaErrors::PROPERTY_VALIDATION_NOT_UPDATABLE, $this->getFormattedPropertyNameWithClassName($propertyName));
				}
				// property requires update permissions, verify that the current user has it
				if ($property->requiresUpdatePermission())
				{				
					if (!kPermissionManager::getUpdatePermitted($this->getDeclaringClassName($propertyName), $propertyName)) {
						//throw new KalturaAPIException(KalturaErrors::PROPERTY_VALIDATION_NO_UPDATE_PERMISSION, $this->getFormattedPropertyNameWithClassName($propertyName));
						//TODO: not throwing exception to not break clients that sends -1 as null for integer values (etc...)
						KalturaLog::err('Current user has not update permission for property ' . $this->getFormattedPropertyNameWithClassName($propertyName));
						$e = new KalturaAPIException(KalturaErrors::PROPERTY_VALIDATION_NO_UPDATE_PERMISSION, $this->getFormattedPropertyNameWithClassName($propertyName));
						$this->$propertyName = null;
						header($this->getDeclaringClassName($propertyName).'-'.$propertyName.' error: '.$e->getMessage());
					}
				}
			}
		}
		
		return $updatableProperties;
	}
	
	public function validateForUsage($sourceObject, $propertiesToSkip = array())
	{
		$useableProperties = array();
		$reflector = KalturaTypeReflectorCacher::get(get_class($this));
		if(!$reflector)
		{
			KalturaLog::err("Unable to validate usage for attribute object type [" . get_class($this) . "], type reflector not found");
			throw new KalturaAPIException(KalturaErrors::PROPERTY_VALIDATION_NO_USAGE_PERMISSION, get_class($this));
		}
			
		$properties = $reflector->getProperties();
		
		if ($reflector->requiresUsagePermission() && !kPermissionManager::getUsagePermitted(get_class($this), kApiParameterPermissionItem::ALL_VALUES_IDENTIFIER)) {
			throw new KalturaAPIException(KalturaErrors::PROPERTY_VALIDATION_NO_USAGE_PERMISSION, get_class($this));
		}
		
		foreach($properties as $property)
		{
			/* @var $property KalturaPropertyInfo */
			$propertyName = $property->getName();
			
			if (in_array($propertyName, $propertiesToSkip)) 
				continue;
			
			if ($this->$propertyName !== null)
			{
				// check if property value is being changed - if not, just continue to the next
				$objectPropertyName = $this->getObjectPropertyName($propertyName);
				$getter_callback = array ( $sourceObject ,"get{$objectPropertyName}"  );
				if (is_callable($getter_callback))
            	{
                	$value = call_user_func($getter_callback);
                	if ($value === $this->$propertyName ||
                		// since propel instansiates database boolean values as integer
                		// a casting shoud be done for values arriving as bool from the api  
                		(is_bool($this->$propertyName) && $value === (int)$this->$propertyName)) {
                		continue;
                	}
            	}
				
				// property requires update permissions, verify that the current user has it
				if ($property->requiresUsagePermission())
				{				
					if (!kPermissionManager::getUsagePermitted($this->getDeclaringClassName($propertyName), $propertyName)) {
						//throw new KalturaAPIException(KalturaErrors::PROPERTY_VALIDATION_NO_UPDATE_PERMISSION, $this->getFormattedPropertyNameWithClassName($propertyName));
						//TODO: not throwing exception to not break clients that sends -1 as null for integer values (etc...)
						$e = new KalturaAPIException(KalturaErrors::PROPERTY_VALIDATION_NO_USAGE_PERMISSION, $this->getFormattedPropertyNameWithClassName($propertyName));
						$this->$propertyName = null;
						KalturaLog::err($this->getDeclaringClassName($propertyName).'-'.$propertyName.' error: '.$e->getMessage());
						header($this->getDeclaringClassName($propertyName).'-'.$propertyName.' error: '.$e->getMessage());
					}
				}
			}
		}
		
		return $useableProperties;
	}
	
	
	private function getObjectPropertyName($propertyName)
	{
		$objectPropertyName = null;
		$mapBetweenObjects = $this->getMapBetweenObjects();
		if (array_key_exists($propertyName, $mapBetweenObjects)) {
			$objectPropertyName = $mapBetweenObjects[$propertyName];
		}
		else if (in_array($propertyName, $mapBetweenObjects)) {
			$objectPropertyName = $propertyName;
		}
		return $objectPropertyName;
	}
	
	public function trimStringProperties(array $propertyNames)
	{
	    foreach ($propertyNames as $propertyName)
	    {
	        if (!$this->isNull($propertyName))
	        {
	            $this->$propertyName = trim($this->$propertyName);
	        }
	    }
	}
}
