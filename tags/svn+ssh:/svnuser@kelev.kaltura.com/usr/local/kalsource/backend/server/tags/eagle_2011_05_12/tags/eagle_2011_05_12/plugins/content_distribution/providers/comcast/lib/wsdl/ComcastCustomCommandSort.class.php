<?php


class ComcastCustomCommandSort extends SoapObject
{				
	protected function getAttributeType($attributeName)
	{
		switch($attributeName)
		{	
			case 'field':
				return 'ComcastCustomCommandField';
			case 'tieBreaker':
				return 'ComcastCustomCommandSort';
			default:
				return parent::getAttributeType($attributeName);
		}
	}
					
	public function __toString()
	{
		return print_r($this, true);	
	}
				
	/**
	 * @var ComcastCustomCommandField
	 **/
	public $field;
				
	/**
	 * @var boolean
	 **/
	public $descending;
				
	/**
	 * @var ComcastCustomCommandSort
	 **/
	public $tieBreaker;
				
}


