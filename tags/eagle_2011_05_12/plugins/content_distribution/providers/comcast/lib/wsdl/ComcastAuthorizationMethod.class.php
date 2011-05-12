<?php


class ComcastAuthorizationMethod extends SoapObject
{				
	const _DIRECTORY = 'Directory';
					
	const _REMOTEUSER = 'RemoteUser';
					
	const _STOREDPASSWORD = 'StoredPassword';
					
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


