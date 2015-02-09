<?php
/**
 * @package plugins.cuePoint
 * @subpackage api.objects
 */
class KalturaCuePointArray extends KalturaTypedArray
{
	public static function fromDbArray($arr, IResponseProfile $responseProfile = null)
	{
		$newArr = new KalturaCuePointArray();
		if ($arr == null)
			return $newArr;
		
		foreach ($arr as $obj)
		{
    		$nObj = KalturaCuePoint::getInstance($obj->getType());
			$nObj->fromObject($obj, $responseProfile);
			$newArr[] = $nObj;
		}
		
		return $newArr;
	}
		
	public function __construct()
	{
		parent::__construct("KalturaCuePoint");	
	}
}
