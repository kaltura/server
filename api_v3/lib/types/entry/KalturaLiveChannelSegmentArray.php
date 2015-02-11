<?php
/**
 * @package api
 * @subpackage objects
 */
class KalturaLiveChannelSegmentArray extends KalturaTypedArray
{
	public static function fromDbArray($arr, KalturaResponseProfileBase $responseProfile = null)
	{
		$newArr = new KalturaLiveChannelSegmentArray();
		if ($arr == null)
			return $newArr;
			
		foreach ($arr as $obj)
		{
			$nObj = new KalturaLiveChannelSegment();
			$nObj->fromObject($obj, $responseProfile);
			$newArr[] = $nObj;
		}
		
		return $newArr;
	}
		
	public function __construct()
	{
		parent::__construct("KalturaLiveChannelSegment");	
	}
}