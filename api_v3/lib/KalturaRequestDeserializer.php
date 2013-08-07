<?php
/**
 * @package api
 * @subpackage v3
 */
class KalturaRequestDeserializer
{
	private $params = null;
	private $paramsGrouped = array();
	private $objects = array();
	private $extraParams = array("format", "ks", "fullObjects");

	const PREFIX = ":";

	public function KalturaRequestDeserializer($params)
	{
		$this->params = $params;
		$this->groupParams();
	}

	public function groupParams()
	{
		// group the params by prefix
		foreach($this->params as $key => $value)
		{
			$path = explode(self::PREFIX, $key);
			$this->setElementByPath($this->paramsGrouped, $path, $value);
		}
	}
	
	private function setElementByPath(&$array, $path, $value)
	{
		$tmpArray = &$array;
		while(($key = array_shift($path)) !== null)
		{
			if ($key == '-' && count($path) == 0)
				break;
			
			if (!isset($tmpArray[$key]) || !is_array($tmpArray[$key]))
				$tmpArray[$key] = array();
				
			if (count($path) == 0)
				$tmpArray[$key] = $value;
			else
				$tmpArray = &$tmpArray[$key];	
		}
		
		$array = &$tmpArray;
	}
	
	function set_element(&$path, $data) {
	    return ($key = array_pop($path)) ? $this->set_element($path, array($key=>$data)) : $data;
	}

	public function buildActionArguments(&$actionParams)
	{
		$serviceArguments = array();
		foreach($actionParams as &$actionParam)
		{
			$type = $actionParam->getType();
			$name = $actionParam->getName();
			
			if ($actionParam->isSimpleType($type))
			{
				if (array_key_exists($name, $this->paramsGrouped))
				{
					$value = $this->castSimpleType($type, $this->paramsGrouped[$name]);
					if(!kXml::isXMLValidContent($value))
						throw new KalturaAPIException(KalturaErrors::INVALID_PARAMETER_CHAR, $name);
						
					$serviceArguments[] = $value;
					continue;
				}
				
				if ($actionParam->isOptional())
				{
					$serviceArguments[] = $this->castSimpleType($type, $actionParam->getDefaultValue());
					continue;
				}
			}
			
			if ($actionParam->isFile()) // File
			{
				if (array_key_exists($name, $this->paramsGrouped)) 
				{
					$fileData = $this->paramsGrouped[$name];
					self::validateFile($fileData);
					$serviceArguments[] = $fileData;
					continue;
				}
				
				if ($actionParam->isOptional()) 
				{
					$serviceArguments[] = null;
					continue;
				} 	
			}
			
			if ($actionParam->isEnum()) // enum
			{
				if (array_key_exists($name, $this->paramsGrouped))
				{
					$enumValue = $this->paramsGrouped[$name];
					if(strtolower($enumValue) == 'true')
						$enumValue = 1;
					if(strtolower($enumValue) == 'false')
						$enumValue = 0;
						
					if (!$actionParam->getTypeReflector()->checkEnumValue($enumValue))
						throw new KalturaAPIException(KalturaErrors::INVALID_ENUM_VALUE, $enumValue, $name, $actionParam->getType());
					
					if($type == 'KalturaNullableBoolean')
					{
						$serviceArguments[] = KalturaNullableBoolean::toBoolean($enumValue);
						continue;
					}
					
					$serviceArguments[] = $this->castSimpleType("int", $enumValue);
					continue;
				}
				
				if ($actionParam->isOptional())
				{
					$serviceArguments[] = $this->castSimpleType("int", $actionParam->getDefaultValue());
					continue;
				}
			}
			
			if ($actionParam->isStringEnum()) // string enum or dynamic
			{
				if (array_key_exists($name, $this->paramsGrouped))
				{
					$enumValue = $this->paramsGrouped[$name];
					if (!$actionParam->getTypeReflector()->checkStringEnumValue($enumValue))
						throw new KalturaAPIException(KalturaErrors::INVALID_ENUM_VALUE, $enumValue, $name, $actionParam->getType());
					
					$serviceArguments[] = $enumValue;
					continue;
				}
				
				if ($actionParam->isOptional())
				{
					$serviceArguments[] = $actionParam->getDefaultValue();
					continue;
				}
			}
			
			if ($actionParam->isArray()) // array
			{
				$arrayObj = new $type();
				if (isset($this->paramsGrouped[$name]) && is_array($this->paramsGrouped[$name]))
				{
					ksort($this->paramsGrouped[$name]);
					foreach($this->paramsGrouped[$name] as $arrayItemParams)
					{
						$arrayObj[] = $this->buildObject($actionParam->getArrayTypeReflector(), $arrayItemParams, $name);
					}
				}
				$serviceArguments[] = $arrayObj;
				continue;
			}
			
			if (isset($this->paramsGrouped[$name])) // object 
			{
				$serviceArguments[] = $this->buildObject($actionParam->getTypeReflector(), $this->paramsGrouped[$name], $name);
				continue;
			}
			
			if ($actionParam->isOptional()) // object that is optional
			{
				$serviceArguments[] = null;
				continue;
			}

			throw new KalturaAPIException(KalturaErrors::MISSING_MANDATORY_PARAMETER, $name);
		}
		return $serviceArguments;
	}
	
