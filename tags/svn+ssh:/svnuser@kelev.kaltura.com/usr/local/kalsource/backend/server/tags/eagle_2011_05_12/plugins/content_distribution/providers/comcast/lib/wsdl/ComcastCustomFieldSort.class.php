<?php


class ComcastCustomFieldSort extends SoapObject
{				
	protected function getAttributeType($attributeName)
	{
		switch($attributeName)
		{	
			case 'field':
				return 'ComcastCustomFieldField';
			case 'tieBreaker':
				return 'ComcastCustomFieldSort';
			default:
				return parent::getAttributeType($attributeName);
		}
	}
					
	public function __toString()
	{
		return print_r($this, true);	
	}
				
	/**
	 * @var ComcastCustomFieldField
	 **/
	public $field;
				
	/**
	 * @var boolean
	 **/
	public $descending;
				
	/**
	 * @var ComcastCustomFieldSort
	 **/
	public $tieBreaker;
				
}


