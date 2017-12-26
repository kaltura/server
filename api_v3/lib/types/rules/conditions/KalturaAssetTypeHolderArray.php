<?php
/**
 * @package api
 * @subpackage objects
 */
class KalturaAssetTypeHolderArray extends KalturaTypedArray
{
	public function __construct()
	{
		parent::__construct("KalturaAssetTypeHolder");
	}


	public static function fromDbArray($arr, KalturaDetachedResponseProfile $responseProfile = null)
	{
		$newArr = new KalturaAssetTypeHolderArray();
		if ($arr == null)
			return $newArr;

		foreach ($arr as $type)
		{
			$nObj = new KalturaAssetTypeHolder();
			$nObj->type = $type;
			$newArr[] = $nObj;
		}
		return $newArr;
	}
	
	


		

}