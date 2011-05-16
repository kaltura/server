<?php


class ComcastEndUserTransactionList extends SoapArray
{				
	public function getType()
	{
		return 'urn:theplatform-com:v4/rights/value/:EndUserTransactionList';
	}
				
	public function __construct()
	{
		parent::__construct("ComcastEndUserTransaction");	
	}
					
	public function __toString()
	{
		return print_r($this, true);	
	}
				
}


