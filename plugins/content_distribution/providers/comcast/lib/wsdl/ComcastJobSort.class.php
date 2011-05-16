<?php


class ComcastJobSort extends SoapObject
{				
	public function getType()
	{
		return 'urn:theplatform-com:v4/admin/sort/:JobSort';
	}
					
	protected function getAttributeType($attributeName)
	{
		switch($attributeName)
		{	
			case 'field':
				return 'ComcastJobField';
			case 'tieBreaker':
				return 'ComcastJobSort';
			default:
				return parent::getAttributeType($attributeName);
		}
	}
					
	public function __toString()
	{
		return print_r($this, true);	
	}
				
	/**
	 * @var ComcastJobField
	 **/
	public $field;
				
	/**
	 * @var boolean
	 **/
	public $descending;
				
	/**
	 * @var ComcastJobSort
	 **/
	public $tieBreaker;
				
}


