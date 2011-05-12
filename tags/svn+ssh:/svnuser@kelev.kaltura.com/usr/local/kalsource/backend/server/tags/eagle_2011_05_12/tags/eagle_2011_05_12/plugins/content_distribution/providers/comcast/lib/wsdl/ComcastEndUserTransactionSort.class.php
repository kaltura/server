<?php


class ComcastEndUserTransactionSort extends SoapObject
{				
	protected function getAttributeType($attributeName)
	{
		switch($attributeName)
		{	
			case 'field':
				return 'ComcastEndUserTransactionField';
			case 'tieBreaker':
				return 'ComcastEndUserTransactionSort';
			default:
				return parent::getAttributeType($attributeName);
		}
	}
					
	public function __toString()
	{
		return print_r($this, true);	
	}
				
	/**
	 * @var ComcastEndUserTransactionField
	 **/
	public $field;
				
	/**
	 * @var boolean
	 **/
	public $descending;
				
	/**
	 * @var ComcastEndUserTransactionSort
	 **/
	public $tieBreaker;
				
}


