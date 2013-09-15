<?php

abstract class WebexXmlEnumerator extends WebexXmlObject
{
	/**
	 * @var string
	 */
	protected $value;
	
	public function __construct ($value)
	{
		$this->value = $value;
	}
	
	
	public function __toString()
	{
		$name = $this->getXmlNodeName();
			
		$xml = "<$name>{$this->value}</$name>";
		return $xml;
	}
	
	protected function validate()
	{
		$members = $this->getRequiredMembers();
		foreach($members as $member)
		{
			if(is_null($this->$member))
				throw new WebexXmlException(get_class($this) . "::$member is required");
		}
	}
	
	/**
	 * @return string
	 */
	abstract protected function getXmlNodeName();
	

}
