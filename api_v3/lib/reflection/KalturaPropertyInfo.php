<?php
/**
 * @package api
 * @subpackage v3
 */
class KalturaPropertyInfo
{
	/**
	 * @var string class name
	 */
	private $_type;
	
	/**
	 * @var string property name
	 */
	private $_name;
	
	/**
	 * @var mix
	 */
	private $_defaultValue;
	
	/**
	 * @var KalturaTypeReflector
	 */
	private $_typeReflector;
	
	/**
	 * @var KalturaTypeReflector
	 */
	private $_arrayTypeReflector;
	
	/**
	 * @var bool
	 */
	private $_readOnly = false;
	
	/**
	 * @var bool
	 */
	private $_insertOnly = false;
	
	/**
	 * @var bool
	 */
	private $_writeOnly = false;
	
	/**
	 * @var string
	 */
	private $_description;
	
	/**
	 * @var array of strings
	 */
	private $_filters = array();
	
	private $_dynamicType = null;
	
	/**
	 * @var array
	 */
	private $_permissions = array();
	
	/**
	 * @var array
	 */
	private $_constraints = array();

	/**
	 * @var bool
	 */
	private $_disableRelativeTime = false;
	
	/**
	 * @var bool
	 */
	private $_deprecated = false;
	
	/**
	 * @var string
	 */
	private $_deprecationMessage = null;
	
	/**
	 * @var bool
	 */
	private $_serverOnly = false;

    /**
     * @var bool
     */
    private $_isTime = false;

	const READ_PERMISSION_NAME = 'read';
	const UPDATE_PERMISSION_NAME = 'update';
	const INSERT_PERMISSION_NAME = 'insert';
	const ALL_PERMISSION_NAME = 'all';	
	
	/**
	 * @param string $type class name
	 * @param string $name property name
	 */
	public function __construct($type, $name = '')
	{
		if ($type == 'time')
		{
			$this->_isTime = true;
			$type = 'int';
		}
		$this->_type = $type;
		$this->_name = $name;
	}
	
	/**
	 * @param string $type class name
	 */
	public function setType($type)
	{
		$this->_type = $type;
	}
	
	/**
	 * @return string class name
	 */
	public function getType()
	{
		return $this->_type;
	}
	
	/**
	 * @param string $name
	 */
	public function setName($name)
	{
		$this->_name = $name;
	}
	
	/**
	 * @return string
	 */
	public function getName()
	{
		return $this->_name;
	}

	/**
	 * @param string $value
	 */
	public function setDefaultValue($value)
	{
		$this->_defaultValue = $value;
	}
	
	/**
	 * @return mix
	 */
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
	
	/**
	 * @return KalturaTypeReflector
	 */
	public function getArrayTypeReflector()
	{
		if ($this->_arrayTypeReflector === null)
		{
			if (!$this->isSimpleType())
				$this->_arrayTypeReflector = KalturaTypeReflectorCacher::get($this->getArrayType());
		}
		
		return $this->_arrayTypeReflector;
	}
	
	/**
	 * Returns the name of the constant according to its value 
	 *
	 * @param mixed $value
	 * @return string
	 */
	public function getConstantName($value)
	{
		$this->getTypeReflector();
		if ($this->_typeReflector)
			return $this->_typeReflector->getConstantName($value);
		else
			return null;
	}
	
	/**
	 * @return boolean
	 */
	public function isFile()
	{
		return $this->_type == 'file';
	}
	
	/**
	 * @return boolean
	 */
	public function isSimpleType()
	{
		$simpleTypes = array("int", "string", "bool", "float", "bigint");
		return in_array($this->_type, $simpleTypes);
	}
	
	/**
	 * @return boolean
	 */
	public function isComplexType()
	{
		return !$this->isSimpleType() && !$this->isFile();
	}

	/**
	 * Returns true when the property is marked as time.
	 * Time types are actually treated as int.
	 *
	 * @return boolean
	 */
	public function isTime()
	{
		return ($this->_isTime);
	}
	
