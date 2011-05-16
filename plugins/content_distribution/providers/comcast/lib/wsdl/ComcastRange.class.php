<?php


class ComcastRange extends SoapObject
{				
	public function getType()
	{
		return 'Range';
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
	 * @var long
	 **/
	public $startIndex;
				
	/**
	 * @var long
	 **/
	public $endIndex;
				
}


