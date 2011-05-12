<?php


class ComcastAssetTypeList extends SoapArray
{				
	public function __construct()
	{
		parent::__construct("ComcastAssetType");	
	}
					
	public function __toString()
	{
		return print_r($this, true);	
	}
				
}


