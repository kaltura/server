<?php


class ComcastMediaFileSort extends SoapObject
{				
	public function getType()
	{
		return 'urn:theplatform-com:v4/content/sort/:MediaFileSort';
	}
					
	protected function getAttributeType($attributeName)
	{
		switch($attributeName)
		{	
			case 'field':
				return 'ComcastMediaFileField';
			case 'tieBreaker':
				return 'ComcastMediaFileSort';
			default:
				return parent::getAttributeType($attributeName);
		}
	}
					
	public function __toString()
	{
		return print_r($this, true);	
	}
				
	/**
	 * @var ComcastMediaFileField
	 **/
	public $field;
				
	/**
	 * @var boolean
	 **/
	public $descending;
				
	/**
	 * @var ComcastMediaFileSort
	 **/
	public $tieBreaker;
				
}