	/**
	 * @return boolean
	 */
	public function isEnum()
	{
		$this->getTypeReflector();
		if ($this->_typeReflector)
			return $this->_typeReflector->isEnum();
		else
			return false;
	}
	
	/**
	 * @return boolean
	 */
	public function isStringEnum()
	{
		$this->getTypeReflector();
		if ($this->_typeReflector)
			return $this->_typeReflector->isStringEnum();
		else
			return false;
	}
	
	/**
	 * @return boolean
	 */
	public function isDynamicEnum()
	{
		$this->getTypeReflector();
		if ($this->_typeReflector)
			return $this->_typeReflector->isDynamicEnum();
		else
			return false;
	}
	
	/**
	 * @return boolean
	 */
	public function isArray()
	{
		$this->getTypeReflector();
		if ($this->_typeReflector)
			return $this->_typeReflector->isArray();
		else
			return false;
	}
	
	/**
	 * @return boolean
	 */
	public function isAssociativeArray()
	{
		$this->getTypeReflector();
		if ($this->_typeReflector)
			return $this->_typeReflector->isAssociativeArray();
		else
			return false;
	}
	
	/**
	 * @return boolean
	 */
	public function isAbstract()
	{
		$this->getTypeReflector();
		
		if ($this->_typeReflector)
			return $this->_typeReflector->isAbstract();
		else
			return false;
	}
	
	/**
	 * @return string
	 */
	public function getArrayType()
	{
		$this->getTypeReflector();
		if ($this->_typeReflector)
			return $this->_typeReflector->getArrayType();
		else
			return false;
	}
	
	public function setDynamicType($value)
	{
		$this->_dynamicType = $value;
	}
	
	public function getDynamicType()
	{
		return $this->_dynamicType;
	}
	
	/**
	 * @param bool $value
	 */
	public function setReadOnly($value)
	{
		$this->_readOnly = $value;
	}
	
	/**
	 * @return boolean
	 */
	public function isReadOnly()
	{
		return $this->_readOnly;
	}
	
	/**
	 * @param bool $value
	 */
	public function setInsertOnly($value)
	{
		$this->_insertOnly = $value;
	}
	
	/**
	 * @param bool $value
	 */
	public function setWriteOnly($value)
	{
		$this->_writeOnly = $value;
	}
	
	/**
	 * @return boolean
	 */
	public function isInsertOnly()
	{
		return $this->_insertOnly;
	}
	
	/**
	 * @return boolean
	 */
	public function isWriteOnly()
	{
		return $this->_writeOnly;
	}
	
	/**
	 * @param bool $value
	 */
	public function setDeprecated($value)
	{
		$this->_deprecated = $value;
	}
	
	/**
	 * @return boolean
	 */
	public function isDeprecated()
	{
		return $this->_deprecated;
	}
	
	/**
	 * @param string $value
	 */
	public function setDeprecationMessage($value)
	{
		$this->_deprecationMessage = $value;
	}
	
	/**
	 * @return string
	 */
	public function getDeprecationMessage()
	{
		return $this->_deprecationMessage;
	}
	
	/**
	 * @param bool $value
	 */
	public function setServerOnly($value)
	{
		$this->_serverOnly = $value;
	}
	
	/**
	 * @return boolean
	 */
	public function isServerOnly()
	{
		return $this->_serverOnly;
	}
	
	/**
	 * @param string $desc
	 */
	public function setDescription($desc)
	{
		$this->_description = $desc;
	}
	
	/**
	 * @return string
	 */
	public function getDescription()
	{
		return $this->_description;
	}	
	
	/**
	 * @param array|string $filters array or comma separated values
	 */
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
	
	/**
	 * @return array of strings
	 */
	public function getFilters()
	{
		return $this->_filters;
	}
	
	public function setConstraints($constaints) {
		$this->_constraints = $constaints;
	}
	
	public function getConstraints() {
		return $this->_constraints;
	}

