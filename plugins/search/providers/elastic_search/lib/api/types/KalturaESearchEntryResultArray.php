<?php
/**
 * @package plugins.elasticSearch
 * @subpackage api.objects
 */
class KalturaESearchEntryResultArray extends KalturaESearchResultArray {
	
    public function __construct()
    {
        return parent::__construct("KalturaESearchEntryResult");
    }

	public static function fromDbArray($arr, KalturaDetachedResponseProfile $responseProfile = null)
	{
		$newArr = new KalturaESearchEntryResultArray();
		foreach ( $arr as $obj )
		{
			$nObj = new KalturaESearchEntryResult();
			$nObj->fromObject($obj, $responseProfile);
			$newArr[] = $nObj;
		}

		return $newArr;
	}

}
