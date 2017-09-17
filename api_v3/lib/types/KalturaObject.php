<?php 
/**
 * @package api
 * @subpackage objects
 */
abstract class KalturaObject implements IApiObject
{
	/**
	 * @var KalturaListResponseArray
	 * @readonly
	 */
	public $relatedObjects;
	
	static protected $sourceFilesCache = array();
	static protected $classPrivatesCache = array();
	
	function __sleep()
	{
	    $allVars = get_object_vars($this);
	    $return = array();
	    foreach(array_keys($allVars) as $name)
	    {
	        if (isset($this->$name))
	        {
	            $return[] = $name;
	        }
	    }
	    return $return;
	}
	
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
		
	static protected function getFunctionBody($func)
	{
		$filename = $func->getFileName();
		$startLine = $func->getStartLine() - 1;
		$endLine = $func->getEndLine();
		$length = $endLine - $startLine;
	
		if (!isset(self::$sourceFilesCache[$filename]))
			self::$sourceFilesCache[$filename] = file($filename);
		
		$body = implode("", array_slice(self::$sourceFilesCache[$filename], $startLine, $length));
		return $body;
	}
	
	static protected function getClassPrivates($className)
	{
		if (isset(self::$classPrivatesCache[$className]))
			return self::$classPrivatesCache[$className];
		
		$refClass = new ReflectionClass($className);
		$result = array();
		
		// properties
		$privateProps = $refClass->getProperties(ReflectionProperty::IS_PRIVATE);
		foreach ($privateProps as $privateProp)
		{
			$result[] = strtolower($privateProp->name);
		}
		
		// methods
		$privateMethods = $refClass->getMethods(ReflectionMethod::IS_PRIVATE);
		foreach ($privateMethods as $privateMethod)
		{
			$result[] = strtolower($privateMethod->name);
		}
		
		self::$classPrivatesCache[$className] = $result;
		
		return $result;
	}
	
