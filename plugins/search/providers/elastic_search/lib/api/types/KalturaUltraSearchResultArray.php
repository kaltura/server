<?php
/**
 * @package plugins.elasticSearch
 * @subpackage api.objects
 */
class KalturaUltraSearchResultArray extends KalturaTypedArray {



    public function __construct()
    {
        return parent::__construct("KalturaUltraSearchResult");
    }

	public static function fromDbArray($arr, KalturaDetachedResponseProfile $responseProfile = null)
	{
		$newArr = new KalturaUltraSearchResultArray();
		foreach ( $arr as $obj )
		{
			$nObj = new KalturaUltraSearchResult();
			$nObj->fromObject($obj, $responseProfile);
			$newArr[] = $nObj;
		}

		return $newArr;
	}

}
