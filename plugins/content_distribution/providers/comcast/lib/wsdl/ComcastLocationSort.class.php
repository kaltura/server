<?php


class ComcastLocationSort extends SoapObject
{				
	public function getType()
	{
		return 'urn:theplatform-com:v4/admin/sort/:LocationSort';
	}
					
	protected function getAttributeType($attributeName)
	{
		switch($attributeName)
		{	
			case 'field':
				return 'ComcastLocationField';
			case 'tieBreaker':
				return 'ComcastLocationSort';
			default:
				return parent::getAttributeType($attributeName);
		}
	}
					
	public function __toString()
	{
		return print_r($this, true);	
	}
				
	/**
	 * @var ComcastLocationField
	 **/
	public $field;
				
	/**
	 * @var boolean
	 **/
	public $descending;
				
	/**
	 * @var ComcastLocationSort
	 **/
	public $tieBreaker;
				
}


