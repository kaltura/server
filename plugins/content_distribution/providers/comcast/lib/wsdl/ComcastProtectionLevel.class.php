<?php


class ComcastProtectionLevel extends SoapObject
{				
	const _DRM = 'DRM';
					
	const _LINK = 'Link';
					
	const _LINKANDDRM = 'LinkAndDRM';
					
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


