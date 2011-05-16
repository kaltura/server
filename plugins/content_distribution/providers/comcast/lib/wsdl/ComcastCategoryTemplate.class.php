<?php


class ComcastCategoryTemplate extends SoapObject
{				
	public function getType()
	{
		return 'CategoryTemplate';
	}
	
	protected function getAttributeType($attributeName)
	{
		switch($attributeName)
		{	
			case 'fields':
				return 'ComcastArrayOfCategoryField';
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
	 * @var ComcastArrayOfCategoryField
	 **/
	public $fields;
				
	/**
	 * @var ComcastArrayOfstring
	 **/
	public $customFields;
				
}


