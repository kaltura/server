<?php
/**
 * @package api
 * @subpackage objects
 */
class KalturaBatchJobArray extends KalturaTypedArray
{
	public static function fromStatisticsBatchJobArray ( $arr )
	{
		$newArr = new KalturaBatchJobArray();
		if ( is_array ( $arr ) )
		{
			foreach ( $arr as $obj )
			{
				$nObj = new KalturaBatchJob();
				$nObj->fromStatisticsObject($obj);
				$newArr[] = $nObj;
			}
		}
		
		return $newArr;
	}
	
	public static function fromBatchJobArray ( $arr )
	{
		$newArr = new KalturaBatchJobArray();
		if ( is_array ( $arr ) )
		{
			foreach ( $arr as $obj )
			{
				$nObj = new KalturaBatchJob();
				$nObj->fromObject($obj);
				$newArr[] = $nObj;
			}
		}
		
		return $newArr;
	}
	
	public function __construct( )
	{
		return parent::__construct ( "KalturaBatchJob" );
	}
}
?>