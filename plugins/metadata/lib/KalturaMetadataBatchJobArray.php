<?php
/**
 * @package api
 * @subpackage objects
 */
class KalturaMetadataBatchJobArray extends KalturaBatchJobArray
{
	public static function fromBatchJobArray ( $arr )
	{
		$newArr = new KalturaMetadataBatchJobArray();
		if ( is_array ( $arr ) )
		{
			foreach ( $arr as $obj )
			{
				$nObj = new KalturaMetadataBatchJob();
				$nObj->fromObject($obj);
				$newArr[] = $nObj;
			}
		}
		
		return $newArr;
	}
	
	public function __construct( )
	{
		return parent::__construct ( "KalturaMetadataBatchJob" );
	}
}
?>