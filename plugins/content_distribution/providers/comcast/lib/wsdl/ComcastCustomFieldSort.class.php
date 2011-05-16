<?php


class ComcastCustomFieldSort extends SoapObject
{				
	public function getType()
	{
		return 'urn:theplatform-com:v4/admin/sort/:CustomFieldSort';
	}
					
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


