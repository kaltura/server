<?php
/**
 * @package plugins.elasticSearch
 * @subpackage api.objects
 */
class KalturaESearchCategoryResultArray extends KalturaTypedArray
{
    public function __construct()
    {
        return parent::__construct("KalturaESearchCategoryResult");
    }

	public static function fromDbArray($arr, KalturaDetachedResponseProfile $responseProfile = null)
	{
		$outputArray = new KalturaESearchCategoryResultArray();
		foreach ( $arr as $obj )
		{
			$nObj = new KalturaESearchCategoryResult();
			$nObj->fromObject($obj, $responseProfile);
			$outputArray[] = $nObj;
		}
		return $outputArray;
	}
}
