<?php


class ComcastRoleTemplate extends SoapObject
{				
	public function getType()
	{
		return 'RoleTemplate';
	}
	
	protected function getAttributeType($attributeName)
	{
		switch($attributeName)
		{	
			case 'fields':
				return 'ComcastArrayOfRoleField';
			case 'customFields':
				return 'ComcastArrayOfstring';
			default:
				return parent::getAttributeType($attributeName);
		}
	}
					
	public function __toString()
	{
		return print_r($this, true);	
	}
				
	/**
	 * @var ComcastArrayOfRoleField
	 **/
	public $fields;
				
	/**
	 * @var ComcastArrayOfstring
	 **/
	public $customFields;
				
}


