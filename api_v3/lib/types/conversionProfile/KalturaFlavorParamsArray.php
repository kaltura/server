<?php
/**
 * @package api
 * @subpackage objects
 */
class KalturaFlavorParamsArray extends KalturaTypedArray
{
	public static function fromDbArray($arr, KalturaResponseProfileBase $responseProfile = null)
	{
		$newArr = new KalturaFlavorParamsArray();
		if ($arr == null)
			return $newArr;

		foreach ($arr as $obj)
		{
			$nObj = KalturaFlavorParamsFactory::getFlavorParamsInstance($obj->getType());
			$nObj->fromObject($obj, $responseProfile);
			$newArr[] = $nObj;
		}
		
		return $newArr;
	}
		
	public function __construct()
	{
		parent::__construct("KalturaFlavorParams");	
	}
}