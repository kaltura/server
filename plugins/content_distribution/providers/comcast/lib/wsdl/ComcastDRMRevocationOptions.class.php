<?php


class ComcastDRMRevocationOptions extends SoapObject
{				
	protected function getAttributeType($attributeName)
	{
		switch($attributeName)
		{	
			case 'releaseIDs':
				return 'ComcastIDSet';
			case 'parentLicenseIDs':
				return 'ComcastIDSet';
			case 'endUserIDs':
				return 'ComcastIDSet';
			default:
				return parent::getAttributeType($attributeName);
		}
	}
					
	public function __toString()
	{
		return print_r($this, true);	
	}
				
	/**
	 * @var string
	 **/
	public $challenge;
				
	/**
	 * @var ComcastIDSet
	 **/
	public $releaseIDs;
				
	/**
	 * @var ComcastIDSet
	 **/
	public $parentLicenseIDs;
				
	/**
	 * @var ComcastIDSet
	 **/
	public $endUserIDs;
				
}


