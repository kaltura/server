<?php
/**
 * @package plugins.elasticSearch
 * @subpackage api.objects
 */
class KalturaESearchGroupResultArray extends KalturaTypedArray
{
	public function __construct()
	{
		return parent::__construct("KalturaESearchGroupResult");
	}

	public static function fromDbArray($arr, KalturaDetachedResponseProfile $responseProfile = null)
	{
		$outputArray = new KalturaESearchGroupResultArray();
		foreach ( $arr as $obj )
		{
			$nObj = new KalturaESearchGroupResult();
			$nObj->fromObject($obj, $responseProfile);
			$outputArray[] = $nObj;
		}
		return $outputArray;
	}

}
