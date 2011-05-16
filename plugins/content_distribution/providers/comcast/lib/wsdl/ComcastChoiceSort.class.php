<?php


class ComcastChoiceSort extends SoapObject
{				
	public function getType()
	{
		return 'urn:theplatform-com:v4/content/sort/:ChoiceSort';
	}
					
	protected function getAttributeType($attributeName)
	{
		switch($attributeName)
		{	
			case 'field':
				return 'ComcastChoiceField';
			case 'tieBreaker':
				return 'ComcastChoiceSort';
			default:
				return parent::getAttributeType($attributeName);
		}
	}
					
	public function __toString()
	{
		return print_r($this, true);	
	}
				
	/**
	 * @var ComcastChoiceField
	 **/
	public $field;
				
	/**
	 * @var boolean
	 **/
	public $descending;
				
	/**
	 * @var ComcastChoiceSort
	 **/
	public $tieBreaker;
				
}


