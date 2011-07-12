<?php


class ComcastArrayOfAssetTypeField extends SoapArray
{				
	public function getType()
	{
		return 'urn:theplatform-com:v4/content/enum/:ArrayOfAssetTypeField';
	}
				
	public function __construct()
	{
		parent::__construct("ComcastAssetTypeField");	
	}
					
	public function __toString()
	{
		return print_r($this, true);	
	}
				
}


