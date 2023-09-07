<?php
/**
 * @package api
 * @subpackage objects
 */
class KalturaOperationResourceArray extends KalturaTypedArray
{
	/**
	 * @param array<kOperationResource> $arr
	 * @return KalturaOperationResourceArray
	 */
	public static function fromDbArray(array $arr, KalturaDetachedResponseProfile $responseProfile = null)
	{
		$newArr = new KalturaOperationResourceArray();
		foreach($arr as $obj)
		{
			/**@var kOperationResource $obj*/
			$resourceObj = new KalturaFileSyncResource();
			$resourceObj->fromObject($obj->getResource(), $responseProfile);

			$nObj = new KalturaOperationResource();
			$nObj->fromObject($obj, $responseProfile);
			$nObj->resource = $resourceObj;
			$newArr[] = $nObj;
		}

		return $newArr;
	}

	public function __construct()
	{
		parent::__construct("KalturaOperationResource");
	}
}