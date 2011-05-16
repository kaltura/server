<?php


class ComcastLargeTextValue extends ComcastFieldValue
{				
	public function getType()
	{
		return 'urn:theplatform-com:v4/base/:LargeTextValue';
	}
					
	protected function getAttributeType($attributeName)
	{
		switch($attributeName)
		{	
			default:
				return parent::getAttributeType($attributeName);
		}
	}
					
	public function __toString()
	{
		return print_r($this, true);	
	}
				
	/**
	 * @var string
	 **/
	public $text;
				
}


