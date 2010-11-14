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
		

	public function fromObject ( $source_object  )
	{
		$reflector = KalturaTypeReflectorCacher::get(get_class($this));
		$properties = $reflector->getProperties();
		
		foreach ( $this->getMapBetweenObjects() as $this_prop => $object_prop )
		{
			if ( is_numeric( $this_prop) ) 
			    $this_prop = $object_prop;
			    
			if(!isset($properties[$this_prop]) || $properties[$this_prop]->isWriteOnly())
				continue;
				
            $getter_callback = array ( $source_object ,"get{$object_prop}"  );
            if (is_callable($getter_callback))
            {
                $value = call_user_func($getter_callback);
                if($properties[$this_prop]->isDynamicEnum())
                	$value = $this->fromDynamicEnumValue($properties[$this_prop]->getType(), $value);
                	
                $this->$this_prop = $value;
            }
            else
            { 
            	KalturaLog::alert("getter for property [$object_prop] was not found on object class [" . get_class($source_object) . "]");
            }
                
            if (in_array($this_prop, array("createdAt", "updatedAt")))
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
	
	protected function fromDynamicEnumValue($type, $value)
	{
		// TODO remove call_user_func after moving to php 5.3
		$baseEnumName = call_user_func("$type::getEnumClass");
//		$baseEnumName = $type::getEnumClass();
	
		$pluginInstances = KalturaPluginManager::getPluginInstances('IKalturaEnumerator');
		foreach($pluginInstances as $pluginInstance)
		{
			$enums = $pluginInstance->getEnums($baseEnumName);
			foreach($enums as $enum)
			{
				// TODO remove call_user_func after moving to php 5.3
				$enumValue = call_user_func("$enum::get")->coreToApi($value);
//				$enumValue = $enum::get()->coreToApi($value);
				if(!is_null($enumValue))
					return $enumValue;
			}
		}
			
		return null;
	}
	
	protected function toDynamicEnumValue($type, $value)
	{
		$split = explode(KalturaPluginEnum::PLUGIN_VALUE_DELIMITER, $value, 2);
		if(count($split) == 1)
			return $value;
			
		list($pluginName, $valueName) = $split;
		
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
			{
				// TODO remove call_user_func after moving to php 5.3
				return call_user_func("$enum::get")->coreValue($value);
//				return $enum::get()->coreValue($value);
			}
		}
		
		return null;
	}
	
	public function toObject ( $object_to_fill = null , $props_to_skip = array() )
	{
		// enables extension with default empty object
		if(is_null($object_to_fill))
			return null;
			
		$typeReflector = KalturaTypeReflectorCacher::get(get_class($this));
		
		foreach ( $this->getMapBetweenObjects() as $this_prop => $object_prop )
		{
		 	if ( is_numeric( $this_prop) ) $this_prop = $object_prop;
			if (in_array($this_prop, $props_to_skip)) continue;
			
			$value = $this->$this_prop;
			$propertyInfo = $typeReflector->getProperty($this_prop);
			if($propertyInfo->isDynamicEnum())
				$value = $this->toDynamicEnumValue($propertyInfo->getType(), $value);
			
			if ($value !== null)
			{
				$setter_callback = array ( $object_to_fill ,"set{$object_prop}");
				if (is_callable($setter_callback))
			 	    call_user_func_array( $setter_callback , array ($value ) );
		 	    else 
	            	KalturaLog::alert("setter for property [$object_prop] was not found on object class [" . get_class($object_to_fill) . "]");
			}
		}
		return $object_to_fill;		
	}
	
	public function toUpdatableObject ( $object_to_fill , $props_to_skip = array() )
	{
		$this->validateForUpdate(); // will check that not updatable properties are not set 
		
		return $this->toObject($object_to_fill, $props_to_skip);
	}
	
	public function toInsertableObject ( $object_to_fill = null , $props_to_skip = array() )
	{
		$this->validateForInsert(); // will check that not insertable properties are not set 
		
		return $this->toObject($object_to_fill, $props_to_skip);
	}
	
	public function validatePropertyNotNull($propertyName)
	{
		if (!property_exists($this, $propertyName) || $this->$propertyName === null)
		{
			throw new KalturaAPIException(KalturaErrors::PROPERTY_VALIDATION_CANNOT_BE_NULL, $this->getFormattedPropertyNameWithClassName($propertyName));
		}
	}
	
	public function validatePropertyMinLength($propertyName, $minLength)
	{
		$this->validatePropertyNotNull($propertyName);
		if (strlen($this->$propertyName) < $minLength)
			throw new KalturaAPIException(KalturaErrors::PROPERTY_VALIDATION_MIN_LENGTH, $this->getFormattedPropertyNameWithClassName($propertyName), $minLength);
	}
	
	public function validatePropertyMaxLength($propertyName, $maxLength)
	{
		$this->validatePropertyNotNull($propertyName);
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
	
	public function validateForInsert()
	{
		$reflector = KalturaTypeReflectorCacher::get(get_class($this));
		$properties = $reflector->getProperties();
		
		foreach($properties as $property)
		{
			$propertyName = $property->getName();
			if ($property->isReadOnly())
			{
				if ($this->$propertyName !== null)
					throw new KalturaAPIException(KalturaErrors::PROPERTY_VALIDATION_NOT_UPDATABLE, $this->getFormattedPropertyNameWithClassName($propertyName));
			}
		}
	}
	
	public function validateForUpdate()
	{
		$updatableProperties = array();
		$reflector = KalturaTypeReflectorCacher::get(get_class($this));
		$properties = $reflector->getProperties();
		
		foreach($properties as $property)
		{
			$propertyName = $property->getName();
			if ($property->isReadOnly() || $property->isInsertOnly())
			{
				if ($this->$propertyName !== null)
					throw new KalturaAPIException(KalturaErrors::PROPERTY_VALIDATION_NOT_UPDATABLE, $this->getFormattedPropertyNameWithClassName($propertyName));
			}
		}
		
		return $updatableProperties;
	}
}