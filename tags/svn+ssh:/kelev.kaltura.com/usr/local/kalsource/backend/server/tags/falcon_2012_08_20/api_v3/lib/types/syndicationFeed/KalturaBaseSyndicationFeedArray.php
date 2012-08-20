<?php
/**
 * @package api
 * @subpackage objects
 */
class KalturaBaseSyndicationFeedArray extends KalturaTypedArray
{
	public static function fromSyndicationFeedArray ( $arr )
	{
		$newArr = new KalturaBaseSyndicationFeedArray();
		if ( $arr == null ) return $newArr;
		foreach ( $arr as $obj )
		{
			$nObj = KalturaSyndicationFeedFactory::getInstanceByType($obj->getType());
			$nObj->fromObject(  $obj );
			$newArr[] = $nObj;
		}
		
		return $newArr;
	}
		
	public function __construct()
	{
		parent::__construct("KalturaBaseSyndicationFeed");	
	}
}