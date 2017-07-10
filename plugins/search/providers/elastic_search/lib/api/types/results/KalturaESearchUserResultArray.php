<?php
/**
 * @package plugins.elasticSearch
 * @subpackage api.objects
 */
class KalturaESearchUserResultArray extends KalturaESearchResultArray
{
    public function __construct()
    {
        return parent::__construct("KalturaESearchUserResult");
    }

	public static function fromDbArray($arr, KalturaDetachedResponseProfile $responseProfile = null)
	{
		$newArr = new KalturaESearchUserResultArray();
		return parent::populateArray($newArr, 'KalturaESearchUserResult', $arr, $responseProfile);
	}

}
