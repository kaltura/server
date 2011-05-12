<?php


class ComcastStorefrontOrderType extends SoapObject
{				
	const _CATEGORY = 'Category';
					
	const _LICENSE = 'License';
					
	const _MEDIA = 'Media';
					
	const _PLAYLIST = 'Playlist';
					
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


