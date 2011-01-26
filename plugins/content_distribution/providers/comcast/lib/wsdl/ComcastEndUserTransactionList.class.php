<?php


class ComcastEndUserTransactionList extends SoapArray
{				
	public function __construct()
	{
		parent::__construct("ComcastEndUserTransaction");	
	}
					
	public function __toString()
	{
		return print_r($this, true);	
	}
				
}


