<?php
/**
 * @package api
 * @subpackage objects
 */
class KalturaOperationAttributesArray extends KalturaTypedArray
{
	public static function fromOperationAttributesArray(array $arr)
	{
		$newArr = new KalturaOperationAttributesArray();
		if(is_null($arr))
			return $newArr;
			
		foreach($arr as $obj)
		{
			$nObj = new KalturaOperationAttributes();
			$nObj->fromObject($obj);
			$newArr[] = $nObj;
		}
		
		return $newArr;
	}
	
	public function __construct()
	{
		parent::__construct("KalturaOperationAttributes");	
	}
}