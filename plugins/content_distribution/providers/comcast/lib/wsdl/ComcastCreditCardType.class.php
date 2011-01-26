<?php


class ComcastCreditCardType extends SoapObject
{				
	const _AMERICANEXPRESS = 'AmericanExpress';
					
	const _DISCOVER = 'Discover';
					
	const _JCB = 'JCB';
					
	const _MASTERCARD = 'MasterCard';
					
	const _NONE = 'None';
					
	const _OTHER = 'Other';
					
	const _VISA = 'Visa';
					
	protected function getAttributeType($attributeName)
	{
		switch($attributeName)
		{	
			default:
				return parent::getAttributeType($attributeName);
		}
	}
					
	public function __toString()
	{
		return print_r($this, true);	
	}
				
}


