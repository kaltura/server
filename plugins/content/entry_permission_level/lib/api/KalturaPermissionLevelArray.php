<?php
/**
 * @package plugins.entryPermissionLevel
 * @subpackage api
 */
class KalturaPermissionLevelArray extends KalturaTypedArray
{
	public static function fromDbArray($arr, KalturaDetachedResponseProfile $responseProfile = null)
	{
		KalturaLog::debug(print_r($arr, true));
		$newArr = new KalturaPermissionLevelArray();
		if ($arr == null)
			return $newArr;

		foreach ($arr as $obj)
		{
    		$nObj = new KalturaPermissionLevel();
			$nObj->fromObject($obj, $responseProfile);
			$newArr[] = $nObj;
		}
		
		return $newArr;
	}
		
	public function __construct()
	{
		parent::__construct("KalturaPermissionLevel");
	}
}