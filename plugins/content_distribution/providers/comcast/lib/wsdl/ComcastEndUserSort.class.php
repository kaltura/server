<?php


class ComcastEndUserSort extends SoapObject
{				
	public function getType()
	{
		return 'urn:theplatform-com:v4/rights/sort/:EndUserSort';
	}
					
	protected function getAttributeType($attributeName)
	{
		switch($attributeName)
		{	
			case 'field':
				return 'ComcastEndUserField';
			case 'tieBreaker':
				return 'ComcastEndUserSort';
			default:
				return parent::getAttributeType($attributeName);
		}
	}
					
	public function __toString()
	{
		return print_r($this, true);	
	}
				
	/**
	 * @var ComcastEndUserField
	 **/
	public $field;
				
	/**
	 * @var boolean
	 **/
	public $descending;
				
	/**
	 * @var ComcastEndUserSort
	 **/
	public $tieBreaker;
				
}


