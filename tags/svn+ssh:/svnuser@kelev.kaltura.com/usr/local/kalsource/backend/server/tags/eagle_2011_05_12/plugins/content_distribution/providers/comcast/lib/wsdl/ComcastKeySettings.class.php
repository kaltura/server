<?php


class ComcastKeySettings extends SoapObject
{				
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
				
	/**
	 * @var string
	 **/
	public $userName;
				
	/**
	 * @var string
	 **/
	public $prefix;
				
	/**
	 * @var string
	 **/
	public $digestAlgorithm;
				
	/**
	 * @var string
	 **/
	public $key;
				
	/**
	 * @var boolean
	 **/
	public $useHexKey;
				
}


