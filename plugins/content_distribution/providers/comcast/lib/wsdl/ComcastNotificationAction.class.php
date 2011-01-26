<?php


class ComcastNotificationAction extends SoapObject
{				
	const _ADD = 'Add';
					
	const _MODIFY = 'Modify';
					
	const _DELETE = 'Delete';
					
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