	/**
	 * @param boolean $disableRelativeTime
	 */
	public function setDisableRelativeTime($disableRelativeTime)
	{
		$this->_disableRelativeTime = $disableRelativeTime;
	}

	/**
	 * @return boolean
	 */
	public function getDisableRelativeTime()
	{
		return $this->_disableRelativeTime;
	}

	/**
	 * @param array $permissions
	 */
	public function setPermissions($permissions)
	{
		if (is_array($permissions))
			$this->_permissions = $permissions;
		else
			$this->_permissions = explode(",", $permissions);
		
		foreach($this->_permissions as &$permission)
		{
			$permission = trim($permission);
		}
	}
	
	
	/**
	 * @return array
	 */
	public function getPermissions()
	{
		return $this->_permissions;
	}
	
	/**
	 * @return boolean
	 */
	public function requiresReadPermission()
	{
		if ($this->requiresUsagePermission())
		{
			return true;
		}
		return in_array(self::READ_PERMISSION_NAME, $this->_permissions);
	}
	
	/**
	 * @return boolean
	 */
	public function requiresUpdatePermission()
	{
		if ($this->requiresUsagePermission())
		{
			return true;
		}
		return in_array(self::UPDATE_PERMISSION_NAME, $this->_permissions);
	}
	
	/**
	 * @return boolean
	 */
	public function requiresInsertPermission()
	{
		if ($this->requiresUsagePermission())
		{
			return true;
		}
		return in_array(self::INSERT_PERMISSION_NAME, $this->_permissions);
	}
	
	/**
	 * @return boolean
	 */
	public function requiresUsagePermission()
	{
		return in_array(self::ALL_PERMISSION_NAME, $this->_permissions);
	}

	/**
	 * @param bool $withSubTypes
	 * @return array 
	 */
	public function toArray($withSubTypes = false, $returnedTypes = array())
	{
		$array = array();
		$array["type"] 			= $this->getType();
		
		if(in_array($this->getType(), $returnedTypes))
		{
			return $array;
		}
		
		$returnedTypes[] = $this->getType();
		
		$array["name"] 			= $this->getName();
		$array["defaultValue"] 	= $this->getDefaultValue();
		$array["isSimpleType"] 	= $this->isSimpleType();
		$array["isComplexType"]	= $this->isComplexType();
		$array["isFile"]		= $this->isFile();
		$array["isEnum"] 		= $this->isEnum();
		$array["isStringEnum"] 	= $this->isStringEnum();
		$array["isArray"] 		= $this->isArray();
		$array["isAbstract"] 		= $this->isAbstract();
		
		if ($this->isArray())
		{
			$propInfo = new KalturaPropertyInfo($this->getArrayType(), "1");
			$array["arrayType"]	= $propInfo->toArray(false, $returnedTypes);
		}
		$array["isReadOnly"] 	= $this->isReadOnly();
		$array["isInsertOnly"] 	= $this->isInsertOnly();
		$array["isWriteOnly"] 	= $this->isWriteOnly();
		$array["description"] 	= $this->getDescription() ? $this->getDescription() : "";
		$array["properties"] 	= array();
		$array["constants"] 	= array();
		$array["subTypes"]		= array();
		
		$typeReflector = $this->getTypeReflector();
		if ($typeReflector)
		{
			if($withSubTypes)
			{
				$subTypes = $typeReflector->getSubTypesNames();
				foreach($subTypes as $subType)
				{
					$subTypeInfo = new KalturaPropertyInfo($subType, $this->_name);
					$array["subTypes"][] = $subTypeInfo->toArray(false, $returnedTypes);
				}
			}
			
			foreach($typeReflector->getProperties() as $prop)
			{
				$array["properties"][] = $prop->toArray($withSubTypes, $returnedTypes);
			}
			
			foreach($typeReflector->getConstants() as $prop)
			{
				$array["constants"][] = $prop->toArray(false, $returnedTypes);	
			}
		}
		return $array;
	}
}
