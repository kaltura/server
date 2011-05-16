<?php


class ComcastMediaTemplate extends SoapObject
{				
	public function getType()
	{
		return 'MediaTemplate';
	}
	
	protected function getAttributeType($attributeName)
	{
		switch($attributeName)
		{	
			case 'fields':
				return 'ComcastArrayOfMediaField';
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
	 * @var ComcastArrayOfMediaField
	 **/
	public $fields;
				
	/**
	 * @var ComcastArrayOfstring
	 **/
	public $customFields;
				
}


