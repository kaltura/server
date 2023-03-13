<?php
/**
 * @package plugins.room
 * @subpackage api.objects
 */
class KalturaRoomEntryArray extends KalturaTypedArray
{

	public function __construct()
	{
		parent::__construct("KalturaRoomEntry");
	}

	public static function fromDbArray($arr, KalturaDetachedResponseProfile $responseProfile = null)
	{
		$newArr = new KalturaRoomEntryArray();
		if ($arr == null)
		{
			return $newArr;
		}

		foreach($arr as $obj)
		{
			$nObj = KalturaEntryFactory::getInstanceByType($obj->getType());
			$nObj->fromObject($obj, $responseProfile);
			$newArr[] = $nObj;
		}

		return $newArr;
	}

}