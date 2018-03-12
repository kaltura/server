<?php
/**
 * @package plugins.reach
 * @subpackage api.objects
 */
class KalturaReachProfileArray extends KalturaTypedArray
{
	public static function fromDbArray($arr, KalturaDetachedResponseProfile $responseProfile = null)
	{
		$newArr = new KalturaReachProfileArray();
		if ($arr == null)
			return $newArr;
		
		foreach ($arr as $obj)
		{
			$object = new KalturaReachProfile();
			$object->fromObject($obj, $responseProfile);
			$newArr[] = $object;
		}
		
		return $newArr;
	}
	
	public function __construct()
	{
		parent::__construct("KalturaReachProfile");
	}
}