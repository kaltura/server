<?php


class ComcastRequestSort extends SoapObject
{				
	public function getType()
	{
		return 'RequestSort';
	}
	
	protected function getAttributeType($attributeName)
	{
		switch($attributeName)
		{	
			case 'field':
				return 'ComcastRequestField';
			case 'tieBreaker':
				return 'ComcastRequestSort';
			default:
				return parent::getAttributeType($attributeName);
		}
	}
					
	public function __toString()
	{
		return print_r($this, true);	
	}
				
	/**
	 * @var ComcastRequestField
	 **/
	public $field;
				
	/**
	 * @var boolean
	 **/
	public $descending;
				
	/**
	 * @var ComcastRequestSort
	 **/
	public $tieBreaker;
				
}


