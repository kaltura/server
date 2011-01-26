<?php


class ComcastSystemTaskSort extends SoapObject
{				
	protected function getAttributeType($attributeName)
	{
		switch($attributeName)
		{	
			case 'field':
				return 'ComcastSystemTaskField';
			case 'tieBreaker':
				return 'ComcastSystemTaskSort';
			default:
				return parent::getAttributeType($attributeName);
		}
	}
					
	public function __toString()
	{
		return print_r($this, true);	
	}
				
	/**
	 * @var ComcastSystemTaskField
	 **/
	public $field;
				
	/**
	 * @var boolean
	 **/
	public $descending;
				
	/**
	 * @var ComcastSystemTaskSort
	 **/
	public $tieBreaker;
				
}


