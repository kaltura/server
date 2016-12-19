<?php
/**
 * @package api
 * @subpackage objects
 */
class KalturaPlaybackSourceArray extends KalturaTypedArray
{
	public static function fromDbArray($arr, KalturaDetachedResponseProfile $responseProfile = null)
	{
		$newArr = new KalturaPlaybackSourceArray();
		if ($arr == null)
			return $newArr;

		foreach ($arr as $obj)
		{
			$nObj = new KalturaPlaybackSource();
			$nObj->fromObject($obj, $responseProfile);
			$newArr[] = $nObj;
		}

		return $newArr;
	}

	public function __construct()
	{
		parent::__construct("KalturaPlaybackSource");
	}
}