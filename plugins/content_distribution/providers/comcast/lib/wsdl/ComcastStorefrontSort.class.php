<?php


class ComcastStorefrontSort extends SoapObject
{				
	protected function getAttributeType($attributeName)
	{
		switch($attributeName)
		{	
			case 'field':
				return 'ComcastStorefrontField';
			case 'tieBreaker':
				return 'ComcastStorefrontSort';
			default:
				return parent::getAttributeType($attributeName);
		}
	}
					
	public function __toString()
	{
		return print_r($this, true);	
	}
				
	/**
	 * @var ComcastStorefrontField
	 **/
	public $field;
				
	/**
	 * @var boolean
	 **/
	public $descending;
				
	/**
	 * @var ComcastStorefrontSort
	 **/
	public $tieBreaker;
				
}


