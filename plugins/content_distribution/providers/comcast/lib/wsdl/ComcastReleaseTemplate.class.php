<?php


class ComcastReleaseTemplate extends SoapObject
{				
	public function getType()
	{
		return 'urn:theplatform-com:v4/content/template/:ReleaseTemplate';
	}
					
	protected function getAttributeType($attributeName)
	{
		switch($attributeName)
		{	
			case 'fields':
				return 'ComcastArrayOfReleaseField';
			case 'customFields':
				return 'ComcastArrayOfstring';
			case 'contentCustomFields':
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
	 * @var ComcastArrayOfReleaseField
	 **/
	public $fields;
				
	/**
	 * @var ComcastArrayOfstring
	 **/
	public $customFields;
				
	/**
	 * @var ComcastArrayOfstring
	 **/
	public $contentCustomFields;
				
}


