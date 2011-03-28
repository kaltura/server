<?php
class KalturaParamInfo extends KalturaPropertyInfo 
{
	private $_optional = false;
	 
	public function KalturaParamInfo($type, $name, $optional = false)
	{
		parent::KalturaPropertyInfo($type, $name);
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
	
	public function toArray($withSubTypes = false)
	{
		$array = parent::toArray($withSubTypes);
		$array["isFile"] = $this->isFile();
		$array["isOptional"] = $this->isOptional();
		return $array;
	}
}