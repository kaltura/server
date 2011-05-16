<?php


class ComcastReleaseSort extends SoapObject
{				
	public function getType()
	{
		return 'urn:theplatform-com:v4/content/sort/:ReleaseSort';
	}
					
	protected function getAttributeType($attributeName)
	{
		switch($attributeName)
		{	
			case 'field':
				return 'ComcastReleaseField';
			case 'tieBreaker':
				return 'ComcastReleaseSort';
			default:
				return parent::getAttributeType($attributeName);
		}
	}
					
	public function __toString()
	{
		return print_r($this, true);	
	}
				
	/**
	 * @var ComcastReleaseField
	 **/
	public $field;
				
	/**
	 * @var boolean
	 **/
	public $descending;
				
	/**
	 * @var ComcastReleaseSort
	 **/
	public $tieBreaker;
				
}


