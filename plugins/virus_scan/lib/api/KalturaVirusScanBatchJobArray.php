<?php
/**
 * @package api
 * @subpackage objects
 */
class KalturaVirusScanBatchJobArray extends KalturaBatchJobArray
{
	public static function fromBatchJobArray ( $arr )
	{
		$newArr = new KalturaVirusScanBatchJobArray();
		if ( is_array ( $arr ) )
		{
			foreach ( $arr as $obj )
			{
				$nObj = new KalturaVirusScanBatchJob();
				$nObj->fromObject($obj);
				$newArr[] = $nObj;
			}
		}
		
		return $newArr;
	}
	
	public function __construct( )
	{
		return parent::__construct ( "KalturaVirusScanBatchJob" );
	}
}
?>