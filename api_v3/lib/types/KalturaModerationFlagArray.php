<?php
/**
 * @package api
 * @subpackage objects
 */
class KalturaModerationFlagArray extends KalturaTypedArray
{
	public static function fromDbArray(array $arr = null, KalturaResponseProfileBase $responseProfile = null)
	{
		$newArr = new KalturaModerationFlagArray();
		foreach($arr as $obj)
		{
			$nObj = new KalturaModerationFlag();
			$nObj->fromObject($obj, $responseProfile);
			$newArr[] = $nObj;
		}
		
		return $newArr;
	}
	
	public function __construct()
	{
		return parent::__construct("KalturaModerationFlag");
	}
}
