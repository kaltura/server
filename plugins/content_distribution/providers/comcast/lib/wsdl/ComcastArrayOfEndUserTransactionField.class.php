<?php


class ComcastArrayOfEndUserTransactionField extends SoapArray
{				
	public function __construct()
	{
		parent::__construct("ComcastEndUserTransactionField");	
	}
					
	public function __toString()
	{
		return print_r($this, true);	
	}
				
}


