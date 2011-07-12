<?php


class ComcastDRMLicenseState extends SoapObject
{				
	public function getType()
	{
		return 'urn:theplatform-com:v4/rights/value/:DRMLicenseState';
	}
					
	protected function getAttributeType($attributeName)
	{
		switch($attributeName)
		{	
			case 'expired':
				return 'Comcastboolean';
			default:
				return parent::getAttributeType($attributeName);
		}
	}
					
	public function __toString()
	{
		return print_r($this, true);	
	}
				
	/**
	 * @var Comcastboolean
	 **/
	public $expired;
				
	/**
	 * @var string
	 **/
	public $keyID;
				
}


