<?php
/**
 * @package api
 * @subpackage v3
 */
class KalturaParamInfo extends KalturaPropertyInfo 
{
	private $_optional = false;
	 
	public function __construct ($type, $name, $optional = false)
	{
		parent::__construct($type, $name);
		$this->_optional = $optional;
	}

	public function setOptional($optional)
	{
		$this->_optional = $optional;
	}
		
	public function isOptional()
	{
		return $this->_optional;
	}
	
	public function toArray($withSubTypes = false, $returnedTypes = array())
	{
		$array = parent::toArray($withSubTypes);
		$array["isFile"] = $this->isFile();
		$array["isOptional"] = $this->isOptional();
		return $array;
	}
}
