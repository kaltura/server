<?php


class ComcastArrayOfEndUserTransactionField extends SoapArray
{				
	public function getType()
	{
		return 'urn:theplatform-com:v4/rights/enum/:ArrayOfEndUserTransactionField';
	}
				
	public function __construct()
	{
		parent::__construct("ComcastEndUserTransactionField");	
	}
					
	public function __toString()
	{
		return print_r($this, true);	
	}
				
}