	protected function generateFromObjectClass($srcObj, $fromObjectClass)
	{
		// initialize reflection data
		$srcObjClass = get_class($srcObj);
		$srcObjRef = new ReflectionClass($srcObj);
		$thisClass = get_class($this);
		$thisRef = KalturaTypeReflectorCacher::get($thisClass);
		if(!$thisRef)
			return false;
		$thisProps = $thisRef->getProperties();
	
		// generate file header
		$result = "<?php\nclass {$fromObjectClass} extends {$srcObjClass}\n{\n\tstatic function fromObject(\$apiObj, \$srcObj, KalturaDetachedResponseProfile \$responseProfile = null)\n\t{\n";
	
		if ($thisRef->requiresReadPermission())
		{
			$result .= "\t\tif (!kPermissionManager::getReadPermitted('{$thisClass}', kApiParameterPermissionItem::ALL_VALUES_IDENTIFIER))\n";
			$result .= "\t\t\treturn;\n";
		}
	
		// generate properties copy code
		$mappingFuncCode = array();
		$usesCustomData = false;
	
		foreach ( $this->getMapBetweenObjects() as $apiPropName => $dbPropName )
		{
			if (is_numeric($apiPropName))
				$apiPropName = $dbPropName;
				
			if(!isset($thisProps[$apiPropName]))
			{
				KalturaLog::alert("property {$apiPropName} defined in map, does not exist on object {$thisClass}");
				continue;
			}
			
			if ($thisProps[$apiPropName]->isWriteOnly())
				continue;
	
			// get getter function body
			$getterName = "get{$dbPropName}";
			if (!is_callable(array($srcObj, $getterName)))
			{
				KalturaLog::alert("getter for property {$dbPropName} was not found on object {$srcObjClass}");
				continue;
			}
			
			$curGetter = $srcObjRef->getMethod($getterName);
			$getterFunc = self::getFunctionBody($curGetter);
			$startBracePos = strpos($getterFunc, '{');
			$endBracePos = strrpos($getterFunc, '}');
	
			$getterBody = trim(substr($getterFunc, $startBracePos + 1, $endBracePos - $startBracePos - 1));
	
			// calculate field value
			$fieldValue = null;
			$getterParameters = '';
	
			if (strrpos($getterBody, 'return ') === 0)
			{
				// simple getter
				$fieldValue = trim(rtrim(trim(substr($getterBody, 7)), ';'));
	
				$matches = array();
				$matchCount = preg_match_all('/\$this\->getFromCustomData\s*\(\s*([\'"]?[\w:_]+[\'"]?)\s*\)/', $fieldValue, $matches);
				for ($curIndex = 0; $curIndex < $matchCount; $curIndex++)
				{				
					$customDataKey = $matches[1][$curIndex];
					$customDataValue = "(isset(\$customData[{$customDataKey}]) ? \$customData[{$customDataKey}] : null)";
					$fieldValue = str_replace($matches[0][$curIndex], $customDataValue, $fieldValue);
					$usesCustomData = true;
				}
	
				$fieldValue = str_replace('$this->', '$srcObj->', $fieldValue);
				$fieldValue = str_replace('self::', "{$curGetter->class}::", $fieldValue);
				
				// check whether we are going to access any private properties
				$matches = array();
				$matchCount = preg_match_all('/\$srcObj\->([\w_]+)/', $fieldValue, $matches);
				$privates = self::getClassPrivates($curGetter->class);
				foreach ($matches[1] as $curProperty)
				{
					if (in_array(strtolower($curProperty), $privates))
					{
						KalturaLog::log("{$curGetter->class}::{$curGetter->name} uses private property/method {$curProperty}");
						$fieldValue = null;		// we have to use the getter since it uses a private property
					}
				}
				
				// check for use of parent::
				if (strpos($fieldValue, 'parent::') !== false)
				{
					KalturaLog::log("{$curGetter->class}::{$curGetter->name} uses parent");
					$fieldValue = null;		// we have to use the getter since it uses a private property
				}
			}
			else
			{
				$params = $curGetter->getParameters();
				if (count($params) == 1 && $params[0]->getDefaultValue() == "Y-m-d H:i:s")
				{
					if (strpos($curGetter->class, 'Base') === 0)
					{
						// date field getter
						$matches = array();
						if (preg_match('/^if \(\$this\->([\w_]+) === null\)/', $getterBody, $matches))
						{
							$memberName = '$srcObj->' . $matches[1];
							$fieldValue = "({$memberName} === null ? null : (int) date_create({$memberName})->format('U'))";
						}
					}
					else
					{
						$getterParameters = 'null';
					}
				}
			}
			
			if (!$fieldValue)
			{
				// complex getter - call original function
				$fieldValue = "\$srcObj->".$curGetter->name."($getterParameters)";
			}
	
			// add support for arrays and dynamic enums
			$curCode = '';
	
			if ($thisProps[$apiPropName]->isArray())
			{
				$arrayClass = $thisProps[$apiPropName]->getType();
				if(method_exists($arrayClass, 'fromDbArray'))
				{
					$curCode = "\$value = {$fieldValue};\n\t\t" .
					"if(is_array(\$value))\n\t\t" .
					"{\n\t\t\t" .
					"\$value = {$arrayClass}::fromDbArray(\$value);\n\t\t" .
					"}\n\t\t";
					$fieldValue = '$value';
				}
			}
			else if ($thisProps[$apiPropName]->isDynamicEnum())
			{
				$propertyType = $thisProps[$apiPropName]->getType();
				$enumClass = call_user_func(array($propertyType, 'getEnumClass'));
				if ($enumClass)
				{
					$fieldValue = "kPluginableEnumsManager::coreToApi('{$enumClass}', {$fieldValue})";
				}
			}
			
			else if ($thisProps[$apiPropName]->getDynamicType())
			{
				$propertyType = $thisProps[$apiPropName]->getDynamicType();
				$enumClass = call_user_func(array($propertyType, 'getEnumClass'));
				if ($enumClass)
				{
					$curCode = "\$value = {$fieldValue};\n\t\t" . 
						"if(!is_null(\$value))\n\t\t" . 
							"{\n\t\t\t" . 
								"\$values = explode(',', \$value);\n\t\t\t" . 
								"\$finalValues = array();\n\t\t\t" . 
								"foreach(\$values as \$val)\n\t\t\t\t" .
									"\$finalValues[] = kPluginableEnumsManager::coreToApi('{$enumClass}', \$val);\n\t\t\t" .
								"\$value = implode(',', \$finalValues);\n\t\t" .
							"}\n\t\t";
					$fieldValue = '$value';
				}
			}
			
			else if (	$thisProps[$apiPropName]->isComplexType() 
						&& !$thisProps[$apiPropName]->isEnum() 
						&& !$thisProps[$apiPropName]->isStringEnum() 
						&& !$thisProps[$apiPropName]->isAbstract())
			{
				$propertyType = $thisProps[$apiPropName]->getType();
				$getter = $curGetter->name;
				$curCode = "\$value = null;\n\t\tif(\$srcObj->$getter()){\n\t\t\t\$value = new $propertyType();\n\t\t\t\$value->fromObject(\$srcObj->$getter());\n\t\t}\n\t\t"; 
				$fieldValue = '$value';
			}
			
	
			// add field copy code
			$curCode .= "\$apiObj->{$apiPropName} = {$fieldValue};";
	
			if ($thisProps[$apiPropName]->requiresReadPermission())
			{
				$declaringClass = $this->getDeclaringClassName($apiPropName);
				$curCode = "if (kPermissionManager::getReadPermitted('{$declaringClass}', '{$apiPropName}'))\n\t\t{\n\t\t\t" . implode("\n\t\t\t", explode("\n", $curCode)) . "\n\t\t}";
			}
	
			$curCode = "if (isset(\$get['{$apiPropName}']))\n\t\t{\n\t\t\t" . implode("\n\t\t\t", explode("\n", $curCode)) . "\n\t\t}";
			$mappingFuncCode[$apiPropName] = $curCode;
		}
	
		ksort($mappingFuncCode);
	
		// generate final code
		if ($usesCustomData)
		{
			$result .= "\t\t\$customData = unserialize(\$srcObj->custom_data);\n";
		}
	
		$result .= "\t\t\$get = array(\n\t\t\t'" . implode("' => true,\n\t\t\t'", array_keys($mappingFuncCode)) . "' => true\n\t\t);";
		$result .= '
		if($responseProfile){
			$fieldsArray = array_flip(array_map("trim", explode(",", $responseProfile->fields)));
			if($responseProfile->type == ResponseProfileType::INCLUDE_FIELDS){
				$get = array_intersect_key($get, $fieldsArray);
			}
			else{
				$get = array_diff_key($get, $fieldsArray);
			}
		}
		';
		
		$result .= implode("\n\t\t", $mappingFuncCode) . "\n\t}\n}";
		
		return $result;
	}
	
