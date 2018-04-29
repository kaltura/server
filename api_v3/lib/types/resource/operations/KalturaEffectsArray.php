<?php
/**
 * @package api
 * @subpackage objects
 */
class KalturaEffectsArray extends KalturaTypedArray
{
	public static function fromDbArray(array $arr = null, KalturaDetachedResponseProfile $responseProfile = null)
	{
		$newArr = new KalturaEffectsArray();
		if(is_null($arr))
			return $newArr;

		foreach($arr as $obj)
		{
			$nObj = new KalturaEffect();
			$nObj->fromObject($obj, $responseProfile);
			$newArr[] = $nObj;
		}

		return $newArr;
	}

	public function __construct()
	{
		parent::__construct("KalturaEffect");
	}
}