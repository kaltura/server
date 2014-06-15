<?php
require_once(__DIR__ . '/WebexXmlObject.class.php');
abstract class WebexXmlRequestType extends WebexXmlObject
{
	public function __toString()
	{
		$name = $this->getXmlNodeName();
		$attributes = $this->getAttributes();
		$attributesArray = array();
		foreach($attributes as $attribute => $attributeValue)
			$attributesArray[] = "$attribute=\"$attributeValue\"";
		$attributes = implode(' ', $attributesArray);
		
		$xml = "<$name $attributes>";
		
		$members = $this->getMembers();
		foreach($members as $member)
		{
			if(is_null($this->$member))
				continue;
			if(is_object($this->$member) && $this->$member instanceof WebexXmlRequestType)
				$xml .= $this->$member;
			else
				$xml .= "<$member>{$this->$member}</$member>";
		}
			
		$xml .= "</$name>";
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
	 * @return array
	 */
	protected function getAttributes()
	{
		return array();
	}
	
	/**
	 * @return array
	 */
	protected function getRequiredMembers()
	{
		return array();
	}
	
	/**
	 * @return string
	 */
	abstract protected function getXmlNodeName();
	
	/**
	 * @return array
	 */
	abstract protected function getMembers();
}
