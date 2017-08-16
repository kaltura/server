<?php
/**
 * @package plugins.elasticSearch
 * @subpackage api.objects
 */
class KalturaESearchCategoryResultArray extends KalturaESearchResultArray
{
    public function __construct()
    {
        return parent::__construct("KalturaESearchCategoryResult");
    }

	public static function fromDbArray($arr, KalturaDetachedResponseProfile $responseProfile = null)
	{
		$newArr = new KalturaESearchCategoryResultArray();
		return parent::populateArray($newArr, 'KalturaESearchCategoryResult', $arr, $responseProfile);
	}
}
