<?php
/**
 * @package api
 * @subpackage objects
 */
class KalturaBaseSyndicationFeedArray extends KalturaTypedArray
{
	public static function fromDbArray(array $arr, IResponseProfile $responseProfile = null)
	{
		$newArr = new KalturaBaseSyndicationFeedArray();
		if ( $arr == null ) return $newArr;
		foreach ( $arr as $obj )
		{
			$nObj = KalturaSyndicationFeedFactory::getInstanceByType($obj->getType());
			$nObj->fromObject($obj, $responseProfile);
			$newArr[] = $nObj;
		}
		
		return $newArr;
	}
		
	public function __construct()
	{
		parent::__construct("KalturaBaseSyndicationFeed");	
	}
}