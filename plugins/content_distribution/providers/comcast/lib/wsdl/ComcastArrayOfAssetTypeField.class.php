<?php


class ComcastArrayOfAssetTypeField extends SoapArray
{				
	public function __construct()
	{
		parent::__construct("ComcastAssetTypeField");	
	}
					
	public function __toString()
	{
		return print_r($this, true);	
	}
				
}


