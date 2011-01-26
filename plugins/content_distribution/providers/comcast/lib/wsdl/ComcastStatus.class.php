<?php


class ComcastStatus extends SoapObject
{				
	const _ERROR = 'Error';
					
	const _INPROGRESS = 'InProgress';
					
	const _RETAINED = 'Retained';
					
	const _UNAPPROVED = 'Unapproved';
					
	const _DISABLED = 'Disabled';
					
	const _WARNING = 'Warning';
					
	const _OK = 'OK';
					
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


