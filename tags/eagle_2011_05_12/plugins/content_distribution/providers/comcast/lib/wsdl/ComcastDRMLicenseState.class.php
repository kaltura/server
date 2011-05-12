<?php


class ComcastDRMLicenseState extends SoapObject
{				
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


