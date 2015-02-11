<?php
/**
 * @package api
 * @subpackage objects
 */
class KalturaLiveStreamEntryArray extends KalturaTypedArray
{
	public static function fromDbArray($arr, KalturaResponseProfileBase $responseProfile = null)
	{
		$newArr = new KalturaLiveStreamEntryArray();
		if ($arr == null)
			return $newArr;		
		foreach ($arr as $obj)
		{
			$nObj = new KalturaLiveStreamEntry();
			$nObj->fromObject($obj, $responseProfile);
			$newArr[] = $nObj;
		}
		
		return $newArr;
	}
		
	public function __construct()
	{
		parent::__construct("KalturaLiveStreamEntry");	
	}
}