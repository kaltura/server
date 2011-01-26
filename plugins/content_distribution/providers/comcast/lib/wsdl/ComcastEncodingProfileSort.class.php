<?php


class ComcastEncodingProfileSort extends SoapObject
{				
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


