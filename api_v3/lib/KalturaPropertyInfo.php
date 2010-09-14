<?php
class KalturaPropertyInfo
{
	private $_type;
	private $_name;
	private $_defaultValue;
	
	/**
	 * @var KalturaTypeReflector
	 */
	private $_typeReflector;
	private $_arrayTypeReflector;
	private $_readOnly = false;
	private $_insertOnly = false;
	private $_description;
	private $_filters = array();
	
	public function KalturaPropertyInfo($type, $name)
	{
		$this->_type = $type;
		$this->_name = $name;
	}
	
	public function setType($type)
	{
		$this->_type = $type;
	}
	
	public function getType()
	{
		return $this->_type;
	}
	
	public function setName($name)
	{
		$this->_name = $name;
	}
	
	public function getName()
	{
		return $this->_name;
	}

	public function setDefaultValue($value)
	{
		$this->_defaultValue = $value;
	}
	
	public function getDefaultValue()
	{
		return $this->_defaultValue;
	}
	
	/**
	 * @return KalturaTypeReflector
	 */
	public function getTypeReflector()
	{
		if ($this->_typeReflector === null)
		{
			if (!$this->isSimpleType() && $this->_type != "file")
				$this->_typeReflector = KalturaTypeReflectorCacher::get($this->_type);
		}
		
		return $this->_typeReflector;
	}
	
	public function getArrayTypeReflector()
	{
		if ($this->_arrayTypeReflector === null)
		{
			if (!$this->isSimpleType())
				$this->_arrayTypeReflector = KalturaTypeReflectorCacher::get($this->getArrayType());
		}
		
		return $this->_arrayTypeReflector;
	}
	
	public function isSimpleType()
	{
		$simpleTypes = array("int", "string", "bool", "float");
		return in_array($this->_type, $simpleTypes);
	}
	
	public function isComplexType()
	{
		return !$this->isSimpleType();
	}
	
	public function isEnum()
	{
		$this->getTypeReflector();
		if ($this->_typeReflector)
			return $this->_typeReflector->isEnum();
		else
			return false;
	}
	
	public function isStringEnum()
	{
		$this->getTypeReflector();
		if ($this->_typeReflector)
			return $this->_typeReflector->isStringEnum();
		else
			return false;
	}
	
	public function isArray()
	{
		$this->getTypeReflector();
		if ($this->_typeReflector)
			return $this->_typeReflector->isArray();
		else
			return false;
	}
	
	public function getArrayType()
	{
		$this->getTypeReflector();
		if ($this->_typeReflector)
			return $this->_typeReflector->getArrayType();
		else
			return false;
	}
	
	public function setReadOnly($value)
	{
		$this->_readOnly = $value;
	}
	
	public function isReadOnly()
	{
		return $this->_readOnly;
	}
	
	public function setInsertOnly($value)
	{
		$this->_insertOnly = $value;
	}
	
	public function isInsertOnly()
	{
		return $this->_insertOnly;
	}
	
	public function setDescription($desc)
	{
		$this->_description = $desc;
	}
	
	public function getDescription()
	{
		return $this->_description;
	}	
	
	public function setFilters($filters)
	{
		if (is_array($filters))
			$this->_filters = $filters;
		else
			$this->_filters = explode(",", $filters);
		
		foreach($this->_filters as &$filter)
		{
			$filter = trim($filter);
		}
	}	
	
	public function getFilters()
	{
		return $this->_filters;
	}

	public function toArray()
	{
		$array = array();
		$array["type"] 			= $this->getType();
		$array["name"] 			= $this->getName();
		$array["defaultValue"] 	= $this->getDefaultValue();
		$array["isSimpleType"] 	= $this->isSimpleType();
		$array["isComplexType"]	= $this->isComplexType();
		$array["isEnum"] 		= $this->isEnum();
		$array["isStringEnum"] 	= $this->isStringEnum();
		$array["isArray"] 		= $this->isArray();
		
		if ($this->isArray())
		{
			$propInfo = new KalturaPropertyInfo($this->getArrayType(), "1");
			$array["arrayType"]	= $propInfo->toArray();
		}
		$array["isReadOnly"] 	= $this->isReadOnly();
		$array["isInsertOnly"] 	= $this->isInsertOnly();
		$array["description"] 	= $this->getDescription() ? $this->getDescription() : "";
		$array["properties"] 	= array();
		$array["constants"] 	= array();
		$typeReflector = $this->getTypeReflector();
		if ($typeReflector)
		{
			foreach($typeReflector->getProperties() as $prop)
			{
				$array["properties"][] = $prop->toArray();
			}
			
			foreach($typeReflector->getConstants() as $prop)
			{
				$array["constants"][] = $prop->toArray();	
			}
		}
		return $array;
	}
}