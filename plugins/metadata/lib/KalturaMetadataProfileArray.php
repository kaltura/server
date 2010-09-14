<?php

class KalturaMetadataProfileArray extends KalturaTypedArray
{
	public static function fromMetadataProfileArray($arr)
	{
		$newArr = new KalturaMetadataProfileArray();
		if ($arr == null)
			return $newArr;

		foreach ($arr as $obj)
		{
    		$nObj = new KalturaMetadataProfile();
			$nObj->fromObject($obj);
			$newArr[] = $nObj;
		}
		
		return $newArr;
	}
		
	public function __construct()
	{
		parent::__construct("KalturaMetadataProfile");	
	}
}