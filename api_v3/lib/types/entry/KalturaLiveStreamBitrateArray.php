<?php
/**
 * @package api
 * @subpackage objects
 */
class KalturaLiveStreamBitrateArray extends KalturaTypedArray
{
	public static function fromDbArray(array $arr, KalturaDetachedResponseProfile $responseProfile = null)
	{
		$newArr = new KalturaLiveStreamBitrateArray();
		if ($arr == null)
			return $newArr;
			
		foreach ($arr as $obj)
		{
			$nObj = new KalturaLiveStreamBitrate();
			$nObj->fromArray($obj);
			$newArr[] = $nObj;
		}
		
		return $newArr;
	}
		
	public function __construct()
	{
		parent::__construct("KalturaLiveStreamBitrate");	
	}
}
