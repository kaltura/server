<?php
/**
 * @package api
 * @subpackage objects
 */
class KalturaMediaCompositionAttributesArray extends KalturaTypedArray
{
	public static function fromDbArray(array $arr = null, KalturaDetachedResponseProfile $responseProfile = null)
	{
		$newArr = new KalturaMediaCompositionAttributesArray();
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
		parent::__construct("KalturaMediaCompositionAttributes");
	}
}