	public function shouldGet($propertyName, KalturaDetachedResponseProfile $responseProfile = null)
	{
		if($responseProfile)
		{
			$fields = array_flip(array_map("trim", explode(",", $responseProfile->fields)));
			if($responseProfile->type == ResponseProfileType::INCLUDE_FIELDS)
			{
				return isset($fields[$propertyName]);
			}
			else
			{
				return !isset($fields[$propertyName]);
			}
		}
		
		return true;
	}
	
	protected function doFromObject($srcObj, KalturaDetachedResponseProfile $responseProfile = null)
	{
		
	}
	
	final public function fromObject($srcObj, KalturaDetachedResponseProfile $responseProfile = null)
	{
		if (!is_object($srcObj))
		{
			KalturaLog::err("expected an object, got " . print_r($srcObj, true));
			return;
		}
	
		if($srcObj instanceof IRelatedObject && $responseProfile && $responseProfile->relatedProfiles)
		{
			if(KalturaResponseProfileCacher::start($this, $srcObj, $responseProfile))
			{
				return;
			}
		}
		
		$thisClass = get_class($this);
		$srcObjClass = get_class($srcObj);
		$fromObjectClass = "Map_{$thisClass}_{$srcObjClass}";
		if (!class_exists($fromObjectClass))
		{
			$cacheFileName = kConf::get("cache_root_path") . "/api_v3/fromObject/{$fromObjectClass}.php";
			$max_include_retries=10;
			$fromObjectClassCode=null;
			while((!@include_once($cacheFileName)) and $max_include_retries--)
			{
				if(!$fromObjectClassCode)
				{
					$fromObjectClassCode = $this->generateFromObjectClass($srcObj, $fromObjectClass);
					if (!$fromObjectClassCode)
						return;
				}

				$cacheDir = dirname($cacheFileName);
				if (!is_dir($cacheDir))
				{
					mkdir($cacheDir);
					chmod($cacheDir, 0775);
				}
				kFile::safeFilePutContents($cacheFileName, $fromObjectClassCode,0644);
			}
			if (!class_exists($fromObjectClass))
			{
				throw new Exception("Could not include cached code file - {$cacheFileName}");
			}
		}
	
		$fromObjectClass::fromObject($this, $srcObj, $responseProfile);
		$this->doFromObject($srcObj, $responseProfile);
		
		if($srcObj instanceof IRelatedObject)
		{
			KalturaResponseProfileCacher::onPersistentObjectLoaded($srcObj);
			if($responseProfile && $responseProfile->relatedProfiles)
			{
				$responseProfile->validateNestedObjects();
				$this->loadRelatedObjects($responseProfile);
				KalturaResponseProfileCacher::stop($srcObj, $this);
			}
		}
	}
	
