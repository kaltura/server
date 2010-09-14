<?php
/**
 * @package api
 * @subpackage objects
 */
class KalturaFileSyncImportBatchJobArray extends KalturaBatchJobArray
{
	public static function fromBatchJobArray ( $arr )
	{
		$newArr = new KalturaFileSyncImportBatchJobArray();
		if ( is_array ( $arr ) )
		{
			foreach ( $arr as $obj )
			{
				$nObj = new KalturaFileSyncImportBatchJob();
				$nObj->fromObject($obj);
				$newArr[] = $nObj;
			}
		}
		
		return $newArr;
	}
	
	public function __construct( )
	{
		return parent::__construct ( "KalturaFileSyncImportBatchJob" );
	}
}
?>