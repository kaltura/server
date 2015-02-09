<?php
/**
 * @package api
 * @subpackage objects
 */
class KalturaSchedulerWorkerArray extends KalturaTypedArray
{
	public static function fromDbArray(array $arr, IResponseProfile $responseProfile = null)
	{
		$newArr = new KalturaSchedulerWorkerArray();
		foreach ( $arr as $obj )
		{
			$nObj = new KalturaSchedulerWorker();
			$nObj->fromObject($obj, $responseProfile);
			$newArr[] = $nObj;
		}
		
		return $newArr;
	}
	
	public static function statusFromSchedulerWorkerArray( $arr )
	{
		$newArr = new KalturaSchedulerWorkerArray();
		foreach ( $arr as $obj )
		{
			$nObj = new KalturaSchedulerWorker();
			$nObj->statusFromObject($obj);
			$newArr[] = $nObj;
		}
		
		return $newArr;
	}
	
	public function __construct( )
	{
		return parent::__construct ( "KalturaSchedulerWorker" );
	}
}
