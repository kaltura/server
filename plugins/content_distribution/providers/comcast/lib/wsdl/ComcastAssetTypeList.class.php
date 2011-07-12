<?php


class ComcastAssetTypeList extends SoapArray
{				
	public function getType()
	{
		return 'urn:theplatform-com:v4/content/value/:AssetTypeList';
	}
				
	public function __construct()
	{
		parent::__construct("ComcastAssetType");	
	}
					
	public function __toString()
	{
		return print_r($this, true);	
	}
				
}


