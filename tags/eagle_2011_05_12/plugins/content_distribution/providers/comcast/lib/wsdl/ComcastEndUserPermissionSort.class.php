<?php


class ComcastEndUserPermissionSort extends SoapObject
{				
	protected function getAttributeType($attributeName)
	{
		switch($attributeName)
		{	
			case 'field':
				return 'ComcastEndUserPermissionField';
			case 'tieBreaker':
				return 'ComcastEndUserPermissionSort';
			default:
				return parent::getAttributeType($attributeName);
		}
	}
					
	public function __toString()
	{
		return print_r($this, true);	
	}
				
	/**
	 * @var ComcastEndUserPermissionField
	 **/
	public $field;
				
	/**
	 * @var boolean
	 **/
	public $descending;
				
	/**
	 * @var ComcastEndUserPermissionSort
	 **/
	public $tieBreaker;
				
}


