<?php
/**
 * @package api
 * @subpackage objects
 */
class KalturaOperationAttributesArray extends KalturaTypedArray
{
	public static function fromDbArray(array $arr = null, KalturaResponseProfileBase $responseProfile = null)
	{
		$newArr = new KalturaOperationAttributesArray();
		if(is_null($arr))
			return $newArr;
			
		foreach($arr as $obj)
		{
			$class = $obj->getApiType();
			$nObj = new $class();
			$nObj->fromObject($obj, $responseProfile);
			$newArr[] = $nObj;
		}
		
		return $newArr;
	}
	
	public function __construct()
	{
		parent::__construct("KalturaOperationAttributes");	
	}
}