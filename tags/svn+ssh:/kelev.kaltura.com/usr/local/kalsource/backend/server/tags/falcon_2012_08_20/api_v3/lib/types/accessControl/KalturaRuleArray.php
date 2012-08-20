<?php
/**
 * @package api
 * @subpackage objects
 */
class KalturaRuleArray extends KalturaTypedArray
{
	public static function fromDbArray($arr)
	{
		KalturaLog::debug(print_r($arr, true));
		$newArr = new KalturaRuleArray();
		if ($arr == null)
			return $newArr;

		foreach ($arr as $obj)
		{
    		$nObj = new KalturaRule();
			$nObj->fromObject($obj);
			$newArr[] = $nObj;
		}
		
		return $newArr;
	}
		
	public function __construct()
	{
		parent::__construct("KalturaRule");	
	}
}