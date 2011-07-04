<?php
/**
 * @package plugins.cuePoint
 * @subpackage api.objects
 */
class KalturaCuePointArray extends KalturaTypedArray
{
	public static function fromDbArray($arr)
	{
		$newArr = new KalturaCuePointArray();
		if ($arr == null)
			return $newArr;
		
		foreach ($arr as $obj)
		{
    		$nObj = new KalturaCuePoint();
			$nObj->fromObject($obj);
			$newArr[] = $nObj;
		}
		
		return $newArr;
	}
		
	public function __construct()
	{
		parent::__construct("KalturaCuePoint");	
	}
}
