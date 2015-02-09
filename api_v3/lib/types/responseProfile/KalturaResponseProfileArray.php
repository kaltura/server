<?php
/**
 * @package api
 * @subpackage objects
 */
class KalturaResponseProfileArray extends KalturaTypedArray
{
	public static function fromDbArray($arr, IResponseProfile $responseProfile = null)
	{
		$newArr = new KalturaResponseProfileArray();
		if ($arr == null)
			return $newArr;

		foreach ($arr as $obj)
		{
			$nObj = new KalturaResponseProfile();
			if(!$nObj)
			{
				KalturaLog::alert("Object [" . get_class($obj) . "] type [" . $obj->getType() . "] could not be translated to API object");
				continue;
			}
			$nObj->fromObject($obj, $responseProfile);
			$newArr[] = $nObj;
		}
		
		return $newArr;
	}
		
	public function __construct()
	{
		parent::__construct("KalturaResponseProfile");	
	}
}