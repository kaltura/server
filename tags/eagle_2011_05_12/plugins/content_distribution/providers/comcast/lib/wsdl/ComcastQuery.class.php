<?php


class ComcastQuery extends SoapObject
{				
	protected function getAttributeType($attributeName)
	{
		switch($attributeName)
		{	
			case 'parameterNames':
				return 'ComcastArrayOfstring';
			case 'parameterValues':
				return 'ComcastArrayOfanyType';
			case 'and':
				return 'ComcastArrayOfQuery';
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
	public $name;
				
	/**
	 * @var ComcastArrayOfstring
	 **/
	public $parameterNames;
				
	/**
	 * @var ComcastArrayOfanyType
	 **/
	public $parameterValues;
				
	/**
	 * @var ComcastArrayOfQuery
	 **/
	public $and;
				
}


