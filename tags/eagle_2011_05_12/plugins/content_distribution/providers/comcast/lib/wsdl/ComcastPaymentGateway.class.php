<?php


class ComcastPaymentGateway extends SoapObject
{				
	const _CUSTOM = 'Custom';
					
	const _NONE = 'None';
					
	const _PAYPAL = 'PayPal';
					
	const _VERISIGNPAYFLOWPRO = 'VeriSignPayflowPro';
					
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


