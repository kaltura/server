<?php


class ComcastRoleSort extends SoapObject
{				
	public function getType()
	{
		return 'urn:theplatform-com:v4/admin/sort/:RoleSort';
	}
					
	protected function getAttributeType($attributeName)
	{
		switch($attributeName)
		{	
			case 'field':
				return 'ComcastRoleField';
			case 'tieBreaker':
				return 'ComcastRoleSort';
			default:
				return parent::getAttributeType($attributeName);
		}
	}
					
	public function __toString()
	{
		return print_r($this, true);	
	}
				
	/**
	 * @var ComcastRoleField
	 **/
	public $field;
				
	/**
	 * @var boolean
	 **/
	public $descending;
				
	/**
	 * @var ComcastRoleSort
	 **/
	public $tieBreaker;
				
}


