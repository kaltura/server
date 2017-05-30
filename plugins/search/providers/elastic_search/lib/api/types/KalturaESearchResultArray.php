<?php
/**
 * @package plugins.elasticSearch
 * @subpackage api.objects
 */
class KalturaESearchResultArray extends KalturaTypedArray {



    public function __construct()
    {
        return parent::__construct("KalturaESearchResult");
    }

	public static function fromDbArray($arr, KalturaDetachedResponseProfile $responseProfile = null)
	{
		$newArr = new KalturaESearchResultArray();
		foreach ( $arr as $obj )
		{
			$nObj = new KalturaESearchResult();
			$nObj->fromObject($obj, $responseProfile);
			$newArr[] = $nObj;
		}

		return $newArr;
	}

}
