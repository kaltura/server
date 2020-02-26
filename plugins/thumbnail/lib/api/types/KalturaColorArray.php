<?php
/**
 * @package plugins.thumbnail
 * @subpackage api.objects
 */
class KalturaColorArray extends KalturaTypedArray
{
	public function __construct()
	{
		return parent::__construct("KalturaColor");
	}

	public static function fromDbArray($arr, KalturaDetachedResponseProfile $responseProfile = null)
	{
		$newArr = new KalturaColorArray();
		foreach ($arr as $obj)
		{
			$newArr[] = $obj;
		}

		return $newArr;
	}
}
