<?php


class ComcastEndUserTransactionTemplate extends SoapObject
{				
	public function getType()
	{
		return 'EndUserTransactionTemplate';
	}
	
	protected function getAttributeType($attributeName)
	{
		switch($attributeName)
		{	
			case 'fields':
				return 'ComcastArrayOfEndUserTransactionField';
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
	 * @var ComcastArrayOfEndUserTransactionField
	 **/
	public $fields;
				
	/**
	 * @var ComcastArrayOfstring
	 **/
	public $customFields;
				
}


