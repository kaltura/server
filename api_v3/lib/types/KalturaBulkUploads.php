<?php

class KalturaBulkUploads extends KalturaTypedArray
{
	public static function fromBatchJobArray ($arr)
	{
		$newArr = new KalturaBulkUploads();
		if ($arr == null)
			return $newArr;
					
		foreach ($arr as $obj)
		{
			$nObj = new KalturaBulkUpload();
			$nObj->fromObject($obj);
			$newArr[] = $nObj;
		}
		
		return $newArr;
	}
		
	public function __construct()
	{
		parent::__construct("KalturaBulkUpload");	
	}
}