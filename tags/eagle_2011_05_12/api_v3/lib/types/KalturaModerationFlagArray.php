<?php
/**
 * @package api
 * @subpackage objects
 */
class KalturaModerationFlagArray extends KalturaTypedArray
{
	public static function fromModerationFlagArray($arr)
	{
		$newArr = new KalturaModerationFlagArray();
		foreach($arr as $obj)
		{
			$nObj = new KalturaModerationFlag();
			$nObj->fromObject($obj);
			$newArr[] = $nObj;
		}
		
		return $newArr;
	}
	
	public function __construct()
	{
		return parent::__construct("KalturaModerationFlag");
	}
}
?>