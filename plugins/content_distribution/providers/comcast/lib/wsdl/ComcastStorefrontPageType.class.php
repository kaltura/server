<?php


class ComcastStorefrontPageType extends SoapObject
{				
	const _CATEGORIES = 'Categories';
					
	const _CUSTOM = 'Custom';
					
	const _LICENSES = 'Licenses';
					
	const _MEDIA = 'Media';
					
	const _PLAYLISTS = 'Playlists';
					
	const _PORTAL = 'Portal';
					
	public function getType()
	{
		return 'urn:theplatform-com:v4/enum/:StorefrontPageType';
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
				
}


