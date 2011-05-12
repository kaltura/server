<?php
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
	    return ($key = array_pop($path)) ? set_element($path, array($key=>$data)) : $data;
	}

	public function buildActionArguments(&$actionParams)
	{
		$serviceArguments = array();
		foreach($actionParams as &$actionParam)
		{
			$found = false;
			$type = $actionParam->getType();
			$name = $actionParam->getName();
			
			if ($actionParam->isSimpleType($type))
			{
				if (array_key_exists($name, $this->paramsGrouped))
				{
					$serviceArguments[] = $this->castSimpleType($type, $this->paramsGrouped[$name]);
					$found = true;
				}
				else if ($actionParam->isOptional())
				{
					$serviceArguments[] = $this->castSimpleType($type, $actionParam->getDefaultValue());
					$found = true;
				}
			}
			else if ($actionParam->isFile())
			{
				if (isset($_FILES[$name])) // FIXME: KalturaRequestDeserializer doesn't depend on $_POST or $_GET, so its a not a good idea to access $_FILES here 
				{
					$serviceArguments[]	= $_FILES[$name];
					$found = true;
				}
				else if ($actionParam->isOptional()) 
				{
					$serviceArguments[] = null;
					$found = true; 	
				}
			}
			else
			{
				if ($actionParam->isEnum()) // enum
				{
					if (array_key_exists($name, $this->paramsGrouped))
					{
						$enumValue = $this->paramsGrouped[$name];
						if (!$actionParam->getTypeReflector()->checkEnumValue($enumValue))
							throw new KalturaAPIException(KalturaErrors::INVALID_ENUM_VALUE, $enumValue, $name, $actionParam->getType());
						
						$serviceArguments[] = $this->castSimpleType("int", $enumValue);
						$found = true;
					}
					else if ($actionParam->isOptional())
					{
						$serviceArguments[] = $this->castSimpleType("int", $actionParam->getDefaultValue());
						$found = true;
					}
				}
				else if ($actionParam->isStringEnum()) // string enum or dynamic
				{
					if (array_key_exists($name, $this->paramsGrouped))
					{
						$enumValue = $this->paramsGrouped[$name];
						if (!$actionParam->getTypeReflector()->checkStringEnumValue($enumValue))
							throw new KalturaAPIException(KalturaErrors::INVALID_ENUM_VALUE, $enumValue, $name, $actionParam->getType());
						
						$serviceArguments[] = $enumValue;
						$found = true;
					}
					else if ($actionParam->isOptional())
					{
						$serviceArguments[] = $actionParam->getDefaultValue();
						$found = true;
					}
				}
				else if ($actionParam->isArray()) // array
				{
					$arrayObj = new $type();
					if (isset($this->paramsGrouped[$name]) && is_array($this->paramsGrouped[$name]))
					{
						foreach($this->paramsGrouped[$name] as $arrayItemParams)
						{
							$arrayObj[] = $this->buildObject($actionParam->getArrayTypeReflector(), $arrayItemParams, $name);
						}
					}
					$serviceArguments[] = $arrayObj;
					$found = true;
				}
				else if (isset($this->paramsGrouped[$name])) // object 
				{
					$serviceArguments[] = $this->buildObject($actionParam->getTypeReflector(), $this->paramsGrouped[$name], $name);
					$found = true;
				}
				else if ($actionParam->isOptional()) // object that is optional
				{
					$serviceArguments[] = null;
					$found = true;
				}
			}

			if (!$found)
			{
				throw new KalturaAPIException(KalturaErrors::MISSING_MANDATORY_PARAMETER, $name);
			}
		}
		return $serviceArguments;
	}

	private function buildObject(KalturaTypeReflector $typeReflector, array &$params, $objectName)
	{
		// if objectType was specified, we will use it only if the anotation type is it's base type
		if (array_key_exists("objectType", $params))
		{
            $possibleType = $params["objectType"];
            if (strtolower($possibleType) !== strtolower($typeReflector->getType())) // reflect only if type is different
            {
                if ($typeReflector->isParentOf($possibleType)) // we know that the objectType that came from the user is right, and we can use it to initiate the object
                    $typeReflector = KalturaTypeReflectorCacher::get($possibleType);
            }
		}
		
	    $class = $typeReflector->getType();
		$obj = new $class;
		$properties = $typeReflector->getProperties();
		foreach($properties as $property)
		{
			$name = $property->getName();
			$type = $property->getType();
			
			if ($property->isSimpleType() || $property->isEnum() || $property->isStringEnum())
			{
				if (!array_key_exists($name, $params))
				{
					// missing parameters should be null or default propery value
					continue;
				}
				
				$value = $params[$name];
				if ($property->isSimpleType())
				{
					$obj->$name = $this->castSimpleType($type, $value);
				}
				else if ($property->isEnum())
				{
					if (!$property->getTypeReflector()->checkEnumValue($value))
						throw new KalturaAPIException(KalturaErrors::INVALID_ENUM_VALUE, $value, $name, $property->getType());
						
					$obj->$name = $this->castSimpleType("int", $value);
				}
				else if ($property->isStringEnum())
				{
					if (!$property->getTypeReflector()->checkStringEnumValue($value))
						throw new KalturaAPIException(KalturaErrors::INVALID_ENUM_VALUE, $value, $name, $property->getType());
						
					$obj->$name = $this->castSimpleType("string", $value);
				}
			}
			else if ($property->isArray())
			{
				if (isset($params[$name]) && is_array($params[$name]))
				{
					$arrayObj = new $type();
					foreach($params[$name] as $arrayItemParams)
					{
						$arrayObj[] = $this->buildObject($property->getArrayTypeReflector(), $arrayItemParams, "{$objectName}:$name");
					}
					$obj->$name = $arrayObj;
				}
			}
			else if ($property->isComplexType())
			{
				if (isset($params[$name]) && is_array($params[$name]))
				{
					$obj->$name = $this->buildObject($property->getTypeReflector(), $params[$name], "{$objectName}:$name");
				}
			}
			else if ($property->isFile())
			{
				if (isset($_FILES["{$objectName}:$name"])) 
				{
					$obj->$name = $_FILES["{$objectName}:$name"];
				}
			}
		}
		return $obj;
	}
	
	public function getKS()
	{
		return $this->castSimpleType("string", $this->paramsGrouped["ks"]);
	}
	
	public function getTargetPartnerId()
	{
		return $this->castSimpleType("int", $this->paramsGrouped["targetPartnerId"]);
	}
	
	public function getTargetUserId()
	{
		return $this->castSimpleType("string", $this->paramsGrouped["targetUserId"]);
	}
	
	private function castSimpleType($type, $var)
	{
		switch($type)
		{
			case "int":
				return (int)$var;
			case "string":
				return (string)$var;
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
