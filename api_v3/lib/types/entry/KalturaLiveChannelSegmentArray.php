<?php
/**
 * @package api
 * @subpackage objects
 */
class KalturaLiveChannelSegmentArray extends KalturaTypedArray
{
	public static function fromEntryArray ( $arr )
	{
		$newArr = new KalturaLiveChannelSegmentArray();
		if ($arr == null)
			return $newArr;
			
		foreach ($arr as $obj)
		{
			$nObj = new KalturaLiveChannelSegment();
			$nObj->fromObject($obj);
			$newArr[] = $nObj;
		}
		
		return $newArr;
	}
		
	public function __construct()
	{
		parent::__construct("KalturaLiveChannelSegment");	
	}
}