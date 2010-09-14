<?php

class KalturaMetadataProfileFieldArray extends KalturaTypedArray
{
	public static function fromMetadataProfileFieldArray($arr)
	{
		$newArr = new KalturaMetadataProfileFieldArray();
		if ($arr == null)
			return $newArr;

		foreach ($arr as $obj)
		{
    		$nObj = new KalturaMetadataProfileField();
			$nObj->fromObject($obj);
			$newArr[] = $nObj;
		}
		
		return $newArr;
	}
		
	public function __construct()
	{
		parent::__construct("KalturaMetadataProfileField");	
	}
}