	public function loadRelatedObjects(KalturaDetachedResponseProfile $responseProfile)
	{	
		$this->relatedObjects = new KalturaListResponseArray();
		foreach($responseProfile->relatedProfiles as $relatedProfile)
		{
			/* @var $relatedProfile KalturaDetachedResponseProfile */
			if(!$relatedProfile->filter)
			{
				KalturaLog::notice("Related response-profile [$relatedProfile->name] has no filter and should not be used as nested profile");
				continue;
			}

			$filter = clone $relatedProfile->filter;
			/* @var $filter KalturaRelatedFilter */
			
			if($relatedProfile->mappings)
			{
				$applied = true;
				foreach($relatedProfile->mappings as $mapping)
				{
					/* @var $mapping KalturaResponseProfileMapping */
					if(!$mapping->apply($filter, $this))
					{
						$applied = false;
						break;
					}
				}
				if(!$applied)
				{
					KalturaLog::warning("Mappings could not be applied for response-profile [$relatedProfile->name]");
					continue;
				}
			}
			
			$pager = $relatedProfile->pager;
			if(!$pager)
			{
				$pager = new KalturaFilterPager();
			}
			
			$this->relatedObjects[$relatedProfile->name] = $filter->getListResponse($pager, $relatedProfile);
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
				
			if ($props_to_skip && is_array($props_to_skip) && in_array($this_prop, $props_to_skip)) 
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
			elseif ($propertyInfo->getDynamicType()&& strlen($value))
			{
				$propertyType = $propertyInfo->getDynamicType();
				$enumType = call_user_func(array($propertyType, 'getEnumClass'));
				
				$values = explode(',', $value);
				$finalValues = array();
				foreach($values as $val)
					$finalValues[] = kPluginableEnumsManager::apiToCore($enumType, $val);
				$value = implode(',', $finalValues);
			}
			elseif (is_string($value))
			{
				if (! kXml::isXMLValidContent($value))
					throw new KalturaAPIException ( KalturaErrors::INVALID_PARAMETER_CHAR, $this_prop );
				else if($this->shouldPurify())
					$value = kHtmlPurifier::purify(get_class($object_to_fill), $object_prop, $value);
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
		$this->enablePurify();
		$retObj = $this->toObject($object_to_fill, $props_to_skip);
		$this->disablePurify();
		return $retObj;
	}
	
	public function toInsertableObject ( $object_to_fill = null , $props_to_skip = array() )
	{
		$this->validateForInsert($props_to_skip); // will check that not insertable properties are not set 

		$this->enablePurify();
		$retObj = $this->toObject($object_to_fill, $props_to_skip);
		$this->disablePurify();
		return $retObj;
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
		$className = get_class($this);
		$reflector = KalturaTypeReflectorCacher::get($className);
		$properties = $reflector->getProperties();
		
		if ($reflector->requiresInsertPermission()&& !kPermissionManager::getInsertPermitted($className, kApiParameterPermissionItem::ALL_VALUES_IDENTIFIER)) {
			throw new KalturaAPIException(KalturaErrors::PROPERTY_VALIDATION_NO_INSERT_PERMISSION, $className);
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
						header($this->getDeclaringClassName($propertyName).'-'.$propertyName.'-error: '.$e->getMessage());
					}
				}

			}
		}
	}
	
	public function validateForUpdate($sourceObject, $propertiesToSkip = array())
	{
		$updatableProperties = array();
		$className = get_class($this);
		$reflector = KalturaTypeReflectorCacher::get($className);
		$properties = $reflector->getProperties();
		
		if ($reflector->requiresUpdatePermission()&& !kPermissionManager::getUpdatePermitted($className, kApiParameterPermissionItem::ALL_VALUES_IDENTIFIER)) {
			throw new KalturaAPIException(KalturaErrors::PROPERTY_VALIDATION_NO_UPDATE_PERMISSION, $className);
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
			
			if ($propertiesToSkip && is_array($propertiesToSkip) && in_array($propertyName, $propertiesToSkip)) 
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
	
	
	protected function getObjectPropertyName($propertyName)
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

	public function cast($className) 
	{
            if(!is_subclass_of($className, get_class($this)) && !is_subclass_of($this,$className))
                throw new KalturaAPIException(KalturaErrors::INVALID_OBJECT_TYPE, get_class($this));
			
	    return unserialize(sprintf(
	        'O:%d:"%s"%s',
	        strlen($className),
	        $className,
	        strstr(strstr(serialize($this), '"'), ':')
	    ));
	}

	/**
	 * @return bool
	 */
	protected function shouldPurify()
	{
		return isset($this->purifyHtml) && $this->purifyHtml;
	}

	protected function disablePurify()
	{
		$this->purifyHtml = false;
		unset($this->purifyHtml);
	}

	protected function enablePurify()
	{
		if (!isset(kCurrentContext::$HTMLPurifierBehaviour) || kCurrentContext::$HTMLPurifierBehaviour == HTMLPurifierBehaviourType::IGNORE)
			$this->purifyHtml = false;
		else
			$this->purifyHtml = true;
	}
}
