<?php
/**
 * @package api
 * @subpackage objects
 */
class KalturaLiveChannelArray extends KalturaTypedArray
{
	public static function fromDbArray($arr, KalturaResponseProfileBase $responseProfile = null)
	{
		$newArr = new KalturaLiveChannelArray();
		if ($arr == null)
			return $newArr;
			
		foreach ($arr as $obj)
		{
			$nObj = new KalturaLiveChannel();
			$nObj->fromObject($obj, $responseProfile);
			$newArr[] = $nObj;
		}
		
		return $newArr;
	}
		
	public function __construct()
	{
		parent::__construct("KalturaLiveChannel");	
	}
}