<?php
/**
 * @package api
 * @subpackage objects
 */
class KalturaLiveStreamBitrateArray extends KalturaTypedArray
{
	public static function fromLiveStreamBitrateArray (array $arr )
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
		$ret = array();
		foreach($this as $nObj)
			$ret[] = $nObj->toObject();
			
		return $ret;
	}
		
	public function __construct()
	{
		parent::__construct("KalturaLiveStreamBitrate");	
	}
}
?>
