<?php


class ComcastJobHeader extends SoapObject
{				
	public function getType()
	{
		return 'JobHeader';
	}
	
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
	public $job;
				
}


