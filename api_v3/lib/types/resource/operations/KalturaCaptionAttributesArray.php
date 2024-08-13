<?php
/**
 * @package api
 * @subpackage objects
 */
class KalturaCaptionAttributesArray extends KalturaTypedArray
{
	public static function fromDbArray(array $arr = null, KalturaDetachedResponseProfile $responseProfile = null)
	{
		$newArr = new KalturaCaptionAttributesArray();
		if(is_null($arr))
		{
			return $newArr;
		}

		foreach($arr as $obj)
		{
			$class = $obj->getApiType();
			$nObj = new $class();
			$nObj->fromObject($obj, $responseProfile);
			$newArr[] = $nObj;
		}

		return $newArr;
	}

	public function __construct()
	{
		parent::__construct("KalturaCaptionAttributes");
	}
}
