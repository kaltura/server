<?php
/**
 * @package api
 * @subpackage objects
 */
class KalturaSchedulerArray extends KalturaTypedArray
{
	public static function fromSchedulerArray( $arr )
	{
		$newArr = new KalturaSchedulerArray();
		foreach ( $arr as $obj )
		{
			$nObj = new KalturaScheduler();
			$nObj->fromObject($obj);
			$newArr[] = $nObj;
		}
		
		return $newArr;
	}
	
	public static function statusFromSchedulerArray( $arr )
	{
		$newArr = new KalturaSchedulerArray();
		foreach ( $arr as $obj )
		{
			$nObj = new KalturaScheduler();
			$nObj->statusFromObject($obj);
			$newArr[] = $nObj;
		}
		
		return $newArr;
	}
	
	public function __construct( )
	{
		return parent::__construct ( "KalturaScheduler" );
	}
}
?>