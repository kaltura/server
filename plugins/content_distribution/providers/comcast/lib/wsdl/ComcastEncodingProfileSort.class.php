<?php


class ComcastEncodingProfileSort extends SoapObject
{				
	public function getType()
	{
		return 'urn:theplatform-com:v4/content/sort/:EncodingProfileSort';
	}
					
	protected function getAttributeType($attributeName)
	{
		switch($attributeName)
		{	
			case 'field':
				return 'ComcastEncodingProfileField';
			case 'tieBreaker':
				return 'ComcastEncodingProfileSort';
			default:
				return parent::getAttributeType($attributeName);
		}
	}
					
	public function __toString()
	{
		return print_r($this, true);	
	}
				
	/**
	 * @var ComcastEncodingProfileField
	 **/
	public $field;
				
	/**
	 * @var boolean
	 **/
	public $descending;
				
	/**
	 * @var ComcastEncodingProfileSort
	 **/
	public $tieBreaker;
				
}


