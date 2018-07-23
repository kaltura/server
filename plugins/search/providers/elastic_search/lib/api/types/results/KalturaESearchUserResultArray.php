<?php
/**
 * @package plugins.elasticSearch
 * @subpackage api.objects
 */
class KalturaESearchUserResultArray extends KalturaTypedArray
{
    public function __construct()
    {
        return parent::__construct("KalturaESearchUserResult");
    }

	public static function fromDbArray($arr, KalturaDetachedResponseProfile $responseProfile = null)
	{
		$outputArray = new KalturaESearchUserResultArray();
		foreach ( $arr as $obj )
		{
			$nObj = new KalturaESearchUserResult();
			$nObj->fromObject($obj, $responseProfile);
			$outputArray[] = $nObj;
		}
		return $outputArray;
	}

}
