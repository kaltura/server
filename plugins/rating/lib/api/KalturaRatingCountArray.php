<?php
/**
 * @package plugins.rating
 * @subpackage api.objects
 */
class KalturaRatingCountArray extends KalturaTypedArray
{
	public static function fromDbArray($arr, KalturaDetachedResponseProfile $responseProfile = null)
	{
		$newArr = new KalturaRatingCountArray();
		if ($arr == null)
			return $newArr;
		
		foreach ($arr as $obj)
		{
			$nObj = new KalturaRatingCount();
			$nObj->fromObject($obj, $responseProfile);
			$newArr[] = $nObj;
		}
		
		return $newArr;
	}
	
	public function __construct()
	{
		parent::__construct("KalturaRatingCount");
	}
}