	private function validateFile($fileData) 
	{
		if (!isset($fileData['tmp_name']) || !is_uploaded_file($fileData['tmp_name'])) {
			$msg = "An error occured while uploading file.";
			KalturaLog::log($msg . ' ' . print_r($fileData, true));
			throw new KalturaAPIException(KalturaErrors::UPLOAD_ERROR);
		}
	}

	private function buildObject(KalturaTypeReflector $typeReflector, array &$params, $objectName)
	{
		// if objectType was specified, we will use it only if the anotation type is it's base type
		if (array_key_exists("objectType", $params))
		{
            $possibleType = $params["objectType"];
            if (strtolower($possibleType) !== strtolower($typeReflector->getType())) // reflect only if type is different
            {
                if ($typeReflector->isParentOf($possibleType)) // we know that the objectType that came from the user is right, and we can use it to initiate the object\
                {
                    $newTypeReflector = KalturaTypeReflectorCacher::get($possibleType);
                    if($newTypeReflector)
                    	$typeReflector = $newTypeReflector;
                }
            }
		}
		
		if($typeReflector->isAbstract())
			throw new KalturaAPIException(KalturaErrors::OBJECT_TYPE_ABSTRACT, $typeReflector->getType());
		 
	    $class = $typeReflector->getType();
		$obj = new $class;
		$properties = $typeReflector->getProperties();
		
		foreach($params as $name => $value)
		{
			$isNull = false;
			if (kString::endsWith($name, '__null'))
			{
				$name = str_replace('__null', '', $name);
				$isNull = true;
			}
			
			if (!array_key_exists($name, $properties))
			{
				continue;
			}
			
			$property = $properties[$name];
			/* @var $property KalturaPropertyInfo */
			$type = $property->getType();
			
			if ($isNull && !$property->isArray())
			{
				$obj->$name = new KalturaNullField();
				continue;
			}
							
			if ($property->isSimpleType())
			{
				$value = $this->castSimpleType($type, $value);
				if(!kXml::isXMLValidContent($value))
					throw new KalturaAPIException(KalturaErrors::INVALID_PARAMETER_CHAR, $name);
				$obj->$name = $value;
				continue;
			}
			
			if ($property->isEnum())
			{
				if (!$property->getTypeReflector()->checkEnumValue($value))
					throw new KalturaAPIException(KalturaErrors::INVALID_ENUM_VALUE, $value, $name, $property->getType());
			
				if($type == 'KalturaNullableBoolean')
				{
					$obj->$name = KalturaNullableBoolean::toBoolean($value);
					continue;
				}
				
				$obj->$name = $this->castSimpleType("int", $value);
				continue;
			}
			
			if ($property->isStringEnum())
			{
				if (!$property->getTypeReflector()->checkStringEnumValue($value))
					throw new KalturaAPIException(KalturaErrors::INVALID_ENUM_VALUE, $value, $name, $property->getType());
					
				$value = $this->castSimpleType("string", $value);
				if(!kXml::isXMLValidContent($value))
					throw new KalturaAPIException(KalturaErrors::INVALID_PARAMETER_CHAR, $name);
				$obj->$name = $value;
				continue;
			}
			
			if ($property->isArray() && is_array($value))
			{
				ksort($value);
				$arrayObj = new $type();
				foreach($value as $arrayItemParams)
				{
					$arrayObj[] = $this->buildObject($property->getArrayTypeReflector(), $arrayItemParams, "{$objectName}:$name");
				}
				$obj->$name = $arrayObj;
				continue;
			}
			
			if ($property->isComplexType() && is_array($value))
			{
				$obj->$name = $this->buildObject($property->getTypeReflector(), $value, "{$objectName}:$name");
				continue;
			}
			
			if ($property->isFile())
			{
				$obj->$name = $value;
				continue;
			}
		}
		return $obj;
	}
	
	private function castSimpleType($type, $var)
	{
		switch($type)
		{
			case "int":
				return (int)$var;
			case "string":
				return kString::stripUtf8InvalidChars((string)$var);
			case "bool":
				if (strtolower($var) === "false")
					return false;
				else
					return (bool)$var;
			case "float":
				return (float)$var;
		}
		
		return null;
	}
}
