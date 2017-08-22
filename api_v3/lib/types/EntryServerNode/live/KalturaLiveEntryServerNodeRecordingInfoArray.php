<?php
/**
 * @package api
 * @subpackage objects
 */
class KalturaLiveEntryServerNodeRecordingInfoArray extends KalturaTypedArray
{
	public static function fromDbArray($arr, KalturaDetachedResponseProfile $responseProfile = null)
	{
		$newArr = new KalturaLiveEntryServerNodeRecordingInfoArray();
		if ($arr == null)
			return $newArr;
	
		foreach ($arr as $obj)
		{
			$nObj = new KalturaLiveEntryServerNodeRecordingInfo();
			$nObj->fromObject($obj, $responseProfile);
			$newArr[] = $nObj;
		}
	
		return $newArr;
	}
	
	public function __construct()
	{
		return parent::__construct("KalturaLiveEntryServerNodeRecordingInfo");
	}
}