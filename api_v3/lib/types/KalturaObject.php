<?php 
/**
 * @package api
 * @subpackage objects
 */
class KalturaObject 
{
	protected function getReadOnly ()
	{
		
	}
	
	// TODO - get the set of properties from the annotations
	protected function getPropertiresForField ( $field )
	{
		
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
		

	public function fromObject ( $source_object  )
	{
		$reflector = KalturaTypeReflectorCacher::get(get_class($this));
		if(!$reflector)
			return false;
			
		$properties = $reflector->getProperties();
		
		if ($reflector->requiresReadPermission() && !kPermissionManager::getReadPermitted(get_class($this), kApiParameterPermissionItem::ALL_VALUES_IDENTIFIER)) {
			return false; // current user has no permission for accessing this object class
		}
		
		foreach ( $this->getMapBetweenObjects() as $this_prop => $object_prop )
		{
			if ( is_numeric( $this_prop) ) 
			    $this_prop = $object_prop;
			    
			if(!isset($properties[$this_prop]) || $properties[$this_prop]->isWriteOnly())
				continue;
				
			// ignore property if it requires a read permission which the current user does not have
			if ($properties[$this_prop]->requiresReadPermission() && !kPermissionManager::getReadPermitted($this->getDeclaringClassName($this_prop), $this_prop))
				continue;
				
            $getter_callback = array ( $source_object ,"get{$object_prop}"  );
            if (is_callable($getter_callback))
            {
                $value = call_user_func($getter_callback);
                
                if($properties[$this_prop]->isArray() && is_array($value))
                {
                	$class = $properties[$this_prop]->getType();
                	if(method_exists($class, 'fromDbArray'))
	                	$value = call_user_func(array($class, 'fromDbArray'), $value);
                }
                elseif($properties[$this_prop]->isDynamicEnum())
                {
					$propertyType = $properties[$this_prop]->getType();
					$enumType = call_user_func(array($propertyType, 'getEnumClass'));
                	$value = kPluginableEnumsManager::coreToApi($enumType, $value);
                }
                	
                $this->$this_prop = $value;
            }
            else
            { 
            	KalturaLog::alert("getter for property [$object_prop] was not found on object class [" . get_class($source_object) . "]");
            }
                
            if (in_array($this_prop, array("createdAt", "updatedAt", "deletedAt")))
            {
                $this->$this_prop = call_user_func_array($getter_callback, array(null)); // when passing null to getCreatedAt, timestamp will be returned
            }
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
			if (in_array($this_prop, $props_to_skip)) 
				continue;
			
			$value = $this->$this_prop;
			if (is_null($value)) 
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
	
	public function validatePropertyNotNull($propertyName)
	{
		if (!property_exists($this, $propertyName) || $this->$propertyName === null)
		{
			throw new KalturaAPIException(KalturaErrors::PROPERTY_VALIDATION_CANNOT_BE_NULL, $this->getFormattedPropertyNameWithClassName($propertyName));
		}
	}
	
	public function validatePropertyMinLength($propertyName, $minLength, $allowNull = false)
	{
		if(!$allowNull)
			$this->validatePropertyNotNull($propertyName);
		elseif(is_null($this->$propertyName))
			return;
		
		if ($this->$propertyName instanceof KalturaNullField) 
			return;
		
		if (strlen($this->$propertyName) < $minLength)
			throw new KalturaAPIException(KalturaErrors::PROPERTY_VALIDATION_MIN_LENGTH, $this->getFormattedPropertyNameWithClassName($propertyName), $minLength);
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
	
	public function validatePropertyMinMaxLength($propertyName, $minLength, $maxLength)
	{
		$this->validatePropertyMinLength($propertyName, $minLength);
		$this->validatePropertyMaxLength($propertyName, $maxLength);
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
}
