<?php
/**
 * @package api
 * @subpackage objects
 */
class KalturaLiveStreamBitrateArray extends KalturaTypedArray
{
	public static function fromDbArray(array $arr)
	{
		$newArr = new KalturaLiveStreamBitrateArray();
		if ($arr == null)
			return $newArr;
			
		foreach ($arr as $obj)
		{
			$nObj = new KalturaLiveStreamBitrate();
			$nObj->fromObject($obj);
			$newArr[] = $nObj;
		}
		
		return $newArr;
	}
		
	public function toArray()
	{
		return $this->toObjectsArray();
	}
		
	public function __construct()
	{
		parent::__construct("KalturaLiveStreamBitrate");	
	}
}
