<?php


class ComcastStorefrontPageSort extends SoapObject
{				
	protected function getAttributeType($attributeName)
	{
		switch($attributeName)
		{	
			case 'field':
				return 'ComcastStorefrontPageField';
			case 'tieBreaker':
				return 'ComcastStorefrontPageSort';
			default:
				return parent::getAttributeType($attributeName);
		}
	}
					
	public function __toString()
	{
		return print_r($this, true);	
	}
				
	/**
	 * @var ComcastStorefrontPageField
	 **/
	public $field;
				
	/**
	 * @var boolean
	 **/
	public $descending;
				
	/**
	 * @var ComcastStorefrontPageSort
	 **/
	public $tieBreaker;
				
}


