<?php


class ComcastDirectorySort extends SoapObject
{				
	public function getType()
	{
		return 'DirectorySort';
	}
	
	protected function getAttributeType($attributeName)
	{
		switch($attributeName)
		{	
			case 'field':
				return 'ComcastDirectoryField';
			case 'tieBreaker':
				return 'ComcastDirectorySort';
			default:
				return parent::getAttributeType($attributeName);
		}
	}
					
	public function __toString()
	{
		return print_r($this, true);	
	}
				
	/**
	 * @var ComcastDirectoryField
	 **/
	public $field;
				
	/**
	 * @var boolean
	 **/
	public $descending;
				
	/**
	 * @var ComcastDirectorySort
	 **/
	public $tieBreaker;
				
}


