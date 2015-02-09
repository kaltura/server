<?php
/**
 * @package api
 * @subpackage objects
 */
class KalturaFlavorParamsOutputArray extends KalturaTypedArray
{
	public static function fromDbArray($arr, IResponseProfile $responseProfile = null)
	{
		$newArr = new KalturaFlavorParamsOutputArray();
		if ($arr == null)
			return $newArr;

		foreach ($arr as $obj)
		{
    		$nObj = new KalturaFlavorParamsOutput();
			$nObj->fromObject($obj, $responseProfile);
			$newArr[] = $nObj;
		}
		
		return $newArr;
	}
		
	public function __construct()
	{
		parent::__construct("KalturaFlavorParamsOutput");	
	}
}