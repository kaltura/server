<?php
/**
 * @package plugins.elasticSearch
 * @subpackage api.objects
 */
class KalturaESearchEntryResultArray extends KalturaTypedArray
{
    public function __construct()
    {
        return parent::__construct("KalturaESearchEntryResult");
    }

	public static function fromDbArray($arr, KalturaDetachedResponseProfile $responseProfile = null)
	{
		$outputArray = new KalturaESearchEntryResultArray();
		foreach ( $arr as $obj )
		{
			$nObj = new KalturaESearchEntryResult();
			$nObj->fromObject($obj, $responseProfile);
			$outputArray[] = $nObj;
		}
		return $outputArray;
	}

}
