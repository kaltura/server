<?php
/**
 * @package plugins.elasticSearch
 * @subpackage api.objects
 */
class KalturaESearchAggregationResponseArray extends KalturaTypedArray
{
	public function __construct()
	{
		return parent::__construct("KalturaESearchAggregationResponseItem");
	}

	public static function fromDbArray($arr, KalturaDetachedResponseProfile $responseProfile = null)
	{
		$outputArray = new KalturaESearchAggregationResponseArray();
		foreach ( $arr as $obj )
		{
			$nObj = new KalturaESearchAggregationResponseItem();
			$nObj->fromObject($obj, $responseProfile);
			$outputArray[] = $nObj;
		}
		return $outputArray;
	}


}