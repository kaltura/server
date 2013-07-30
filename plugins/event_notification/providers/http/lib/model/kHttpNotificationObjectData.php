<?php
/**
 * Evaluates Kaltura API object, depends on the execution context
 *  
 * @package plugins.httpNotification
 * @subpackage model.data
 */
class kHttpNotificationObjectData extends kHttpNotificationData
{
	/**
	 * Kaltura API object type
	 * @var string
	 */
	protected $objectType;
	
	/**
	 * Data format
	 * @var int
	 */
	protected $format;
	
	/**
	 * Ignore null attributes during serialization
	 * @var bool
	 */
	protected $ignoreNull;
	
	/**
	 * PHP code
	 * @var string
	 */
	protected $code;
	
	/**
	 * Serialized object
	 * @var string
	 */
	protected $coreObject;

	/* (non-PHPdoc)
	 * @see kHttpNotificationData::setScope()
	 */
	public function setScope(kScope $scope) 
	{
		if(strpos($this->code, ';') !== false)
			throw new kCoreException("Evaluated code may be simple value only");
			
		$object = eval("return {$this->code};");
		if(is_object($object))
			$this->coreObject = serialize($object);
	}
	
	/**
	 * @return string
	 */
	public function getCoreObject() 
	{
		return $this->coreObject;
	}

	/**
	 * @return string $objectType
	 */
	public function getObjectType()
	{
		return $this->objectType;
	}

	/**
	 * @return int $format
	 */
	public function getFormat()
	{
		return $this->format;
	}

	/**
	 * @return bool $ignoreNull
	 */
	public function getIgnoreNull()
	{
		return $this->ignoreNull;
	}

	/**
	 * @param string $objectType
	 */
	public function setObjectType($objectType)
	{
		$this->objectType = $objectType;
	}

	/**
	 * @param int $format
	 */
	public function setFormat($format)
	{
		$this->format = $format;
	}

	/**
	 * @param bool $ignoreNull
	 */
	public function setIgnoreNull($ignoreNull)
	{
		$this->ignoreNull = $ignoreNull;
	}
	
	/**
	 * @return string $code
	 */
	public function getCode()
	{
		return $this->code;
	}

	/**
	 * @param string $code
	 */
	public function setCode($code)
	{
		$this->code = $code;
	}
}