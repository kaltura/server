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
		return parent::populateArray($newArr, 'KalturaESearchEntryResult', $arr, $responseProfile);
	}

}
