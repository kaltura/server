<?php


class ComcastAuthentication extends SoapObject
{				
	const _EXTERNAL = 'External';
					
	const _END_USER = 'End-user';
					
	const _NONE = 'None';
					
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


