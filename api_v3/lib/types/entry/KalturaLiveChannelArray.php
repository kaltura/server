<?php
/**
 * @package api
 * @subpackage objects
 */
class KalturaLiveChannelArray extends KalturaTypedArray
{
	public static function fromEntryArray ( $arr )
	{
		$newArr = new KalturaLiveChannelArray();
		if ($arr == null)
			return $newArr;
			
		foreach ($arr as $obj)
		{
			$nObj = new KalturaLiveChannel();
			$nObj->fromObject($obj);
			$newArr[] = $nObj;
		}
		
		return $newArr;
	}
		
	public function __construct()
	{
		parent::__construct("KalturaLiveChannel");	
	}
}