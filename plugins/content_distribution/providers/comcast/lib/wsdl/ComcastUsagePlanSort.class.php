<?php


class ComcastUsagePlanSort extends SoapObject
{				
	public function getType()
	{
		return 'urn:theplatform-com:v4/rights/sort/:UsagePlanSort';
	}
					
	protected function getAttributeType($attributeName)
	{
		switch($attributeName)
		{	
			case 'field':
				return 'ComcastUsagePlanField';
			case 'tieBreaker':
				return 'ComcastUsagePlanSort';
			default:
				return parent::getAttributeType($attributeName);
		}
	}
					
	public function __toString()
	{
		return print_r($this, true);	
	}
				
	/**
	 * @var ComcastUsagePlanField
	 **/
	public $field;
				
	/**
	 * @var boolean
	 **/
	public $descending;
				
	/**
	 * @var ComcastUsagePlanSort
	 **/
	public $tieBreaker;
				
}


