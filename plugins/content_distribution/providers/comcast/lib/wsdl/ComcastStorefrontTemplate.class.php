<?php


class ComcastStorefrontTemplate extends SoapObject
{				
	public function getType()
	{
		return 'StorefrontTemplate';
	}
	
	protected function getAttributeType($attributeName)
	{
		switch($attributeName)
		{	
			case 'fields':
				return 'ComcastArrayOfStorefrontField';
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
	 * @var ComcastArrayOfStorefrontField
	 **/
	public $fields;
				
	/**
	 * @var ComcastArrayOfstring
	 **/
	public $customFields;
				
}


