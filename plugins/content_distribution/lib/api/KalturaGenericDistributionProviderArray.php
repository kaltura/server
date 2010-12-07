<?php

class KalturaGenericDistributionProviderArray extends KalturaTypedArray
{
	public static function fromGenericDistributionProvidersArray($arr)
	{
		$newArr = new KalturaGenericDistributionProviderArray();
		if ($arr == null)
			return $newArr;

		foreach ($arr as $obj)
		{
    		$nObj = new KalturaGenericDistributionProvider();
			$nObj->fromObject($obj);
			$newArr[] = $nObj;
		}
		
		return $newArr;
	}
		
	public function __construct()
	{
		parent::__construct("KalturaGenericDistributionProvider");	
	}
}